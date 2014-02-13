<?php

namespace Ws\EventsBundle\Manager;

use Doctrine\ORM\EntityManager;
use Ws\SportsBundle\Entity\Event;
use Ws\SportsBundle\Entity\Serie;
use Ws\SportsBundle\Entity\EventParticipants;

class EventManager 
{
	protected $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function persistEvent($event)
	{
		$this->em->persist($event);
		return $event;
	}

	public function persistSerie($serie)
	{
		$this->em->persist($serie);
		return $serie;
	}

	public function saveSerie($event)
	{
		$serie = $event->getSerie();

		$start = $serie->getStartDate();
		$end = $serie->getEndDate();
		$end->modify('+1 day');

		$interval = \DateInterval::createFromDateString('1 day');
		$period = new \DatePeriod($start,$interval,$end);
		
		$nb = 0;
		foreach($period as $day){
			
			$weekday = $day->format('l');

			if( $serie->isWeekdayInSerie($weekday) ){

				$occurence = clone($event);
				$occurence->setDate($day);
				
				$this->em->getRepository('WsEventsBundle:Event')->persistEvent($occurence);
				$nb++;				
			}			
		}

		$serie->setNboccurence($nb);
		$this->em->getRepository('WsEventsBundle:Serie')->persistAndFlush($occurence);

		return true;	
	}

	public function saveParticipant($event,$user)
	{
		$attempt = new EventParticipants();
		$attempt->setEvent($event);
		$attempt->setUser($user);

		$this->em->getRepository('WsEventsBundle:EventParticipants')->persistAndFlush($attempt);		

		return true;
	}
}
?>