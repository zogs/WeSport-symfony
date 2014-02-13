<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Entity\Serie;
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

            $fields = $request->request->get('event');
            $location = $this->get('world.location_manager')->getLocationFromCityArray($fields['location']);        

            $event = $form->getData();
            $event->setLocation($location);
            $event->setOrganizer($this->getUser());
            

            if($this->get('ws_events.manager')->saveSerie($event)){

                $this->get('session')->getFlashBag()->add('success','formulaire valide');
            }
            else {
                $this->get('session')->getFlashBag()->add('error','peut pas sauvegarder !');
            }


    	}
        

    	return $this->render('WsEventsBundle:Default:new.html.twig', array(
    		'form' => $form->createView(),
    		));
    }
}
