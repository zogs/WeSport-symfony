<?php

namespace My\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MenuController extends Controller
{

    public function TopMenuAction(){

        return $this->getMenuAction('top','top',3);
    }

    public function BottomMenuAction(){

        return $this->getMenuAction('bottom','bottom',10);
       
    }
    
    public function getMenuAction($menu = 'top',$template='top',$max=10)
    {
        $pages = $this->getDoctrine()->getManager()->getRepository('MyPageBundle:Page')->findByMenu($menu,$max);

        return $this->render('MyPageBundle:Menu:'.$template.'.html.twig',array(
            'pages' => $pages
            ));
    }
}
