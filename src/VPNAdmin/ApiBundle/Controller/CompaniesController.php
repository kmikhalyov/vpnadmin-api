<?php

namespace VPNAdmin\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View AS ViewAnnotation;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use VPNAdmin\ApiBundle\Entity\Company;
use VPNAdmin\ApiBundle\Form\CompanyType;

/**
 * @RouteResource("Company")
 */
class CompaniesController extends FOSRestController
{
    /**
     * Get the list of companies.
     * 
     * @QueryParam(
     *     name="sortBy", requirements="(name|quota)",
     *     default="name", description="Sort field"
     * )
     * @QueryParam(
     *     name="sortDir", requirements="(asc|desc)",
     *     default="asc", description="Sort direction"
     * )
     * @ViewAnnotation()
     * @param ParamFetcher $paramFetcher The ParamFetcher instance.
     * return View
     */
    public function cgetAction(ParamFetcher $paramFetcher)
    {
        $sortBy = $paramFetcher->get('sortBy');
        $sortDir = $paramFetcher->get('sortDir');
        $companies = $this->getDoctrine()
            ->getRepository('VPNAdminApiBundle:Company')
            ->findAll($sortBy, $sortDir);

        $view = $this->view($companies, 200);
        $context = new Context();
        $context->addGroup('list');
        $view->setContext($context);
        return $view;
    }

    /**
     * Get the company.
     * 
     * @ViewAnnotation()
     * @param int $id The company ID.
     * @return View
     * @throws ResourceNotFoundException
     */
    public function getAction($id)
    {
        $company = $this->getCompany($id);
        $view = $this->view($company, 200);
        $context = new Context();
        $context->addGroup('details');
        $view->setContext($context);
        return $view;
    }

    /**
     * Create a new company.
     * 
     * @ViewAnnotation()
     * @param Request $request The Request instance.
     * @return View
     */
    public function cpostAction(Request $request)
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Add date fields.
            $utc = new \DateTime('', new \DateTimeZone('UTC'));
            $company->setCreated($utc);
            $company->setModified($utc);
            // Save company.
            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();
            // Make response.
            $view = $this->view(array('id' => $company->getId()), 201);
            return $view;
        }
        return $this->view($form, 400);
    }

    /**
     * Edit company.
     * 
     * @ViewAnnotation()
     * @param int $id The company ID.
     * @param Request $request The HTTP Request instance.
     * @return View
     * @throws ResourceNotFoundException
     */
    public function cputAction($id, Request $request)
    {
        $company = $this->getCompany($id);
        $options = array('method' => 'PUT');
        $form = $this->createForm(CompanyType::class, $company, $options);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Add date fields.
            $gmt = new \DateTime('', new \DateTimeZone('UTC'));
            $company->setModified($gmt);
            // Update company.
            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();
            // Make response.
            $view = $this->view(array('message' => 'Company updated.'), 202);
            return $view;
        }
        return $this->view($form, 400);
    }

    /**
     * Delete company.
     * 
     * @ViewAnnotation()
     * @param int $id The company ID.
     * @return View The view instance.
     * @throws ResourceNotFoundException
     */
    public function cdeleteAction($id)
    {
        $company = $this->getCompany($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($company);
        $em->flush();
        // Make response.
        $view = $this->view(array('message' => 'Company deleted.'), 202);
        return $view;
    }

    /**
     * Fetch company by ID.
     * 
     * @param int $id The company ID.
     * @return Company The company instance.
     * @throws ResourceNotFoundException
     */
    protected function getCompany($id)
    {
        $company = $this->getDoctrine()
            ->getRepository('VPNAdminApiBundle:Company')
            ->find($id);
        if (!$company) {
            throw new ResourceNotFoundException('Company not found.');
        }
        return $company;
    }
}
