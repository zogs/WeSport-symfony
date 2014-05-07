<?php

namespace My\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function viewAction($id){

    	$em = $this->getDoctrine()->getManager();

        $page = $em->getRepository('MyPageBundle:Page')->find($id);

    	return $this->render('MyPageBundle:Page:view.html.twig',array('page'=>$page));
    }
    
}
