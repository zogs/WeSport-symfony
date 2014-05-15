<?php

namespace Ws\EventsBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;

use Ws\EventsBundle\Entity\Participation;
use Ws\EventsBundle\Entity\Invited;

use My\UtilsBundle\Utils\String;

class InvitationManager extends AbstractManager
{
	protected $em;

	
	public function saveInvit($invit)
	{		

		//persist Invitation object
		$this->save($invit,true);

		return true;	

	}	

}
?>