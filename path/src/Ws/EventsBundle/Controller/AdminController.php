<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Entity\Serie;
use Ws\EventsBundle\Form\Type\EventType;


class AdminController extends Controller
{
    public function indexAction()
    {

    	$series = $this->getDoctrine()->getEntityManager()->getRepository('WsEventsBundle:Serie')->findRecentSeriePosted();

        return $this->render('WsEventsBundle:Admin:index.html.twig', array('series' => $series));
    }

}