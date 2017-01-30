<?php

namespace VPNAdmin\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReportController extends FOSRestController
{
    /**
     * Get companies tranfers report.
     * 
     * @Get("/report/{year}/{month}")
     * @param string $year The year number(yyyy).
     * @param string $month The month number(mm).
     * @return View The View instance.
     */
    public function getReportAction($year, $month)
    {
        $from = \DateTime::createFromFormat(
            'Y-m-d H:i:s',
            "$year-$month-01 00:00:00",
            new \DateTimeZone('UTC')
        );
        if ($from === false) {
            throw new HttpException(400, 'Invalid date.');
        }
        $to = clone $from;
        $to->modify('last day of this month 23:59:59');

        $companies = $this->getDoctrine()
            ->getRepository('VPNAdminApiBundle:Company')
            ->findExceededLimit($from, $to);
        return $this->view($companies, 200);
    }

    /**
     * Generate example data for the last 6 month.
     * 
     * @Post("/generate")
     * @return View The View instance.
     */
    public function generateDataAction()
    {
        $this->clearData();
        $companies = $this->generateCompanies();
        $users = $this->generateUsers($companies);
        $this->generateTransfers($users);
        return $this->view(array('message' => 'Data generated.'), 200);
    }

    /**
     * Delete transfers
     * 
     * @throws \Exception
     */
    protected function clearData()
    {
        $tables = array('Transfer', 'User', 'Company');
        foreach ($tables as $table) {
            if (!$this->clearTable('VPNAdmin\\ApiBundle\\Entity\\' . $table)) {
                throw new \Exception("Failed to clear {$table} data.");
            }
        }
    }

    /**
     * Delete table rows
     * 
     * @param string $className
     * @return boolean
     */
    protected function clearTable($className)
    {
        $em = $this->getDoctrine()->getManager();
        $cmd = $em->getClassMetadata($className);
        $connection = $em->getConnection();
        $connection->beginTransaction();

        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $connection->query('DELETE FROM '.$cmd->getTableName());
            // Beware of ALTER TABLE here--it's another DDL statement and will cause
            // an implicit commit.
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
            return true;
        } catch (\Exception $e) {
            $connection->rollback();
        }
        return false;
    }

    protected function generateCompanies($min = 5, $max = 10)
    {
        $generator = $this->get('data.generator');
        $companies = $generator->generateCompanies($min, $max);
        $em = $this->getDoctrine()->getManager();
        foreach ($companies as $company) {
            $em->persist($company);
        }
        $em->flush();
        return $companies;
    }

    protected function generateUsers(array $companies, $min = 5, $max = 50)
    {
        $generator = $this->get('data.generator');
        $users = $generator->generateUsers($companies, $min, $max);
        $em = $this->getDoctrine()->getManager();
        foreach ($users as $user) {
            $em->persist($user);
        }
        $em->flush();
        return $users;
    }

    protected function generateTransfers($users)
    {
        $generator = $this->get('data.generator');
        $transfers = $generator->generateTransfers($users);
        $em = $this->getDoctrine()->getManager();
        foreach ($transfers as $transfer) {
            $em->persist($transfer);
        }
        $em->flush();
        return $transfers;
    }
}
