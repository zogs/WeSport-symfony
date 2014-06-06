<?php 

namespace Ws\EventsBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as Router;

use My\FlashBundle\Controller\FlashController as Flashbag;
use Ws\MailerBundle\Mailer\Mailer;
use Ws\EventsBundle\Event\WsEvents;
use Ws\EventsBundle\Event\AddParticipant;
use Ws\EventsBundle\Event\CancelParticipant;


class ParticipationListener implements EventSubscriberInterface
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
			WsEvents::PARTICIPANT_ADD => 'onAddParticipant',
			WsEvents::PARTICIPANT_CANCEL => 'onCancelParticipant',
		);
	}

	public function onAddParticipant(AddParticipant $event)
	{
		$wsevent = $event->getEvent();
		$participant = $event->getParticipant();

		$this->mailer->sendParticipationAddedToAdmin($wsevent,$participant);
	}

	public function onCancelParticipant(CancelParticipant $event)
	{
		$wsevent = $event->getEvent();
		$participant = $event->getParticipant();

		$this->mailer->sendParticipationCanceledToAdmin($wsevent,$participant);
	}


}