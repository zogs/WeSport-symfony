<?php

namespace My\FlashBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MyFlashBundle:Default:index.html.twig', array('name' => $name));
    }

    public function testAction()
    {
    	$this->get('flashbag')->add('success','Le message de succès est bien affiché !');
    	$this->get('flashbag')->add('error',"Le message d'erreur apparait aussi !");
    	$this->get('flashbag')->add('warning',"Attention au message warning ...");
    	$this->get('flashbag')->add('info',"Ah non tout va bien !");

    	return $this->render('MyFlashBundle:Default:test.html.twig');
    }

    
}
