<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

use Ws\EventsBundle\Form\Type\AlertType;
use Ws\EventsBundle\Manager\CalendarUrlGenerator;
use Ws\EventsBundle\Entity\Alert;
use Ws\EventsBundle\Event\WsEvents;
use Ws\EventsBundle\Event\CreateAlert;

class AlertController extends Controller
{
	/**
	 * Show alert form
	 * @param  request
	 * @return  view
	 * 
	 */
	public function createAction(Request $request)
	{						
		//Prepare Search request from URL
		//get manager
		$manager = $this->get('calendar.manager');
		$manager->disableFlashbag();
		//add cookie params
		$manager->addParamsFromCookies($request->cookies->all());
		//add URL params
		$manager->addParamsFromUrl($request->query->get('url'));
		//compute params and prepare Search
		$manager->prepareParams();
		$search = $manager->getSearch();

		//Create empty alert
		$alert = new Alert();
		//pre-filled with Search from URL
		$alert->setSearch($search);
		
		//Create form
		$form = $this->createForm('alert_type',$alert);
		$form->handleRequest($this->getRequest());

		if($form->isValid()){
		
			if($alert->getSearch()->hasLocation()){

				if($alert->getSearch()->hasSports()){
					
					if($this->get('ws_events.alert.manager')->saveAlert($alert)){

						$this->get('flashbag')->add("C'est bon :) N'oubliez pas de regarder vos mails!",'success');

						$this->get('event_dispatcher')->dispatch(WsEvents::ALERT_NEW, new CreateAlert($alert,$this->getUser())); 	
	
					}

					return $this->redirect($this->generateUrl('ws_alerts_index'));					
				}
				else {
					$this->get('flashbag')->add('Veuillez choisir un ou plusieurs sports...','warning');
				}
				
			}
			else {
				$this->get('flashbag')->add('Veuillez choisir une ville...','warning');
			}
		}		

		
		return $this->render('WsEventsBundle:Alert:create.html.twig',array(
			'form' => $form->createView(),
			));

	}

	public function indexAction()
	{
		$user = $this->getUser();

		$alerts = $this->getDoctrine()->getRepository('WsEventsBundle:Alert')->findByUser($user);

		return $this->render('WsEventsBundle:Alert:index.html.twig',array(
			'alerts'=>$alerts,
			));

	}

	public function viewAction(Alert $alert)
	{
		if($alert->getUser() != $this->getUser()) throw new Exception("You can not see this page", 1);

		$events = $this->getDoctrine()->getManager()->getRepository('WsEventsBundle:Event')->findEvents($alert->getSearch());

		return $this->render('WsEventsBundle:Alert:view.html.twig',array(
			'alert' => $alert,
			'events'=> $events,
			));
		
	}


	public function deleteAction(Alert $alert)
	{
		$user = $this->getUser();

		if($user != $alert->getUser()) throw new Exception("Sorry but you can't delete someone else's alert", 1);
		
		$this->get('ws_events.alert.manager')->deleteAlert($alert);

		$this->get('flashbag')->add("Alerte correctement supprimé.","success");

		return $this->redirect($this->generateUrl('ws_alerts_index'));
	}

	public function disableAction(Alert $alert)
	{
		if($this->getUser() != $alert->getUser()) throw new Exception("Sorry but you can't disable someone else's alert", 1);

		$this->get('ws_events.alert.manager')->disableAlert($alert);

		$this->get('flashbag')->add("Alerte désactivé !","success");

		return $this->redirect($this->generateUrl('ws_alerts_index'));
	}

	public function enableAction(Alert $alert)
	{
		if($this->getUser() != $alert->getUser()) throw new Exception("Sorry but you can't enable someone else's alert", 1);

		$this->get('ws_events.alert.manager')->enableAlert($alert);

		$this->get('flashbag')->add("Alerte réactivé !","success");

		return $this->redirect($this->generateUrl('ws_alerts_index'));
	}

	public function extendAction(Alert $alert, $nbmonth){

		if($this->get('ws_events.alert.manager')->extendAlert($alert,$nbmonth)){
			$this->get('flashbag')->add("Votre alerte a été prolongé :)",'success');			
		}

		return $this->redirect($this->generateUrl('ws_alerts_index'));
	}


	public function sendAlertsAction($type)
	{

		$em = $this->getDoctrine()->getManager();
		$mailer = $this->get('ws_mailer');
		$manager = $this->get('ws_events.alert.manager');
		$generator = $this->get('calendar.url.generator');
		$repo = $em->getRepository('WsEventsBundle:Event');		
		$sended = array();
		$expired = array();
		$nb_events = 0;

		//find daily or monthly alerts
		$alerts = $em->getRepository('WsEventsBundle:Alert')->findAlerts($type);

		//for each alert		
		foreach ($alerts as $k => $alert) {

			//find all events matching the alert		
			$events = $repo->findEvents($alert->getSearch());

			if(!empty($events)){
				//mail the new events to the user
				$mailer->sendAlertMessage($alert,$generator,$events);
				//save which events have been alerted
				$manager->saveAlerted($alert,$events);			

				$sended[] = array('alert'=>$alert,'events'=>$events);
				$nb_events += count($events);
			}

			//disactive outdated alerts
			$now = new \DateTime('now');
			if($alert->getDateStop()->format('Ymd') == $now->format('Ymd')){
				$manager->disableAlert($alert);
				//inform the user this alert is outdated
				$mailer->sendExpiredAlertmessage($alert);

				$expired[] = $alert;
			}
		}

		$manager->flush();
	
		return $this->render('WsEventsBundle:Alert:admin.html.twig',array(
			'expired'=>$expired,
			'sended'=>$sended,
			'alerts'=>$alerts,
			'nb_events'=>$nb_events,
			'type'=>$type,
			));
	}


	
}