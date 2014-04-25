<?php

namespace My\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function viewAction($id){

    	$em = $this->getDoctrine()->getManager();

        $page = $em->getRepository('MyPageBundle:Page')->find($id);

    	return $this->render('MyPageBundle:Default:page_view.html.twig',array('page'=>$page));
    }

    public function showMenuAction(){


    	$em = $this->getDoctrine()->getManager();

        $pages = $em->getRepository('MyPageBundle:Page')->findAll();

    	return $this->render('MyPageBundle:Default:page_menu.html.twig',array(
    		'menuPages'=> $pages
    		)
    	);
    }

    
}
