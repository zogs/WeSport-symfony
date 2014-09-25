<?php

namespace Ws\ConvertSQLBundle\Caller;

use Ws\ConvertSQLBundle\Caller\AbstractCaller;

class ParticipationsCaller extends AbstractCaller
{
	
	public function setEvent()
	{
		$event = $this->em->getRepository('WsEventsBundle:Event')->findOneById($this->entry['event_id']);

		if(empty($event)) return null;

		return $event;
	}

	public function setUser()
	{

		$user = $this->em->getRepository('MyUserBundle:User')->findOneById($this->entry['user_id']);

		if(empty($user)) return null;

		return $user;
	}
}