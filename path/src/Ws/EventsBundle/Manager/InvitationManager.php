<?php

namespace Ws\EventsBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;

use Ws\EventsBundle\Entity\Participation;
use Ws\EventsBundle\Entity\Invited;
use Ws\EventsBundle\Entity\Event;
use My\UserBundle\Entity\User;

use My\UtilsBundle\Utils\String;

class InvitationManager extends AbstractManager
{
	protected $em;

	
	public function saveInvit($invit)
	{		

		//persist Invitation object
		return $this->save($invit,true);

	}

	/**
	 * Save all Invited as been sended at least once
	 */
	public function saveAsSended($invit)
	{
		foreach ($invit->getInvited() as $key => $invited) {
			if($invited->getNbSended() == 0){
				$invited->setNbSended(1);
				$this->save($invited);
			}
		}
		$this->flush();

		return $this;
	}

	public function saveInvited($invited)
	{		

		//persist Invitation object
		return $this->save($invited,true);

	}	

	public function getUserInvitation(User $user,Event $event)
	{
		return $this->em->getRepository('WsEventsBundle:Invitation')->findOneByUserAndEvent($user,$event);
	}

	public function getEventInvitations(Event $event)
	{
		return $this->em->getRepository('WsEventsBundle:Invitation')->findByEvent($event);
	}

}
?>