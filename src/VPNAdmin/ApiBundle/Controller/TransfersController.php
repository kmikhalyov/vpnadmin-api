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

/**
 * @RouteResource("Transfer")
 */
class TransfersController extends FOSRestController
{
    /**
     * Get the list of transfers.
     * 
     * @QueryParam(name="sortBy", requirements="(created|transferred)", default="created")
     * @QueryParam(name="sortDir", requirements="(asc|desc)", default="desc")
     * @QueryParam(name="offset", requirements="\d+", nullable=true)
     * @QueryParam(name="limit", requirements="\d+", nullable=true)
     * @ViewAnnotation()
     * @param ParamFetcher $paramFetcher The ParamFetcher instance.
     * @return View The View instance.
     */
    public function cgetAction(ParamFetcher $paramFetcher)
    {
        $sortBy = $paramFetcher->get('sortBy');
        $sortDir = $paramFetcher->get('sortDir');
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        
        $transfers = $this->getDoctrine()
            ->getRepository('VPNAdminApiBundle:Transfer')
            ->findby(
                array(),
                array($sortBy => $sortDir),
                $limit > 0 ? (int)$limit : null,
                $offset > 0 ? (int)$offset : null
            );

        $view = $this->view($transfers, 200);
        $context = new Context();
        $context->addGroup('list');
        $view->setContext($context);
        return $view;
    }
}
