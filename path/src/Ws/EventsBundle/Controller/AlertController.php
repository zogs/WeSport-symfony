<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

use Ws\EventsBundle\Form\Type\AlertType;
use Ws\EventsBundle\Manager\CalendarUrlGenerator;
use Ws\EventsBundle\Entity\Alert;

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
						$this->get('flashbag')->add("Voila ! Vous allez recevoir plein d'annonces inchallah :)",'success');			
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


	public function deleteAction(Alert $alert)
	{
		$user = $this->getUser();

		if($user != $alert->getUser()) throw new Exception("Sorry but you can't delete someone else's alert", 1);
		
		$this->get('ws_events.alert.manager')->deleteAlert($alert);

		$this->get('flashbag')->add("Alerte correctement supprimé.","success");

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

		//find daily or monthly alerts
		$alerts = $em->getRepository('WsEventsBundle:Alert')->findAlerts($type);

		$repo = $em->getRepository('WsEventsBundle:Event');		
		$sended = array();
		$expired = array();
		$nb_events = 0;
		//for each alert		
		foreach ($alerts as $k => $alert) {
			//find events matching the alert		
			$events = $repo->findEvents($alert->getSearch());

			if(!empty($events)){
				//mail the user with the new events
				$mailer->sendAlertMessage($alert,$generator,$events);
				//save which events have been alerted
				$manager->saveAlerted($alert,$events);			

				$sended[] = array('alert'=>$alert,'events'=>$events);
				$nb_events += count($events);
			}

			//disactive outdated alerts
			$now = new \DateTime('now');
			if($alert->getDateStop()->format('Ymd') == $now->format('Ymd')){
				$manager->disactiveAlert($alert);
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