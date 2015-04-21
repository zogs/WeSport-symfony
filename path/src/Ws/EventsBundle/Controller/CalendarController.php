<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

use Ws\EventsBundle\Event\WsEvents;
use Ws\EventsBundle\Event\ViewCalendar;
use Ws\EventsBundle\Event\AjaxCalendar;
use Ws\EventsBundle\Event\ResetCalendar;
use Ws\EventsBundle\Manager\CalendarUrlGenerator;

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
		$session->set('autoLocationed',$location->getCity()->getName());

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

		//$manager->addParams($this->getRequest()->query->all());

		//find searched week
		$week = $manager->findCalendar();	

		//find an empty empty week
		//(existing events will be load be ajax)
		//$manager->prepareParams();
		//$week = $manager->getEmptyWeek();	
		//get search
		$search = $manager->getSearch();	
		
		//create form		
		$form = $this->createForm('calendar_search',$search);
		//throw event
		$this->get('event_dispatcher')->dispatch(WsEvents::CALENDAR_VIEW, new ViewCalendar($search,$this->getUser())); 
		
		
		return $this->render('WsEventsBundle:Calendar:weeks.html.twig', array(
			'weeks' => array($week),
			'is_ajax' => false,
			'search' => $search,            
			'form' => $form->createView(),
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
		$form = $this->createForm('calendar_search');
		$form->handleRequest($request);

		$search = $form->getData();

		if($form->isValid()){

			$urlGenerator = new CalendarUrlGenerator($this->get('router'));			
			$params = $urlGenerator->setSearch($search)->getShortUrlParams();
			
			return $this->redirect($this->generateUrl('ws_calendar',$params));
		}
		else {

			$this->get('flashbag')->add('Oups une erreur est apparu dans le formulaire..','error');
			return $this->redirect($this->generateUrl('ws_calendar'));
		}
		

	}


	public function ajaxAction(Request $request, $date)
	{
		//get calendar manager
		$manager = $this->get('calendar.manager');
		//get the form
		$form = $this->createForm('calendar_search');		

		//set manager params
		$manager->addParamsFromCookies($request->cookies->all());
		$manager->addParameters($request->request->get($form->getName()));		
		$manager->addParameters($request->query->get($form->getName()));
		$manager->addParamsNbDays($request->query->get('nbdays'));
		$manager->addParamsDate($date);

		//find searched week
		$week = $manager->findCalendar();
		//get search params
		$search = $manager->getSearch();

		//throw event
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
		
		$manager->resetParams()->resetCookie()->resetSearch();
		$search = $manager->getSearch();
		$this->get('event_dispatcher')->dispatch(WsEvents::CALENDAR_RESET, new ResetCalendar($search,$this->getUser())); 

		return $this->redirect($this->generateUrl('ws_calendar'));

	}

}
