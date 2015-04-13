<?php

namespace Ws\EventsBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;

use Ws\EventsBundle\Entity\Alert;
use Ws\EventsBundle\Entity\Alerted;
use Ws\EventsBundle\Entity\Follow;
use Ws\EventsBundle\Entity\Event;
use My\UserBundle\Entity\User;

class FollowManager extends AbstractManager
{
	protected $em;
	
	public function saveFollowFromAlert(Alert $alert)
	{		
		$follow = new Follow();
		$follow->setUser($alert->getUser());
		$follow->setOrganizer($alert->getSearch()->getOrganizer());
		$follow->setAlert($alert);

		$this->save($follow);
	}

	public function isEventFollowed(Event $event)
	{
		return $this->isUserFollowed($event->getOrganizer());
	}

	public function isUserFollowed(User $organizer)
	{
		return $this->em->getRepository('WsEventsBundle:Follow')->findOneBy(array(
			'organizer'=> $organizer,
			'user' => $this->getUser(),
			));
	}

	public function deleteFollow(Follow $follow)
	{
		$this->delete($follow);
		return true;
	}

}
?>