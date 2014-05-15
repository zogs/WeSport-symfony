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


class EventController extends Controller
{
	public function indexAction($name)
	{
		return $this->render('WsEventsBundle:Event:index.html.twig', array('name' => $name));
	}

	/**
	 * Get the calendar of events
	 *
	 * @param string action
	 * @param date date
	 *
	 * @return View
	 */
	public function calendarAction($country,$city,$sports,$date,$nbdays,$type)
	{
		$params = array(
			'country' => $country,
			'city_name' => $city,
			'sports' => $sports,
			'date' => $date,
			'nbdays' => $nbdays,
			'type' => $type,
			);        
		//get manager
		$manager = $this->get('calendar.manager');
		//set search parameter
		$manager->setCookieParams($this->getRequest()->cookies->all());
		$manager->setRequestParams($this->getRequest()->query->all());
		$manager->setUriParams($params);
		//find searched week
		$week = $manager->findCalendarByParams();
		//save search cookie
		$manager->saveSearchCookies();
		//get search params
		$search = $manager->getSearchParams();
		

		return $this->render('WsEventsBundle:Calendar:weeks.html.twig', array(
			'weeks' => array($week),
			'is_ajax' => false,
			'search' => $search,            
			));
	}   


	public function weekAjaxAction($date)
	{
		//get manager
		$manager = $this->get('calendar.manager');
		//set params
		$manager->setCookieParams($this->getRequest()->cookies->all());
		$manager->setRequestParams($this->getRequest()->query->all());
		$manager->setUriParams(array('date'=>$date));
		//find searched week
		$week = $manager->findCalendarByParams();
		//save search cookie
		$manager->saveSearchCookies();
		//get search params
		$search = $manager->getSearchParams();


		return $this->render('WsEventsBundle:Calendar:weeks.html.twig',array(
			'weeks' => array($week),
			'is_ajax' => true,
			'search' => $search,
			));
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

				$this->get('flashbag')->add('formulaire valide','success');
			}
			else {
				$this->get('flashbag')->add('peut pas sauvegarder !','error');
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

		$this->get('ws_events.manager')->deleteSerie($event);
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
