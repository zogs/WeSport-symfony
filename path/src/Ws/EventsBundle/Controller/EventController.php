<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Entity\Serie;
use Ws\EventsBundle\Form\Type\EventType;
use Ws\EventsBundle\Form\Type\CalendarSearchType;
use Ws\EventsBundle\Form\Type\InvitationType;
use Ws\EventsBundle\Event\WsEvents;
use Ws\EventsBundle\Event\CreateEvents;
use Ws\EventsBundle\Event\DeleteEvent;
use Ws\EventsBundle\Event\DeleteSerie;
use Ws\EventsBundle\Event\ViewEvent;
use Ws\EventsBundle\Event\AddParticipant;
use Ws\EventsBundle\Event\CancelParticipant;


class EventController extends Controller
{
	public function indexAction($name)
	{
		return $this->render('WsEventsBundle:Event:index.html.twig', array('name' => $name));
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

			$fields = $request->request->get('event');
			$location = $this->get('world.location_manager')->getLocationFromCityArray($fields['location']);        

			$event = $form->getData();
			$event->setLocation($location);
			$event->setOrganizer($this->getUser());						


			if($this->get('ws_events.manager')->saveAll($event)){

				//set flash message
				$this->get('flashbag')->add('Bravo, votre activité est en ligne !','success');
				
				//throw CREATE_EVENTS
				$this->get('event_dispatcher')->dispatch(WsEvents::CREATE_EVENTS, new CreateEvents($event,$this->getUser()));  
			}
			else {
				$this->get('flashbag')->add('peut pas sauvegardeeer !','error');
			}
		}             

		return $this->render('WsEventsBundle:Event:new.html.twig', array(
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

		//throw event CONFIRM_EVENT
		$sfevent = new ConfirmEvent($event,$this->getUser());
		$this->get('event_dispatcher')->dispatch(WsEvents::CONFIRM_EVENT, $sfevent);   


		$this->get('ws_events.manager')->confirmEvent($event);
		$this->get('flashbag')->add("L'événement a été confirmé !");

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

		//throw event DELETE_EVENT
		$sfevent = new DeleteEvent($event,$this->getUser());
		$this->get('event_dispatcher')->dispatch(WsEvents::DELETE_EVENT, $sfevent);   


		$this->get('ws_events.manager')->deleteEvent($event);
		$this->get('flashbag')->add("L'événement a été supprimé !");

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

		//get whole serie
		$serie = $event->getSerie();

		//throw event DELETE_SERIE
		$sfevent = new DeleteSerie($serie,$this->getUser());
		$this->get('event_dispatcher')->dispatch(WsEvents::DELETE_SERIE, $sfevent);


		$this->get('ws_events.manager')->deleteSerie($serie);
		$this->get('flashbag')->add("Tous les événements ont été supprimés !",'success');

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
		
		$gmap = $this->get('world.gmap');
		$gmap->setSize('100%','100px');
		$gmap->setLang($this->getRequest()->getLocale());
		$gmap->setEnableWindowZoom(true);
		$gmap->addMarkerByAddress($event->getFullAddress(),$event->getTitle());
		$gmap->setCenter($event->getFullAddress());
		$gmap->setZoom(12);
		$gmap->generate();
		$gmap = $gmap->getGoogleMap();

		//throw event		
		$this->get('event_dispatcher')->dispatch(WsEvents::VIEW_EVENT, new ViewEvent($event,$this->getUser()));

		//render
		return $this->render('WsEventsBundle:Event:view.html.twig',array(
			'event'=> $event,
			'gmap'=> $gmap,
			'token'=> $this->get('form.csrf_provider')->generateCsrfToken('delete_event')
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

		//throw event ADD_PARTICIPANT
		$sfevent = new AddParticipant($event,$this->getUser());
		$this->get('event_dispatcher')->dispatch(WsEvents::ADD_PARTICIPANT, $sfevent);   


		$this->get('ws_events.manager')->saveParticipation($event,$this->getUser(),true);
		$this->get('flashbag')->add("Merci de votre participation !");

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

		//throw event CONFIRM_EVENT
		$sfevent = new CancelParticipant($event,$this->getUser());
		$this->get('event_dispatcher')->dispatch(WsEvents::CANCEL_PARTICIPANT, $sfevent);   


		$this->get('ws_events.manager')->deleteParticipation($event,$this->getUser(),true);
		$this->get('flashbag')->add("Une prochaine fois peut être !",'info');

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
