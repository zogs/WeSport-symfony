<?php

namespace Ws\EventsBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;

use Ws\EventsBundle\Entity\Alert;
use Ws\EventsBundle\Entity\Alerted;

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

	public function saveAlerted(Alert $alert, $events)
	{
		$alert->setNbEmails($alert->getNbEmails()+1);
		$alert->setNbEvents($alert->getNbEvents()+count($events));

		//set to inactive if needed
		if($alert->getDateStop() < new \DateTime('now')){
			$alert->setActive(false);
		}

		$this->saveEventsAlerted($events, $alert);

	}
	public function saveEventsAlerted($events, Alert $alert)
	{
		foreach ($events as $k => $event) {
			
			$alerted = new Alerted();
			$alerted->setAlert($alert);
			$alerted->setEvent($event);
			$alerted->setUser($alert->getUser());

			$this->save($alerted);
		}
	}

}
?>