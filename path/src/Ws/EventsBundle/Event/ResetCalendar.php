<?php

namespace Ws\EventsBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ResetCalendar extends Event
{
	protected $user;

	public function __construct($user)
	{
		$this->user = $user;	
	}


	// Le listener doit avoir accès à l'utilisateur
	public function getUser()
	{
		return $this->user;
	}
}
