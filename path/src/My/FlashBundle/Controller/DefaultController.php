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
    	$this->get('flashbag')->add('Le message de succès est bien affiché !','success');
    	$this->get('flashbag')->add("Le message d'erreur apparait aussi !",'error');
    	$this->get('flashbag')->add("Attention au message warning ...",'warning');
    	$this->get('flashbag')->add("Ah non tout va bien !",'info');

    	return $this->render('MyFlashBundle:Default:test.html.twig');
    }

    
}
