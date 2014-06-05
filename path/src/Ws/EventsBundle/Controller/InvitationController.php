<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Entity\Invited;
use Ws\EventsBundle\Entity\Invitation;
use Ws\EventsBundle\Form\Type\InvitationType;

use My\UtilsBundle\Utils\String;


class InvitationController extends Controller
{
	/**
	 * Show invitation form and perform invitation
	 * @param  request
	 * @return  view
	 * 
	 */
	public function createAction(Event $event)
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

			if($emails = $this->get('ws_mailer')->sendInvitationMessages($invit)){
				$this->get('flashbag')->add('Vous avez envoyé '.count($emails).' invitations !','success');
			}
			
		}

		return $this->render('WsEventsBundle:Invitation:new.html.twig',array(
			'form' => $form->createView(),
			));

	}

	/**
	 * Confirm participation
	 * @param invited
	 * @return view
	 */
	public function confirmParticipationAction(Invited $invited)
	{
		$this->getDoctrine()->getManager()->getRepository('WsEventsBundle:Invited')->confirmParticipation($invited);

		if($invited->isRegisteredUser())
			$message = "Bravo, vous participerez en tant qu'invité ! N'hésitez pas <a href=".$this->generateUrl('fos_user_registration_register').">à vous inscrire</a> pour plus de facilité !";
		else 
			$message = "Bravo, vous participerez à cet événement ! <a href=".$this->generateUrl('fos_user_security_login').">Connectez-vous</a> pour discuter.";
		
		$this->get('flashbag')->add($message);

		return $this->redirect($this->generateUrl('ws_event_view',array('event'=>$invited->getInvitation()->getEvent()->getId())));
	}

	/**
	 * Deny participation
	 * @param invited
	 * @return view
	 */
	public function denyParticipationAction(Invited $invited)
	{
		$this->getDoctrine()->getManager()->getRepository('WsEventsBundle:Invited')->denyParticipation($invited);

		if($invited->isRegisteredUser())
			$message = "Ok, merci d'avoir répondu. N'hésitez pas <a href=".$this->generateUrl('fos_user_registration_register').">à vous inscrire</a> il ya plein de sport sur coSporturage.fr";
		else 
			$message = "Ok, merci d'avoir répondu. Vous voulez <a href=".$this->generateUrl('fos_user_security_login').">vous connecter ?</a>";
		
		$this->get('flashbag')->add($message,'warning');

		return $this->redirect($this->generateUrl('ws_event_view',array('event'=>$invited->getInvitation()->getEvent()->getId())));
	}

	/**
	 * Add emails to the blacklist
	 * @param emails
	 * @return view
	 */
	public function addBlackListAction($emails)
	{
		$emails = String::findEmailsInString($emails);

		if(empty($emails)) throw new Exception('No email in the string parameter');

		$this->getDoctrine()->getManager()->getRepository('WsEventsBundle:InvitationBlacklist')->addEmailsToBlackList($emails);

		$this->get('flashbag')->add('Voila, vous ne recevrez plus d\'invitation de la part des utilisateurs de coSporturage.fr... Mais vous pouvez toujours changer d\'avis !!');

		return $this->redirect($this->generateUrl('ws_calendar'));
	}

	/**
	 * remove emails from blacklist
	 * @param emails
	 * @return view
	 */
	public function removeBlackListAction($emails)
	{
		$emails = String::findEmailsInString($emails);

		if(empty($emails)) throw new Exception('No email in the string parameter');

		$this->getDoctrine()->getManager()->getRepository('WsEventsBundle:InvitationBlacklist')->removeEmailsFromBlackList($emails);

		$this->get('flashbag')->add('Bravo! Vous allez pouvoir recevoir des invitations de la part des membres de coSporturage.fr');

		return $this->redirect($this->generateUrl('ws_calendar'));
	}
}