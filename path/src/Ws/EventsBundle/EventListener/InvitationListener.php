<?php 

namespace Ws\EventsBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as Router;

use My\FlashBundle\Controller\FlashController as Flashbag;
use Ws\MailerBundle\Mailer\Mailer;
use Ws\EventsBundle\Event\WsEvents;
use Ws\EventsBundle\Event\CreateEvents;
use Ws\EventsBundle\Event\CreateInvitation;
use Ws\StatisticBundle\Manager\StatisticManager;


class InvitationListener implements EventSubscriberInterface
{
	protected $em;
	protected $router;
	protected $flashbag;
	protected $mailer;
	protected $statistic;

	public function __construct(EntityManager $em, Router $router, Flashbag $flashbag, Mailer $mailer, StatisticManager $statistic)
	{
		$this->em = $em;
		$this->router = $router;
		$this->flashbag = $flashbag;
		$this->mailer = $mailer;
		$this->statistic = $statistic;
	}

	static public function getSubscribedEvents()
	{
		return array(
			WsEvents::SERIE_CREATE => 'sendMultipleInvitations',
			WsEvents::INVITATION_CREATE => 'sendOneInvitation'

		);
	}

	public function sendMultipleInvitations(CreateEvents $event)
	{
		$wsevent = $event->getEvent();
		$user = $event->getUser();	

		$invitations = $wsevent->getInvitations();

		$total = array();
		if(!empty($invitations)){

			foreach ($invitations as $invitation) {
				
				if($invitation->hasInvited()){

					//send the invitations
					$emails = $this->mailer->sendInvitationMessages($invitation);
					$total = array_merge($total,$emails);					
				}
			}
			//add flash message
			$this->flashbag->add(count($total).' invitations ont été envoyées');
			
		}

	}

	public function sendOneInvitation(CreateInvitation $event)
	{
		$user = $event->getUser();	
		$invitation = $event->getInvitation();

		if($invitation->hasInvited()){

			//send the invitations
			$emails = $this->mailer->sendInvitationMessages($invitation);					
			//add flash message
			$this->flashbag->add(count($emails).' invitations ont été envoyées');
		}

	}



}