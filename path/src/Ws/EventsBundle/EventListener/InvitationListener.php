<?php 

namespace Ws\EventsBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as Router;

use My\FlashBundle\Controller\FlashController as Flashbag;
use Ws\MailerBundle\Mailer\Mailer;
use Ws\EventsBundle\Event\WsEvents;
use Ws\EventsBundle\Event\CreateEvents;
use Ws\EventsBundle\Event\ViewEvent;


class InvitationListener implements EventSubscriberInterface
{
	protected $em;
	protected $router;
	protected $flashbag;
	protected $mailer;


	public function __construct(EntityManager $em, Router $router, Flashbag $flashbag, Mailer $mailer)
	{
		$this->em = $em;
		$this->router = $router;
		$this->flashbag = $flashbag;
		$this->mailer = $mailer;
	}

	static public function getSubscribedEvents()
	{
		return array(
			WsEvents::SERIE_CREATE => 'onNewEvents',

		);
	}

	public function onNewEvents(CreateEvents $event)
	{
		$wsevent = $event->getEvent();
		$user = $event->getUser();	

		if($invitations = $wsevent->getInvitations()){

			//first invitations is the one created with the event
			$invitation = $invitations[0];
			//send the invitations
			$emails = $this->mailer->sendInvitationMessages($invitation);
			//add flash message
			$this->flashbag->add(count($emails).' invitations ont été envoyées !');
		}

	}



}