<?php

namespace Ws\ConvertSQLBundle\Caller;

use Ws\ConvertSQLBundle\Caller\LocationCaller;

class EventsCaller extends LocationCaller
{
	
	public function setType()
	{
		$db = $this->container->get('doctrine.dbal.oldwesport_connection');
		$stmt = $db->prepare('SELECT * FROM users WHERE user_id='.$this->entry['user_id'].' LIMIT 1');
		$stmt->execute();
		$user = $stmt->fetch();

		if(empty($user)) return 'person';
		if($user['account']=='public') return 'person';
		if($user['account']=='asso') return 'asso';
		if($user['account']=='bizness') return 'pro';
		return 'person';

	}

	public function setOrganizer()
	{

		$user = $this->em->getRepository('MyUserBundle:User')->findOneById($this->entry['user_id']);

		if(NULL===$user) return 'skip';

		return $user;
	}

	public function setSerie()
	{
		$serie = new \Ws\EventsBundle\Entity\Serie();

		if(empty($this->entry['serie_id'])) return $serie;

		$db = $this->container->get('doctrine.dbal.oldwesport_connection');
		$stmt = $db->prepare('SELECT * FROM events_serie WHERE serie_id='.$this->entry['serie_id'].' LIMIT 1');
		$stmt->execute();
		$old = $stmt->fetch();

		if(!empty($old)){
		
			$serie->setStartDate($this->formatDate($old['startdate'],'Y-m-d'));
			$serie->setEndDate($this->formatDate($old['enddate'],'Y-m-d'));
			$serie->setOccurences($old['count']);
			$serie->setType($this->entity->getType());
			$serie->setDateDepot($this->entity->getDateDepot());
			$serie->setMonday($old['Monday']);
			$serie->setTuesday($old['Tuesday']);
			$serie->setWednesday($old['Wednesday']);
			$serie->setThursday($old['Thursday']);
			$serie->setFriday($old['Friday']);
			$serie->setSaturday($old['Saturday']);
			$serie->setSunday($old['Sunday']);
			$serie->setOrganizer($this->entity->getOrganizer());
		}

		return $serie;
	}

	public function setSport()
	{
		$sport = $this->em->getRepository('WsSportsBundle:Sport')->findOneBySlug($this->entry['sport']);

		return $sport;
	}

	public function setOccurence()
	{
		return $this->entity->getSerie()->getOccurences();
	}

	public function setSpot($location_fields)
	{		
		$spot = new \Ws\EventsBundle\Entity\Spot();

		$spot->setAddress($this->entry['address']);
		
		$location = $this->findLocationFromData($location_fields);
		if($location === null) return 'skip';
		if($location->hasCity() === false) return 'skip';
		
		$spot->setLocation($location);

		return $spot;
	}

	public function setLocation()
	{
		if($this->entity->getSpot() !== null && $this->entity->getSpot()->getLocation() !== null){
			return $this->entity->getSpot()->getLocation();
		}

		return 'skip';
	}
}