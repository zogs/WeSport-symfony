<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;


use Ws\EventsBundle\Event\WsEvents;
use Ws\EventsBundle\Event\ViewCalendar;
use Ws\EventsBundle\Event\AjaxCalendar;


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
	public function loadAction($country,$city,$sports,$date,$nbdays,$type,$time,$price,$organizer)
	{
		
		$params = array(
			'country' => $country,
			'city_name' => $city,
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

		//set search parameter
		$manager->setCookieParams($this->getRequest()->cookies->all());
		$manager->setUriParams($params);
		//find searched week
		$week = $manager->findCalendar();
		//save search cookie
		$manager->saveSearchCookies();
		//get search params
		$search = $manager->getSearchData();
		
		//throw VIEW_CALENDAR
		$this->get('event_dispatcher')->dispatch(WsEvents::VIEW_CALENDAR, new ViewCalendar($search,$this->getUser())); 


		return $this->render('WsEventsBundle:Calendar:weeks.html.twig', array(
			'weeks' => array($week),
			'is_ajax' => false,
			'search' => $search,            
			));
	}   


	public function ajaxAction($date)
	{
		//get manager
		$manager = $this->get('calendar.manager');
		//set params
		$manager->setCookieParams($this->getRequest()->cookies->all());
		$manager->setGetParams($this->getRequest()->query->all());
		$manager->setDateWeek($date);
		//find searched week
		$week = $manager->findCalendar();
		//save search cookie
		$manager->saveSearchCookies();
		//get search params
		$search = $manager->getSearchData();

		//throw AJAX_CALENDAR
		$this->get('event_dispatcher')->dispatch(WsEvents::AJAX_CALENDAR, new AjaxCalendar($search,$this->getUser())); 


		return $this->render('WsEventsBundle:Calendar:weeks.html.twig',array(
			'weeks' => array($week),
			'is_ajax' => true,
			'search' => $search,
			));
	}

}
