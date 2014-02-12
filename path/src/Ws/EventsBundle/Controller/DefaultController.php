<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Form\Type\EventType;


class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('WsEventsBundle:Default:index.html.twig', array('name' => $name));
    }

    public function newAction(Request $request)
    {

        $event = new Event();

        $form = $this->createForm('event',$event);

        $form->handleRequest($request);

    	if($form->isValid()){

            $data = $request->request->get('event');

            $location = $this->get('world.location_manager')->getLocationFromCityArray($data['location']);        

            $event = $form->getData();
            $event->setLocation($location);
            $event->setOrganizer($this->getUser());
            

            var_dump($event);
            exit();


            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();


            $this->get('session')->getFlashBag()->add('success','formulaire valide');
    	}
        

    	return $this->render('WsEventsBundle:Default:new.html.twig', array(
    		'form' => $form->createView(),
    		));
    }
}
