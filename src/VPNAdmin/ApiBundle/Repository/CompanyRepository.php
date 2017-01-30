<?php

namespace VPNAdmin\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CompanyRepository extends EntityRepository
{
    const DEFAULT_SORTBY = 'name';
    const DEFAULT_SORTDIR = 'ASC';

    protected static $sortBy = array('name', 'quota');

    public function findAll($sortBy = null, $sortDir = null)
    {
        $orderBy = array(
            $this->sortBy($sortBy) => $this->sortDir($sortDir)
        );
        return $this->findBy(array(), $orderBy);
    }

    /**
     * Find a companies that exceeded limit over the given period.
     * 
     * @param \DateTime $from The period date from.
     * @param \DateTime $to The period date to.
     */
    public function findExceededLimit(\DateTime $from, \DateTime $to)
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT c.id, c.name, c.quota, SUM(t.transferred) AS used'
            . ' FROM VPNAdminApiBundle:Transfer t'
            . ' JOIN t.user u'
            . ' JOIN u.company c'
            . ' WHERE t.created BETWEEN :from AND :to'
            . ' GROUP BY c.id'
            . ' HAVING used > c.quota'
            . ' ORDER BY used DESC'
        );
        $query->setParameter('from', $from->format('Y-m-d H:i:s'));
        $query->setParameter('to', $to->format('Y-m-d H:i:s'));
        return $query->getResult();
    }

    protected function sortBy($field)
    {
        $field = is_string($field) ? strtolower($field) : self::DEFAULT_SORTBY;
        if (in_array($field, self::$sortBy)) {
            return $field;
        }
        return self::DEFAULT_SORTBY;
    }

    protected function sortDir($dir)
    {
        $dir = is_string($dir) ? strtoupper($dir) : self::DEFAULT_SORTDIR;
        if ($dir === 'ASC' || $dir === 'DESC') {
            return $dir;
        }
        return self::DEFAULT_SORTDIR;
    }
}
