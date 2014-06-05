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
		$manager = $this->get('calendar.manager');
		$manager->disableFlashbag();
		$manager->addParamsFromCookies($request->cookies->all());
		$manager->addParamsFromUrl($request->query->get('url'));
		$manager->prepareParams();
		$search = $manager->getSearch();

		$alert = new Alert();
		$alert->setSearch($search);
		$alert->setUser($this->getUser());
		
		$form = $this->createForm(new AlertType(),$alert);

		$form->handleRequest($this->getRequest());

		if($form->isValid()){

			$alert = $form->getData();

			if($this->get('ws_events.alert.manager')->saveAlert($alert)){
				$this->get('flashbag')->add("Voila ! On espère que vous allez recevoir plein d'annonces :)",'success');			
			}

			return $this->redirect($this->generateUrl('ws_alerts_index'));
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


	public function sendAlertsAction($type)
	{

		$em = $this->getDoctrine()->getManager();
		$mailer = $this->get('ws_mailer');
		$manager = $this->get('ws_events.alert.manager');
		$generator = $this->get('calendar.url.generator');

		$alerts = $em->getRepository('WsEventsBundle:Alert')->findAlerts($type);
		$repo = $em->getRepository('WsEventsBundle:Event');		

		$sended = array();
		$nb_events = 0;
		foreach ($alerts as $k => $alert) {
						
			$events = $repo->findEvents($alert->getSearch());

			if(!empty($events)){
				$mailer->sendAlertMessage($alert,$generator,$events);
				$manager->saveAlerted($alert,$events);			

				$sended[] = array('alert'=>$alert,'events'=>$events);
				$nb_events += count($events);
			}
		}

		$manager->flush();
	
		return $this->render('WsEventsBundle:Alert:admin.html.twig',array(
			'sended'=>$sended,
			'alerts'=>$alerts,
			'nb_events'=>$nb_events,
			'type'=>$type,
			));
	}


	
}