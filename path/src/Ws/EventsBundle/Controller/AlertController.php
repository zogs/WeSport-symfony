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
		$manager->addParamsFromCookies($request->cookies->all());
		$manager->addParamsFromUrl($request->query->get('url'));
		$manager->prepareParams();
		$search = $manager->getSearch();

		$alert = new Alert();
		$alert->setSearch($search);
		$alert->setUser($this->getUser());

		$form = $this->createForm(new AlertType(),$alert);

		$form->handleRequest($this->getRequest());

		if($form->isSubmitted()){
			$alert = $form->getData();

			if($this->get('ws_events.alert.manager')->saveAlert($alert)){
				$this->get('flashbag')->add('alert saved','success');			
			}
		}
		
		/*
		$em = $this->getDoctrine()->getManager();
		$secu = $this->get('security.context');

		$form = $this->createForm(new InvitationType($em,$secu,$event));
	
		$form->handleRequest($this->getRequest());

		if($form->isValid()){

			$invit = $form->getData();

			if($this->get('ws_events.invit.manager')->saveInvit($invit)){
				$this->get('flashbag')->add('invitation enregistrés','success');
			}			

			if($emails = $this->get('ws_mailer')->sendInvitationMessages($invit)){
				$this->get('flashbag')->add('Vous avez envoyé '.count($emails).' invitations !','success');
			}
			
		}
		*/

		return $this->render('WsEventsBundle:Alert:create.html.twig',array(
			'form' => $form->createView(),
			));

	}

	
}