<?php

namespace Ws\MailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('WsMailerBundle:Default:index.html.twig', array('name' => $name));
    }
}
