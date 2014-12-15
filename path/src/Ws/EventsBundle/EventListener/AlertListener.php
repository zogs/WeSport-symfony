<?php 

namespace Ws\EventsBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as Router;

use My\FlashBundle\Controller\FlashController as Flashbag;
use Ws\MailerBundle\Mailer\Mailer;
use Ws\EventsBundle\Event\WsEvents;
use Ws\EventsBundle\Event\CreateAlert;


class AlertListener implements EventSubscriberInterface
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
			WsEvents::ALERT_CREATE => 'onNewAlert',
		);
	}

	public function onNewAlert(CreateAlert $event)
	{
		$alert = $event->getAlert();
		$user = $event->getUser();	

		$this->mailer->sendAlertConfirmation($alert,$user);

	}

}