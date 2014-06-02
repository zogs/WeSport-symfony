<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;


use Ws\EventsBundle\Event\WsEvents;
use Ws\EventsBundle\Event\ViewCalendar;
use Ws\EventsBundle\Event\AjaxCalendar;
use Ws\EventsBundle\Event\ResetCalendar;


class CalendarController extends Controller
{

	/**
	 * Get the calendar of events
	 *
	 * @param string action
	 * @param date date
	 *
	 * @return View
	 */
	public function loadAction($city,$sports,$date,$nbdays,$type,$time,$price,$organizer)
	{
		
		$params = array(
			'city' => $city,
			'sports' => $sports,
			'date' => $date,
			'nbdays' => $nbdays,
			'type' => $type,
			'time' => $time,	
			'price' => $price,
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
		
		//throw VIEW_CALENDAR
		$this->get('event_dispatcher')->dispatch(WsEvents::VIEW_CALENDAR, new ViewCalendar($search,$this->getUser())); 


		return $this->render('WsEventsBundle:Calendar:weeks.html.twig', array(
			'weeks' => array($week),
			'is_ajax' => false,
			'search' => $search,            
			));
	}  

	/**
	 * Update the calendar from form
	 *
	 * @return redirect loadAction
	 */
	public function updateAction(Request $request)
	{
		$manager = $this->get('calendar.manager');
		$manager->addParamsFromCookies($request->cookies->all());
		$manager->addParams($request->request->all());
		$manager->prepareParams();
		$search = $manager->getSearch();
		$params = $search->getShortUrlParams();

		return $this->redirect($this->generateUrl('ws_events_calendar',$params));

	}


	public function ajaxAction($date)
	{
		//get manager
		$manager = $this->get('calendar.manager');
		//set params
		$manager->addParamsFromCookies($this->getRequest()->cookies->all());
		$manager->addParams($this->getRequest()->query->all());
		$manager->addParamsDate($date);
		//find searched week
		$week = $manager->findCalendar();
		//get search params
		$search = $manager->getSearch();

		//throw AJAX_CALENDAR
		$this->get('event_dispatcher')->dispatch(WsEvents::AJAX_CALENDAR, new AjaxCalendar($search,$this->getUser())); 


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
		$this->get('event_dispatcher')->dispatch(WsEvents::RESET_CALENDAR, new ResetCalendar($search,$this->getUser())); 


		return $this->render('WsEventsBundle:Calendar:weeks.html.twig',array(
			'weeks' => array($week),
			'is_ajax' => false,
			'search' => $search,
			));

	}

}
