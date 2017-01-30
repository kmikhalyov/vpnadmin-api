<?php

namespace VPNAdmin\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View AS ViewAnnotation;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use VPNAdmin\ApiBundle\Entity\User;
use VPNAdmin\ApiBundle\Form\UserType;

/**
 * @RouteResource("User")
 */
class UsersController extends FOSRestController
{
    /**
     * Get the list of users.
     * 
     * @QueryParam(
     *     name="sortBy", requirements="(name|email)",
     *     default="name", description="Sort field"
     * )
     * @QueryParam(
     *     name="sortDir", requirements="(asc|desc)",
     *     default="asc", description="Sort direction"
     * )
     * @ViewAnnotation()
     * @param ParamFetcher $paramFetcher The ParamFetcher instance.
     * @return View The View instance.
     */
    public function cgetAction(ParamFetcher $paramFetcher)
    {
        // Get query params.
        $sortBy = $paramFetcher->get('sortBy');
        $sortDir = $paramFetcher->get('sortDir');
        // Fetch users
        $users = $this->getDoctrine()
            ->getRepository('VPNAdminApiBundle:User')
            ->findAll($sortBy, $sortDir);
        // Make response
        $view = $this->view($users, 200);
        $context = new Context();
        $context->addGroup('details');
        $view->setContext($context);
        return $view;
    }

    /**
     * Get the user.
     * 
     * @ViewAnnotation()
     * @param int $id The user ID.
     * @return View The View instance.
     * @throws ResourceNotFoundException
     */
    public function getAction($id)
    {
        $user = $this->getCompanyUser($id);
        $view = $this->view($user, 200);
        $context = new Context();
        $context->addGroup('full');
        $view->setContext($context);
        return $view;
    }

    /**
     * Create a new user.
     * 
     * @ViewAnnotation()
     * @param Request $request The Request instance.
     * @return View The View instance.
     */
    public function cpostAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $company = $this->getDoctrine()
                ->getRepository('VPNAdminApiBundle:Company')
                ->find($user->getCompanyId());
            if (!$company) {
                $error = new FormError('Company not found.');
                $form->get('company_id')->addError($error);
                return $this->view($form->getErrors(true), 400);
            }
            $user->setCompany($company);
            // Add date fields.
            $utc = new \DateTime('', new \DateTimeZone('UTC'));
            $user->setCreated($utc);
            $user->setModified($utc);
            // Save user.
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            // Make response.
            $view = $this->view(array('id' => $user->getId()), 201);
            return $view;
        }
        return $this->view($form, 400);
    }

    /**
     * Edit a user.
     * 
     * @ViewAnnotation()
     * @param int $id The user ID.
     * @param Request $request The HTTP Request instance.
     * @return View The View instance.
     * @throws ResourceNotFoundException
     */
    public function cputAction($id, Request $request)
    {
        $user = $this->getCompanyUser($id);
        $options = array('method' => 'PUT');
        $form = $this->createForm(UserType::class, $user, $options);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $company = $this->getDoctrine()
                ->getRepository('VPNAdminApiBundle:Company')
                ->find($user->getCompanyId());
            if (!$company) {
                $error = new FormError('Company not found.');
                $form->get('company_id')->addError($error);
                return $this->view($form->getErrors(true), 400);
            }
            $user->setCompany($company);
            // Add date fields.
            $gmt = new \DateTime('', new \DateTimeZone('UTC'));
            $user->setModified($gmt);
            // Update user.
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            // Make response.
            $view = $this->view(array('message' => sprintf('User %d updated.', $id)), 202);
            return $view;
        }
        return $this->view($form, 400);
    }

    /**
     * Delete a user.
     * 
     * @ViewAnnotation()
     * @param int $id The user ID.
     * @return View The View instance.
     * @throws ResourceNotFoundException
     */
    public function cdeleteAction($id)
    {
        // Find user
        $user = $this->getCompanyUser($id);
        // User exists, delete
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        // Make response.
        $view = $this->view(array('message' => sprintf('User %d deleted.', $id)), 202);
        return $view;
    }

    /**
     * Fetch the company user by ID.
     * 
     * @param int $id The company user ID.
     * @return User The User instance.
     * @throws ResourceNotFoundException
     */
    protected function getCompanyUser($id)
    {
        $user = $this->getDoctrine()
            ->getRepository('VPNAdminApiBundle:User')
            ->find($id);
        if (!$user) {
            $error = sprintf('User with id "%d" not found.', $id);
            throw new ResourceNotFoundException($error);
        }
        return $user;
    }
}
