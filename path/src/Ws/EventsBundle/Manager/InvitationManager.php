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

	public function saveInvited($invited)
	{		

		//persist Invitation object
		return $this->save($invited,true);

	}	

	public function getUserInvitation(User $user,Event $event)
	{
		return $this->em->getRepository('WsEventsBundle:Invitation')->findUserInvitation($user,$event);
	}

}
?>