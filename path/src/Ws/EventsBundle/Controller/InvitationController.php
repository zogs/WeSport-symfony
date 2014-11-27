<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use My\UserBundle\Entity\User;
use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Entity\Invited;
use Ws\EventsBundle\Entity\Invitation;
use Ws\EventsBundle\Form\Type\InvitationsType;
use Ws\EventsBundle\Event\WsEvents;
use Ws\EventsBundle\Event\CreateInvitation;

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

		//Only people who participate to the event can invite other
		if($em->getRepository('WsEventsBundle:Participation')->isUserParticipating($this->getUser(),$event)){
			
			$secu = $this->get('security.context');		
			$form = $this->createForm(new InvitationsType($em,$secu,$event));
		
			$form->handleRequest($this->getRequest());

			if($form->isValid()){

				$invitation = $form->getData();	
				
				if(!$invitation->isEmpty()){

					$this->get('ws_events.invit.manager')->saveInvit($invitation);
					$this->get('event_dispatcher')->dispatch(WsEvents::INVITATION_CREATE, new CreateInvitation($invitation,$this->getUser())); 												
					$this->get('ws_events.invit.manager')->saveAsSended($invitation);
				}	
				else {
					$this->get('flashbag')->add('Hum... le formulaire est plutôt vide...','error');
				}				
				
			}

			$invitations = $this->get('ws_events.invit.manager')->getEventInvitations($event);

			return $this->render('WsEventsBundle:Invitation:new.html.twig',array(
				'form' => $form->createView(),
				'event' => $event,
				'invitations'=>$invitations,
				));
		}
		//else redirect to the event page
		else {
			$this->get('flashbag')->add("Vous ne pouvez pas inviter quelqu'un tant que vous ne participez pas",'error');
			return $this->redirect($this->generateUrl('ws_event_view',array('event'=>$event->getId(),'slug'=>$event->getSlug())));
		}

	}

	public function getInviterEmailsAction()
	{
		if(null==$this->getUser()) return new JsonResponse(array());

		$em = $this->getDoctrine()->getManager();		
		$emails = $em->getRepository('WsEventsBundle:Invited')->findByUserAndIsLikeEmail($this->getUser(),$this->getRequest()->query->get('email_is_like'));

		foreach ($emails as $k => $invited) {
			
			$emails[$k] = array();
		            $emails[$k]['token'] = preg_split('/[ -]/',$invited->getId().' '.$invited->getEmail());
		            $emails[$k]['id'] = $invited->getId();
		            $emails[$k]['email'] = $invited->getEmail();
		}
		
		return new JsonResponse($emails);

	}

	/**
	 * Resend a email to the invited person
	 * @param invited
	 * @return   view
	 */
	public function resendAction(Invited $invited)
	{
		if($this->getUser() != $invited->getInvitation()->getInviter()) throw new AccessDeniedHttpException('You are not allowed to do that');

		if($invited->getNbSended() < 3){

			$email = $this->get('ws_mailer')->sendInvitedMessage($invited);

			$invited->setNbSended($invited->getNbSended()+1);
			$date = new \DateTime('now');
			$invited->setDate($date);
			$this->get('ws_events.invit.manager')->saveInvited($invited);

			$this->get('flashbag')->add('Un email a été envoyé à '.$email,'info');
			
		}
		else {
			$this->get('flashbag')->add("Désolé mais l'invitation a déjà été envoyé 3 fois...",'warning');
		}

		return $this->redirect($this->generateUrl('ws_event_view',array('event'=>$invited->getInvitation()->getEvent()->getId(),'slug'=>$invited->getInvitation()->getEvent()->getSlug())));

	}


	/**
	 * Confirm participation
	 * @param invited
	 * @return view
	 */
	public function confirmParticipationAction(Invited $invited)
	{
		$em = $this->getDoctrine()->getManager();

		//check if the invited person is already participating
		if(NULL != $em->getRepository('WsEventsBundle:Participation')->findParticipation($invited->getInvitation()->getEvent(),$invited->getUser(),$invited)){		
			//delete invited
			//in order to avoid double participation
			$em->getRepository('WsEventsBundle:Invited')->removeInvited($invited);
			//display message
			$message = "Bravo ".$invited->getUser()->getUsername().", vous participez à cette activité ! <a href=".$this->generateUrl('fos_user_security_login').">Connectez-vous</a> pour discuter.";
		}
		//if not
		else {
			//confirm participaiton
			$em->getRepository('WsEventsBundle:Invited')->confirmParticipation($invited);

			//display message
			if($invited->isRegisteredUser())
				$message = "Bravo ".$invited->getUser()->getUsername().", vous participez à cette activité ! <a href=".$this->generateUrl('fos_user_security_login').">Connectez-vous</a> pour discuter.";
			else 
				$message = "Bravo, vous participerez en tant qu'invité ! N'hésitez pas <a href=".$this->generateUrl('fos_user_registration_register').">à vous inscrire</a> pour plus de facilité !";	
		}		

		$this->get('flashbag')->add($message);

		return $this->redirect($this->generateUrl('ws_event_view',array('event'=>$invited->getInvitation()->getEvent()->getId(),'slug'=>$invited->getInvitation()->getEvent()->getSlug())));
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

		return $this->redirect($this->generateUrl('ws_event_view',array('event'=>$invited->getInvitation()->getEvent()->getId(),'slug'=>$invited->getInvitation()->getEvent()->getSlug())));
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