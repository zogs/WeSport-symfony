<?php

namespace Ws\EventsBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

use Ws\EventsBundle\Entity\Alert;
use Ws\EventsBundle\Event\WsEvents;

class CreateAlert extends Event
{
	protected $alert;
	protected $user;

	public function __construct(Alert $alert, UserInterface $user)
	{
		$this->alert = $alert;
		$this->user = $user;
	}

	//le listener doit avoir acces a l'activité
	public function getAlert()
	{
		return $this->alert;
	}

	//le listenr doit pouvoir modifier l'activité
	public function setAlert(Alert $alert)
	{
		return $this->alert = $alert;
	}

	// Le listener doit avoir accès à l'utilisateur
	public function getUser()
	{
		return $this->user;
	}

	public function getStatLogic()
	{
		return array(
			array('global',WsEvents::ALERT_NEW,+1),
			array('user',WsEvents::ALERT_NEW,+1),
			);
	}
}
