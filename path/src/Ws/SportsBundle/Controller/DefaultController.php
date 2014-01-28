<?php

namespace Ws\SportsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('WsSportsBundle:Default:index.html.twig', array('name' => $name));
    }
}
