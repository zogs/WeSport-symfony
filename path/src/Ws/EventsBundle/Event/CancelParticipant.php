<?php

namespace Ws\EventsBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

use Ws\EventsBundle\Entity\Event as WsEvent;
use Ws\StatisticBundle\Manager\StatLogic;
use Ws\StatisticBundle\Manager\EventStatisticInterface;

class CancelParticipant extends Event implements EventStatisticInterface
{
	protected $event;
	protected $user;

	public function __construct(WsEvent $event, UserInterface $user)
	{
		$this->event = $event;
		$this->user = $user;
	}

	//le listener doit avoir acces a l'activité
	public function getEvent()
	{
		return $this->event;
	}

	//le listenr doit pouvoir modifier l'activité
	public function setEvent(WsEvent $event)
	{
		return $this->event = $event;
	}

	// Le listener doit avoir accès à l'utilisateur
	public function getParticipant()
	{
		return $this->user;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function getStatLogics()
	{
		return array(
			new StatLogic('global',$this,+1),
			new StatLogic('user',$this,+1),
			new StatLogic('global',$this,-1,'ws.participant.add'),
			new StatLogic('user',$this,-1,'ws.participant.add'),
			);
	}
}
