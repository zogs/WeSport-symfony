<?php

namespace Ws\SportsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('WsSportsBundle:Default:index.html.twig', array('name' => $name));
    }

    public function autocompleteAction($prefix)
    {
    	if(!isset($prefix) || strlen($prefix)<3 ) throw $this->createNotFoundException('Search string must be 3 caracters at least');

    	$sports = $this->getDoctrine()->getManager()->getRepository('WsSportsBundle:Sport')->autocomplete($prefix);

    	foreach ($sports as $k => $sport) {
			
			$sports[$k] = array();
			$sports[$k]['name'] = $sport->getName();
			$sports[$k]['id'] = $sport->getId();
			$sports[$k]['value'] = $sport->getId();
            $sports[$k]['icon'] = $sport->getIcon();
			$sports[$k]['category'] = $sport->getCategory()->getName();
    	}

    	return new JsonResponse($sports);

    }
}
