<?php

namespace My\UtilsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MyUtilsBundle:Default:index.html.twig', array('name' => $name));
    }
}
