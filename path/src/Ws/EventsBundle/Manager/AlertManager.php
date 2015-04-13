<?php

namespace Ws\EventsBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;

use Ws\EventsBundle\Entity\Alert;
use Ws\EventsBundle\Entity\Alerted;

class AlertManager extends AbstractManager
{
	protected $em;

	
	public function saveAlert(Alert $alert)
	{		
		$start = new \DateTime();
		$alert->setDateStart($start);

		$stop = new \DateTime('+'.$alert->getDuration().' month');
		$alert->setDateStop($stop);

		$alert->getSearch()->setDate('infutur'); // reset search date param to 'none'

		$alert->getSearch()->setAlert($alert);

		$this->save($alert);
		return true;	
	}

	public function extendAlert(Alert $alert,$nbmonth)
	{
		$stop = new \DateTime('+'.$nbmonth.' month');
		$alert->setDateStop($stop);
		$alert->setActive(true);

		$this->save($alert);
		return true;
	}	

	public function deleteAlert(Alert $alert)
	{
		$this->delete($alert);
		return true;
	}

	public function disableAlert(Alert $alert)
	{
		$alert->setActive(false);
		return $this->save($alert);
	}

	public function enableAlert(Alert $alert)
	{
		$alert->setActive(true);
		return $this->save($alert);
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