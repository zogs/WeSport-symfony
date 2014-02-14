<?php

namespace Ws\EventsBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;

use Ws\EventsBundle\Entity\Participation;

class EventManager extends AbstractManager
{
	protected $em;

	public function saveAll($event)
	{
		if($event->getSerie()->getStartDate() && $event->getSerie()->getEndDate() ){
			return $this->saveSerie($event);
		} 
		elseif($event->getDate()){
			$this->saveEvent($event,true);
			$this->saveParticipation($event,$this->getUser(),true);
			return $event;
		}		
	}

	public function saveEvent($event, $flush = false)
	{
		$this->save($event,$flush);	
		return $event;
	}

	public function saveSerie($event)
	{
		$serie = $event->getSerie();
		$start = $serie->getStartDate();
		$end = $serie->getEndDate();
		$end->modify('+1 day');
		$interval = \DateInterval::createFromDateString('1 day');
		$period = new \DatePeriod($start,$interval,$end);
		
		//find occurences
		$occurences = array();
		foreach($period as $day){
			
			$weekday = $day->format('l');

			if( $serie->isWeekdayInSerie($weekday) ){

				$occurence = clone($event);
				$occurence->setDate($day);
				$occurences[] = $occurence;											
			}			
		}

		//save events
		foreach ($occurences as $occurence) {
			$this->saveEvent($occurence);
		}
		//save serie
		$serie->setNboccurence(count($occurences));
		$this->save($occurence,true);

		//save participations
		reset($occurences);
		foreach ($occurences as $occurence) {
			$this->saveParticipation($occurence,$this->getUser());
		}
		$this->flush();

		return true;	
	}

	public function saveParticipation($event,$user,$flush = false)
	{
		$attempt = new Participation();
		$attempt->setEvent($event);
		$attempt->setUser($user);

		$this->save($attempt,$flush);		
		return $attempt;
	}

	public function deleteParticipation($event,$user,$flush = false)
	{
		$part = $this->getRepository('WsEventsBundle:Participation')->findParticipation($event,$user);

		if($part)
			$this->delete($part,$flush);
	}

	public function deleteEvent($event, $flush = false)
	{
		$this->delete($event);
	}

	public function deleteSerie($event)
	{
		$serie = $event->getSerie();
		//delete all events of the serie
		$events = $this->getRepository('WsEventsBundle:Event')->findBySerie($serie);
		foreach ($events as $event) {			
			$this->delete($event);
		}
		//delete serie
		$this->delete($serie,true);

		return true;
	}

	public function confirmEvent($event)
	{
		$event->setConfirmed(1);
		$this->save($event,true);
	}
}
?>