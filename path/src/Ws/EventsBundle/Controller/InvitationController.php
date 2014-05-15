<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

use Ws\EventsBundle\Entity\Event;
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
	public function newAction(Event $event)
	{
		$em = $this->getDoctrine()->getManager();
		$secu = $this->get('security.context');

		$form = $this->createForm(new InvitationType($em,$secu,$event));

		$form->handleRequest($this->getRequest());

		if($form->isValid()){

			$invit = $form->getData();

			if($this->get('ws_events.invit.manager')->saveInvit($invit)){
				$this->get('flashbag')->add('invitation enregistrés','success');
			}

			if($count = $this->get('ws_mailer')->sendInvitationMessage($invit)){
				$this->get('flashbag')->add($count.' invitations envoyés','success');
			}
			
		}

		return $this->render('WsEventsBundle:Invitation:new.html.twig',array(
			'form' => $form->createView(),
			));

	}
}