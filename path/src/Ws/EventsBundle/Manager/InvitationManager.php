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
		//set Invited from emails list
		//will be persisted with Invitation object
		$invit = $this->setInvited($invit);		

		//persist Invitation object
		$this->save($invit,true);

		return true;	

	}

	private function setInvited($invit)
	{		
		$emails = $invit->getEmails();

		if(empty($emails)) return $invit;

		$emails = String::findEmailsInString($emails);

		foreach ($emails as $key => $email) {
			
			$o = new Invited();
			$o->setEmail($email);
			$o->setInvitation($invit);

			$invit->addInvited($o);
		}

		$invit->setEmails(null);

		return $invit;
	}

}
?>