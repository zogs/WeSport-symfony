<?php

namespace Ws\MailerBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;
use Ws\MailerBUndle\Entity\Settings;

class SettingsManager extends AbstractManager
{
	protected $em;
	protected $user;

	public function setUser()
	{
		$this->user = $user;
	}


	public function saveSettings($settings)
	{		
		
		$this->save($settings,true);
		return true;	
	}	

	
}
?>