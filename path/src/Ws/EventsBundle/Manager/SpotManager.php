<?php

namespace Ws\EventsBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;

use Ws\EventsBundle\Entity\Spot;

class SpotManager extends AbstractManager
{
	protected $em;

	
	public function saveSpot(Spot $spot)
	{		
		$this->save($spot,true);
		return $spot;	
	}

	public function deleteSpot(Spot $spot)
	{
		$this->delete($spot,true);
		return true;
	}

}
?>