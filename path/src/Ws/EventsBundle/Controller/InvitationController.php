<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Entity\Serie;
use Ws\EventsBundle\Form\Type\InvitationType;


class InvitationController extends Controller
{
	/**
	 * Show invitation form and perform invitation
	 *
	 * @param  request
	 * @return  view
	 * 
	 */
	public function newAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();

		$form = $this->createForm(new InvitationType($em,$user));

		$form->handleRequest($request);

		if($form->isValid()){

			$invit = $form->getData();

			if($this->get('ws_events.invit.manager')->saveInvit($invit)){
				$this->get('flashbag')->add('invitation enregistrés','success');
			}

			if($this->get('ws_mailer')->sendInvitationMessage($invit)){
				$this->get('flashbag')->add('invitation envoyés','success');
			}
			
		}

		return $this->render('WsEventsBundle:Invitation:new.html.twig',array(
			'form' => $form->createView(),
			));

	}
}