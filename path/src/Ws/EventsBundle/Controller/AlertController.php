<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
		$form = $this->createForm('alert',$alert);
		$form->handleRequest($this->getRequest());

		if($form->isValid()){
		
			if($alert->getSearch()->hasLocation()){

				if($alert->getSearch()->hasSports()){
					
					if($this->get('ws_events.alert.manager')->saveAlert($alert)){

						$this->get('flashbag')->add("C'est parti, maintenant y'a plus qu'a attendre !",'success');

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
		dump($_SERVER);
		exit();
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
		//only ROLE_ADMIN can perform this
		if($this->get('security.context')->isGranted('ROLE_ADMIN') === false) throw new AccessDeniedException('Only administrators can perform this action');

		$alerter = $this->get('ws_events.alerter');

		$results = $alerter->send($type);
	
		return $this->render('WsEventsBundle:Alert:admin.html.twig',array(
			'results'=>$results,
			'type'=>$type,
			));
	}

	public function sendUserAlertsAction($username)
	{
		//only ROLE_ADMIN can perform this
		if($this->get('security.context')->isGranted('ROLE_ADMIN') === false) throw new AccessDeniedException('Only administrators can perform this action');

		$em = $this->getDoctrine()->getManager();

		if(is_numeric($username)) $user = $em->getRepository('MyUserBundle:User')->findById($username);
		elseif(is_string($username)) $user = $em->getRepository('MyUserBundle:User')->findByUsername($username);
		if( !$user ) throw new \Exception("User is not defined", 1);
	

		$alerts = $em->getRepository('WsEventsBundle:Alert')->findByUser($user);
		$alerter = $this->get('ws_events.alerter');
		$results = $alerter->sendAlerts($alerts);
	
		return $this->render('WsEventsBundle:Alert:admin.html.twig',array(
			'results'=>$results,
			'type'=>'user:'.$user->getUsername(),
			));
	}

	public function sendMyAlertsAction()
	{
		$user = $this->getUser();

		if( !$user ) throw new \Exception("You must be connected to perform this action", 1);
		
		$alerts = $this->getDoctrine()->getManager()->getRepository('WsEventsBundle:Alert')->findByUser($user);
		$alerter = $this->get('ws_events.alerter');
		$results = $alerter->sendAlerts($alerts);
	
		return $this->render('WsEventsBundle:Alert:admin.html.twig',array(
			'results'=>$results,
			'type'=>'user:'.$user->getUsername(),
			));
	}

	
}