<?php

namespace Ws\EventsBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;


class AlertManager extends AbstractManager
{
	protected $em;

	
	public function saveAlert($alert)
	{		
		$start = new \DateTime();
		$alert->setDateStart($start);

		$stop = new \DateTime('+'.$alert->getDuration().' month');
		$alert->setDateStop($stop);

		$alert->getSearch()->setDate('none');

		$this->save($alert,true);
		return true;	
	}	

	public function deleteAlert($alert)
	{
		$this->delete($alert,true);
		return true;
	}

}
?>