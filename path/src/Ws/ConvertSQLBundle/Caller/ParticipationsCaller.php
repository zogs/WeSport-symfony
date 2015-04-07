<?php

namespace Ws\ConvertSQLBundle\Caller;

use Ws\ConvertSQLBundle\Caller\AbstractCaller;

class ParticipationsCaller extends AbstractCaller
{	
	public function getEvent()
	{			
		//$qb = $this->em->createQuery('SELECT e FROM Ws\EventsBundle\Entity\Event e WHERE e.id=:id');
		//$qb->setParameter('id',$this->entry['event_id']);
		//$event = $qb->getOneOrNullResult();	
		$event = $this->em->getRepository('WsEventsBundle:Event')->findOneById($this->entry['event_id']);	

		if(empty($event)) return '_skip_';

		return $event;
	}

	public function getUser()
	{		
		//$qb = $this->em->createQuery('SELECT u FROM My\UserBundle\Entity\User u WHERE u.id=:id');
		//$qb->setParameter('id',$this->entry['user_id']);
		//$event = $qb->getOneOrNullResult();	

		$user = $this->em->getRepository('MyUserBundle:User')->findOneById($this->entry['user_id']);	

		if(empty($user)) return '_skip_';

		return $user;
	}
}