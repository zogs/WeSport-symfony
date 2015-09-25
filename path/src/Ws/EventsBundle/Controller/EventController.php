<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Entity\Serie;
use Ws\EventsBundle\Form\Type\EventType;
use Ws\EventsBundle\Form\Type\CalendarSearchType;
use Ws\EventsBundle\Form\Type\InvitationType;
use Ws\EventsBundle\Event\WsEvents;
use Ws\EventsBundle\Event\CreateEvents;
use Ws\EventsBundle\Event\DeleteEvent;
use Ws\EventsBundle\Event\EditEvent;
use Ws\EventsBundle\Event\DeleteSerie;
use Ws\EventsBundle\Event\ConfirmEvent;
use Ws\EventsBundle\Event\ViewEvent;
use Ws\EventsBundle\Event\AddParticipant;
use Ws\EventsBundle\Event\CancelParticipant;
use Ws\StatisticBundle\Entity\UserStat;


class EventController extends Controller
{
	public function indexAction()
	{
		if(null == $this->getUser()) throw new AccessDeniedException();
		
		$series = $this->getDoctrine()->getManager()->getRepository('WsEventsBundle:Serie')->findSeriesToComeByUser($this->getUser());
		
		return $this->render('WsEventsBundle:Event:index.html.twig', array('series' => $series));
	}	
	
	/**
	 * Get and manage the creation form
	 *
	 * @param request
	 *
	 * @return View
	 */
	public function createAction(Request $request)
	{

		$event = new Event();

		$form = $this->createForm('event',$event);

		$form->handleRequest($request);

		if($form->isValid()){

			$event = $form->getData();	
			
			if($event = $this->get('ws_events.manager')->saveAll($event)){

				//set flash message
				if($event->getSerie()->getOccurences()<=1)
					$this->get('flashbag')->add('Bravo, votre activité est en ligne !','success');
				else
					$this->get('flashbag')->add('Bravo, vos activités sont en ligne ! Et voici la première... ','success');
				
				//throw event SERIE_CREATE
				$this->get('event_dispatcher')->dispatch(WsEvents::SERIE_CREATE, new CreateEvents($event,$this->getUser())); 

			}
			else {
				$this->get('flashbag')->add('peut pas sauvegardeeer !','error');
			}
			
			return $this->redirect($this->generateUrl('ws_event_view',array('event'=>$event->getId(),'slug'=>$event->getSlug())));
		}             

		return $this->render('WsEventsBundle:Event:new.html.twig', array(
			'form' => $form->createView(),
			));
	}

	/**
	 * Get and manage the edit form
	 *
	 * @param request
	 *
	 * @return View
	 */
	public function editAction(Event $event)
	{
		$form = $this->createForm('event',$event);

		$form->handleRequest($this->getRequest());

		if($form->isValid()){

			$event = $form->getData();
			
			if($this->get('ws_events.manager')->saveEvent($event,true)){

				//set flash message
				$this->get('flashbag')->add('Votre activité a été modifié !','success');
				
				//throw CREATE_EVENTS
				$this->get('event_dispatcher')->dispatch(WsEvents::EVENT_EDIT, new EditEvent($event,$this->getUser()));

				//redirect to update the event's displayed
				return $this->redirect($this->generateUrl('ws_event_edit',array('event'=>$event->getId())));  
			}
			else {
				$this->get('flashbag')->add('peut pas sauvegardeeer !','error');
			}
		}             

		return $this->render('WsEventsBundle:Event:edit.html.twig', array(
			'form' => $form->createView(),
			'event'=> $event,
			));
	}



	public function confirmAction(Event $event, $token)
	{
		if (!$this->get('form.csrf_provider')->isCsrfTokenValid('event_token', $token)) {
			throw new AccessDeniedException('Invalid CSRF token.');
		}

		if($this->getUser()!=$event->getOrganizer()) {
			throw $this->createNotFoundException('Vous ne pouvez pas confirmer cet événement');        
		} 

		if($this->get('ws_events.manager')->confirmEvent($event)){
			
			$this->get('flashbag')->add("L'événement a été confirmé !");
			
		}

		$this->redirect($this->generateUrl("ws_event_view",array('event'=>$event->getId()))); 
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
		if (!$this->get('form.csrf_provider')->isCsrfTokenValid('event_delete', $token)) {
			throw new AccessDeniedException('Invalid CSRF token.');
		}

		if($this->getUser()!=$event->getOrganizer()) {
			throw $this->createNotFoundException('Vous ne pouvez pas supprimer cet événement');        
		}

		$this->get('event_dispatcher')->dispatch(WsEvents::EVENT_DELETE, new DeleteEvent($event,$this->getUser()));  
		
		$this->get('ws_events.manager')->deleteEvent($event);

		$this->get('flashbag')->add("L'activité a été supprimé !");

		return $this->redirect($this->generateUrl("ws_event_create"));     
	}


	/**
	 * Delete a serie 
	 *
	 * @param event
	 * @param token
	 *
	 * @return Redirect
	 */
	public function deleteSerieAction(Serie $serie,$token)
	{
		if (!$this->get('form.csrf_provider')->isCsrfTokenValid('serie_delete', $token)) {
			throw new AccessDeniedException('Invalid CSRF token.');
		}

		if($this->getUser()!=$serie->getOrganizer()) {
			throw $this->createNotFoundException('Vous ne pouvez pas supprimer cette série');        
		}   

		//throw event DELETE_SERIE
		$this->get('event_dispatcher')->dispatch(WsEvents::SERIE_DELETE, new DeleteSerie($serie,$this->getUser()));

		$this->get('ws_events.manager')->deleteSerie($serie);
		
		$this->get('flashbag')->add("Tous les événements ont été supprimés !",'success');

		return $this->redirect($this->generateUrl("ws_event_create")); 

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
		
		//get google map
		//located on the Event address
		$gmap = $this->get('world.gmap');
		$gmap->setSize('100%','100%');
		$gmap->setLang($this->getRequest()->getLocale());
		$gmap->setEnableWindowZoom(true);
		$gmap->addMarkerByAddress($event->getSpot()->getFullAddress(),$event->getTitle());
		$gmap->setCenter($event->getSpot()->getFullAddress());
		$gmap->setZoom(12);
		$gmap->generate();
		$gmap = $gmap->getGoogleMap();

		//find if the current User follow this organizer
		$event->followed = $this->get('ws_events.follow.manager')->isEventFollowed($event);

		//throw event		
		$this->get('event_dispatcher')->dispatch(WsEvents::EVENT_VIEW, new ViewEvent($event,$this->getUser()));

		//render
		return $this->render('WsEventsBundle:Event:view.html.twig',array(
			'event'=> $event,
			'gmap'=> $gmap,			
			)
		);
	}

}
