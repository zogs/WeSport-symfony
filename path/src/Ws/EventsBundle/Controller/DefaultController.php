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

            if(!empty($data['location']['city_id']))
                $location = $this->getDoctrine()->getRepository('MyWorldBundle:Location')->findLocationByCityId($data['location']['city_id']);
            elseif(!empty($data['location']['city_name'])){
                try{
                    $city = $this->getDoctrine()->getRepository('MyWorldBundle:City')->findCityByName($data['location']['city_name']);                    
                    $location = $this->getDoctrine()->getRepository('MyWorldBundle:Location')->findLocationByCityId($city->getId());
                }
                catch(NoResultException $e){
                    $this->get('session')->getFlashBag()->add('error','Cette ville nexiste pas');
                }
            }
            $event = $form->getData();
            //print('<pre>');
            //print_r($event);
            //print('</pre>');
            //exit();

            $event->setLocation($location);

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
