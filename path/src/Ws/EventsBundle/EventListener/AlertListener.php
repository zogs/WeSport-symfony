<?php 

namespace Ws\EventsBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as Router;

use My\FlashBundle\Controller\FlashController as Flashbag;
use Ws\MailerBundle\Mailer\Mailer;
use Ws\EventsBundle\Event\WsEvents;
use Ws\EventsBundle\Event\CreateAlert;
use Ws\StatisticBundle\Manager\StatisticManager;

class AlertListener implements EventSubscriberInterface
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
			WsEvents::ALERT_NEW => 'onNewAlert',
		);
	}

	public function onNewAlert(CreateAlert $event)
	{
		$alert = $event->getAlert();
		$user = $event->getUser();	

		$this->mailer->sendAlertConfirmation($alert,$user);

		$this->statistic->fromEvent($event)->update();
	}

}