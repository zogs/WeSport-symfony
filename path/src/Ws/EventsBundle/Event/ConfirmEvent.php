<?php

namespace Ws\EventsBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Ws\EventsBundle\Entity\Event as WsEvent;

class ConfirmEvent extends Event
{
	protected $event;
	protected $user;

	public function __construct(WsEvent $event, $user)
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
	public function getUser()
	{
		return $this->user;
	}

	public function getStatLogics()
	{
		return array(			
			new StatLogic('global',$this,+1),
			new StatLogic('user',$this,+1),
			);
	}
}
