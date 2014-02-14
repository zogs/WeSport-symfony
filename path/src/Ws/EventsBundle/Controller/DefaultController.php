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

    /**
     * Get and manage the creation form
     *
     * @param request
     *
     * @return View
     */
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
            

            if($this->get('ws_events.manager')->saveAll($event)){

                $this->get('flashbag')->add('success','formulaire valide');
            }
            else {
                $this->get('flashbag')->add('error','peut pas sauvegarder !');
            }


    	}
        

    	return $this->render('WsEventsBundle:Default:new.html.twig', array(
    		'form' => $form->createView(),
    		));
    }



    public function confirmAction(Event $event, $token)
    {
        if (!$this->get('form.csrf_provider')->isCsrfTokenValid('delete_event', $token)) {
            throw new AccessDeniedHttpException('Invalid CSRF token.');
        }

        if($this->getUser()!=$event->getOrganizer()) {
            throw $this->createNotFoundException('Vous ne pouvez pas supprimer cet événement');        
        } 

        $this->get('ws_events.manager')->confirmEvent($event);
        $this->get('flashbag')->add('success',"L'événement a été confirmé !");

        $this->redirect($this->generateUrl("ws_events_new")); 
    }


    /**
     * Delete event
     *
     * @param event
     * @param token
     *
     * @return redirect
     */
    public function deleteAction(Event $event,$token)
    {
        if (!$this->get('form.csrf_provider')->isCsrfTokenValid('delete_event', $token)) {
            throw new AccessDeniedHttpException('Invalid CSRF token.');
        }

        if($this->getUser()!=$event->getOrganizer()) {
            throw $this->createNotFoundException('Vous ne pouvez pas supprimer cet événement');        
        }   

        $this->get('ws_events.manager')->deleteEvent($event);
        $this->get('flashbag')->add('success',"L'événement a été supprimé !");

        $this->redirect($this->generateUrl("ws_events_new"));     
    }


    /**
     * Delete a serie 
     *
     * @param event
     * @param token
     *
     * @return Redirect
     */
    public function deleteSerieAction(Event $event,$token)
    {
        if (!$this->get('form.csrf_provider')->isCsrfTokenValid('delete_event', $token)) {
            throw new AccessDeniedHttpException('Invalid CSRF token.');
        }

        if($this->getUser()!=$event->getOrganizer()) {
            throw $this->createNotFoundException('Vous ne pouvez pas supprimer cette série');        
        }   

        $this->get('ws_events.manager')->deleteSerie($event);
        $this->get('flashbag')->add('success',"Tous les événements ont été supprimés !");

        $this->redirect($this->generateUrl("ws_events_new")); 

    }


    /**
     * Delete a serie 
     *
     * @param event
     *
     * @return View
     */
    public function viewAction(Event $event)
    {
        return $this->render('WsEventsBundle:Default:view.html.twig',array(
            'event'=>$event,
            'token'=>$this->get('form.csrf_provider')->generateCsrfToken('delete_event')
            )
        );
    }


    /**
     * Add a participant
     *
     * @param event
     *
     * @return View
     */
    public function addParticipationAction(Event $event,$token)
    {
        if (!$this->get('form.csrf_provider')->isCsrfTokenValid('delete_event', $token)) {
            throw new AccessDeniedHttpException('Invalid CSRF token.');
        }

        $this->get('ws_events.manager')->saveParticipation($event,$this->getUser(),true);
        $this->get('flashbag')->add('success',"Merci de votre participation !");

        $this->redirect($this->generateUrl(
            'ws_events_view',array(
                'sport'=>$event->getSport(),
                'slug'=>$event->getSlug(),
                'event'=>$event->getId()
                )
            )
        );
    }

    /**
     * remove a participant
     *
     * @param event
     *
     * @return View
     */
    public function removeParticipationAction(Event $event,$token)
    {
        if (!$this->get('form.csrf_provider')->isCsrfTokenValid('delete_event', $token)) {
            throw new AccessDeniedHttpException('Invalid CSRF token.');
        }

        $this->get('ws_events.manager')->deleteParticipation($event,$this->getUser(),true);
        $this->get('flashbag')->add('info',"Une prochaine fois peut être !");

        $this->redirect($this->generateUrl(
            'ws_events_view',array(
                'sport'=>$event->getSport(),
                'slug'=>$event->getSlug(),
                'event'=>$event->getId()
                )
            )
        );
    }
}
