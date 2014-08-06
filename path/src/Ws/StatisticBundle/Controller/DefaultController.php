<?php

namespace Ws\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('WsStatisticBundle:Default:index.html.twig', array('name' => $name));
    }

    public function updateAction($scope)
    {
    	$stats = $this->get('statistic.manager')->update($scope);
    	
    	return $this->render('WsStatisticBundle:Default:print.html.twig', array('stats' => $stats));
    }

    public function showAction($scope,$id)
    {
    	$stat = $this->get('statistic.manager')->setContext($scope,$id)->get();

    	return $this->render('WsStatisticBundle:Default:index.html.twig', array('name' => 'test'));
    }
}
