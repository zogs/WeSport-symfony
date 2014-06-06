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
use Ws\EventsBundle\Event\ChangeEvent;


class EventsListener implements EventSubscriberInterface
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
			WsEvents::EVENT_VIEW => 'onViewEvent',
			WsEvents::EVENT_CHANGE => 'onChangeEvent',
		);
	}

	public function onNewEvents(CreateEvents $event)
	{
		//exit('onNewEvents WIN');
	}

	public function onViewEvent(ViewEvent $event)
	{
		//exit('viewEvent WIN');
	}

	public function onChangeEvent(ChangeEvent $event)
	{
		$wsevent = $event->getEvent();

		$participants = $wsevent->getParticipations();

		$this->mailer->sendEventModificationToParticipants($wsevent,$participants);


	}


}