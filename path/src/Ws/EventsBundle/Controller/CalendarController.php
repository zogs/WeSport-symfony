<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

use Ws\EventsBundle\Event\WsEvents;
use Ws\EventsBundle\Event\ViewCalendar;
use Ws\EventsBundle\Event\AjaxCalendar;
use Ws\EventsBundle\Event\ResetCalendar;


use Yavin\Symfony\Controller\InitControllerInterface;


class CalendarController extends Controller implements InitControllerInterface
{

	/**
	 * Is call before each Action of the controller
	 * Set the default City and Country of the visitor , if exists
	 *
	 * @param Request
	 */
	public function init(Request $request)
	{
		//check session autoLocation value
		$session = $request->getSession();

		//return nothing if the user have already being autolocationed
		if(null != $session->get('autoLocationed')) return;

		//return nothing if the user is already connected, no need to find his city
		if(null != $this->getUser()) return;

		//get the client IP and use the locationip service to find the nearest city Location
		$client_ip = ($request->getClientIp()!= '127.0.0.1')? $request->getClientIp() : '193.52.250.230';  //213.186.33.87
		$location = $this->get('world.locationip.service')->getLocationFromIp($client_ip);

		//Return nothing if we can't find the city of the visitor
		if(!isset($location) || $location->exist() == false) return; 
		//Call the calendar Manager and set the country and city parameters
		$manager = $this->get('calendar.manager');		
		$manager->addParameters(array(
			'country' => $location->getCountry()->getCode(),
			'city' => $location->getCity()->getId(),
			'area' => 50,
			));
		$manager->setAutoLocation(true);
		$session->set('autoLocationed',true);

	}


	/**
	 * Get the calendar of events
	 *
	 * @param string action
	 * @param date date
	 *
	 * @return View
	 */
	public function loadAction($city,$sports,$date,$nbdays,$type,$time,$price,$level,$organizer)
	{
		
		$params = array(
			'city' => $city,
			'sports' => $sports,
			'date' => $date,
			'nbdays' => $nbdays,
			'type' => $type,
			'time' => $time,	
			'price' => $price,
			'level' => $level,
			'organizer' => $organizer,		
			);   

		//get manager
		$manager = $this->get('calendar.manager');
		
		//set parameters
		$manager->addParamsFromCookies($this->getRequest()->cookies->all());
		$manager->addParamsURI($params);
		$manager->addParams($this->getRequest()->query->all());
		//find searched week
		$week = $manager->findCalendar();
		//get search params
		$search = $manager->getSearch();
	
		//throw event
		$this->get('event_dispatcher')->dispatch(WsEvents::CALENDAR_VIEW, new ViewCalendar($search,$this->getUser())); 
		
		return $this->render('WsEventsBundle:Calendar:weeks.html.twig', array(
			'weeks' => array($week),
			'is_ajax' => false,
			'search' => $search,            
			)
		);
	}  

	/**
	 * Update the calendar from form
	 *
	 * @return redirect loadAction
	 */
	public function updateAction(Request $request)
	{
		$manager = $this->get('calendar.manager');

		$search = $manager
					->addParamsFromCookies($request->cookies->all())
					->addParams($request->request->all())
					->prepareParams()
					->getSearch();

		$params = $search->getShortUrlParams();
		
		return $this->redirect($this->generateUrl('ws_calendar',$params));

	}


	public function ajaxAction($date)
	{
		//get manager
		$manager = $this->get('calendar.manager');
		//set params
		$manager->addParamsFromCookies($this->getRequest()->cookies->all())
				->addParams($this->getRequest()->query->all())
				->addParamsDate($date);

		//find searched week
		$week = $manager->findCalendar();
		//get search params
		$search = $manager->getSearch();

		//throw AJAX_CALENDAR
		$this->get('event_dispatcher')->dispatch(WsEvents::CALENDAR_AJAX, new AjaxCalendar($search,$this->getUser())); 


		return $this->render('WsEventsBundle:Calendar:weeks.html.twig',array(
			'weeks' => array($week),
			'is_ajax' => true,
			'search' => $search,
			));
	}

	public function resetAction()
	{
		$manager = $this->get('calendar.manager');

		$manager->resetParams();

		$week = $manager->findCalendar();
		//get search params
		$search = $manager->getSearch();

		//throw RESET_CALENDAR
		$this->get('event_dispatcher')->dispatch(WsEvents::CALENDAR_RESET, new ResetCalendar($search,$this->getUser())); 


		return $this->render('WsEventsBundle:Calendar:weeks.html.twig',array(
			'weeks' => array($week),
			'is_ajax' => false,
			'search' => $search,
			));

	}

}
