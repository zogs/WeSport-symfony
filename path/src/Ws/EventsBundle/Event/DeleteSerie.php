<?php

namespace Ws\EventsBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

use Ws\EventsBundle\Entity\Serie as Serie;

class DeleteSerie extends Event
{
	protected $serie;
	protected $user;

	public function __construct(Serie $serie, UserInterface $user)
	{
		$this->serie = $serie;
		$this->user = $user;
	}

	//le listener doit avoir acces a l'activité
	public function getSerie()
	{
		return $this->serie;
	}

	//le listenr doit pouvoir modifier l'activité
	public function setSerie(Serie $serie)
	{
		return $this->serie = $serie;
	}

	// Le listener doit avoir accès à l'utilisateur
	public function getUser()
	{
		return $this->user;
	}
}
