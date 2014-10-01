<?php

namespace Ws\EventsBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Ws\EventsBundle\Entity\Invitation;

class CreateInvitation extends Event
{
	protected $invitation;
	protected $user;

	public function __construct(Invitation $invitation, $user)
	{
		$this->invitation = $invitation;
		$this->user = $user;
	}

	//le listener doit avoir acces a l'activité
	public function getInvitation()
	{
		return $this->invitation;
	}

	//le listenr doit pouvoir modifier l'activité
	public function setInvitation(Invitation $invitation)
	{
		return $this->invitation = $invitation;
	}

	// Le listener doit avoir accès à l'utilisateur
	public function getUser()
	{
		return $this->user;
	}
}
