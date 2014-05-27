<?php

namespace Ws\EventsBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ViewCalendar extends Event
{
	protected $search;
	protected $user;

	public function __construct($search, $user)
	{
		$this->search = $search;
		$this->user = $user;
	}

	public function getSearch()
	{
		return $this->search;
	}

	// Le listener doit avoir accès à l'utilisateur
	public function getUser()
	{
		return $this->user;
	}
}
