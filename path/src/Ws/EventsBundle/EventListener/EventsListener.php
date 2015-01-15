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
use Ws\EventsBundle\Event\DeleteEvent;
use Ws\StatisticBundle\Manager\StatisticManager;


class EventsListener implements EventSubscriberInterface
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
			WsEvents::SERIE_CREATE => 'onNewEvents',
			WsEvents::EVENT_VIEW => 'onViewEvent',
			WsEvents::EVENT_CHANGE => 'onChangeEvent',
			WsEvents::EVENT_CONFIRM => 'onConfirmEvent',
			WsEvents::EVENT_DELETE => 'onDeleteEvent',
		);
	}

	public function onNewEvents(CreateEvents $event)
	{
		$this->statistic->fromEvent($event)->update();
	}

	public function onViewEvent(ViewEvent $event)
	{
		//exit('viewEvent WIN');
	}

	public function onChangeEvent(ChangeEvent $event)
	{
		$ev = $event->getEvent();

		$changes = $ev->getChanges();
		
		if(!empty($changes)){
			$this->mailer->sendEventModificationToParticipants($ev);			
		}
	}

	public function onConfirmEvent(ConfirmEvent $event)
	{
		$ev = $event->getEvent();

		$this->mailer->sendEventConfirmedToParticipants($ev);

		$this->statistic->fromEvent($event)->update();
	}

	public function onDeleteEvent(DeleteEvent $event)
	{
		$ev = $event->getEvent();

		$this->mailer->sendEventDeletedToParticipants($ev);

		$this->statistic->fromEvent($event)->update();
	}


}