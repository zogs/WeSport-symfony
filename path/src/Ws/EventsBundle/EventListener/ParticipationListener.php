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
use Ws\StatisticBundle\Manager\StatisticManager;
use Ws\EventsBundle\Manager\EventManager;

class ParticipationListener implements EventSubscriberInterface
{
	protected $flashbag;
	protected $mailer;
	protected $statistic;
	protected $manager;

	public function __construct(Flashbag $flashbag, Mailer $mailer, StatisticManager $statistic,EventManager $manager)
	{
		$this->flashbag = $flashbag;
		$this->mailer = $mailer;
		$this->statistic = $statistic;
		$this->manager = $manager;
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

		//confirm event if nb min is reached
		if($wsevent->countParticipation() == $wsevent->getNbMin()){
			$this->manager->confirmEvent($wsevent);
		}


		$this->mailer->sendParticipationAddedToAdmin($wsevent,$participant);

		$this->statistic->fromEvent($event)->update();
	}

	public function onCancelParticipant(CancelParticipant $event)
	{
		$wsevent = $event->getEvent();
		$participant = $event->getParticipant();

		//unconfirm event if nb min is reached
		if($wsevent->countParticipation() < $wsevent->getNbMin()){
			$this->manager->unconfirmEvent($wsevent);
		}

		$this->mailer->sendParticipationCanceledToAdmin($wsevent,$participant);

		$this->statistic->fromEvent($event)->update();
	}


}