<?php

namespace My\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MenuController extends Controller
{

    public function TopMenuAction(){


    	$em = $this->getDoctrine()->getManager();

        $pages = $em->getRepository('MyPageBundle:Page')->findByMenu('top');

    	return $this->render('MyPageBundle:Menu:top.html.twig',array(
    		'menuPages'=> $pages
    		)
    	);
    }

    public function BottomMenuAction(){


        $em = $this->getDoctrine()->getManager();

        $pages = $em->getRepository('MyPageBundle:Page')->findByMenu('bottom');

        return $this->render('MyPageBundle:Menu:bottom.html.twig',array(
            'menuPages'=> $pages
            )
        );
    }
    
}
