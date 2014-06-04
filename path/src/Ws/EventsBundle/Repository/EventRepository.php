<?php

namespace Ws\EventsBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Ws\EventsBundle\Entity\Search;
/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends EntityRepository
{
	private $search;	

	public function findEvents(Search $search)
	{
		$this->search = $search;

		$qb = $this->createQueryBuilder('e');
		$qb->select('e');

		$qb = $this->filterByDate($qb);
		$qb = $this->filterBySports($qb);
		$qb = $this->filterByOnline($qb);		
		$qb = $this->filterByCity($qb);
		$qb = $this->filterByType($qb);
		$qb = $this->filterByTime($qb);
		$qb = $this->filterByPrice($qb);
		$qb = $this->filterByOrganizer($qb);
		$qb = $this->filterByDayOfWeek($qb);
	
		/*
		\My\UtilsBundle\Utils\Debug::debug($search);
		\My\UtilsBundle\Utils\Debug::debug($qb->getParameters());
		\My\UtilsBundle\Utils\Debug::debug($qb->getDQL());
		exit();
		*/
		
		
		return $qb->getQuery()->getResult();
	}


	public function filterByCity($qb)
	{		
		if($this->search->hasLocation() === false || $this->search->getLocation()->hasCity() === false ) return $qb;

		$location = $this->search->getLocation();

		if($this->search->hasArea())
			return $this->filterByArea($qb,$location);
		else
			return $this->filterByLocation($qb,$location);
	}

	public function filterByArea($qb,$location)
	{
		if($this->search->hasArea() === false) return $qb;		

		$onedegree = 111.045;
		$earthradius = 6366.565;
		/* not use, extend_metric is not a param available
		if(!empty($this->params['extend_metric']) && $this->params['extend_metric'] == 'km'){ // in km
			$onedegree = 111.045;
			$earthradius = 6366.565;
		}
		if(!empty($this->params['extend_metric']) && $this->params['extend_metric'] == 'miles'){ // in km
			$onedegree = 69;
			$earthradius = 3956;
		}
		*/

		$cityLat = $location->getCity()->getLatitude();
		$cityLon = $location->getCity()->getLongitude();
		$distance= $this->search->getArea();
		$lon1 = $cityLon-$distance/abs(cos(deg2rad($cityLat))*$onedegree);
		$lon2 = $cityLon+$distance/abs(cos(deg2rad($cityLat))*$onedegree);
		$lat1 = $cityLat-($distance/$onedegree);
		$lat2 = $cityLat+($distance/$onedegree);

		$extend_zone = "$earthradius * 2 * ASIN(SQRT(POWER(SIN(($cityLat - C.latitude) *  pi()/180 / 2), 2) +COS($cityLat * pi()/180) * COS(C.latitude * pi()/180) * POWER(SIN(($cityLon - C.longitude) * pi()/180 / 2), 2) )) as HIDDEN distance";
								
		$qb->select('e',$extend_zone);
		$qb->innerJoin('e.location','L');
		$qb->innerJoin('L.city','C');

		$qb->andWhere('C.longitude BETWEEN '.$lon1.' AND '.$lon2.' AND C.latitude BETWEEN '.$lat1.' AND '.$lat2);


		return $qb;

	}

	public function filterByCityArround($qb,$location)
	{
		if($this->search->hasArea() === false) return $qb;

		//get cities arroud the location
		$cities = $this->_em->getRepository('MyWorldBundle:City')->findCitiesArround(
			$this->search->getArea(),
			$location->getCity()->getLat(),
			$location->getCity()->getLon(),
			$this->search->getCountry()
			);

		//get location of cities
		foreach($cities as $k => $city){
			
			$location = $this->_em->getRepository('MyWorldBundle:Location')->findLocationByCityId($city->getId());
			$locations[] = $location;
			
		}
		
		return $this->filterByLocationArray($qb,$locations);
	}

	public function filterByLocation($qb,$location)
	{				
		$qb->setParameter('location',$location);
		return $qb->andWhere($qb->expr()->eq('e.location',':location'));
	}

	public function filterByLocationArray($qb,$locations)
	{
		$qb->setParameter(':locations',$locations);
		return $qb->andWhere('e.location IN (:locations)');
	}

	public function filterBySports($qb)
	{
		if($this->search->hasSports() === false) return $qb;		

		$qb->setParameter(':sports',$this->search->getSports()); // ex :sports = array(67,68,98);
		return $qb->andWhere('e.sport IN (:sports)');		
	}

	public function filterByType($qb)
	{
		if($this->search->hasType() === false) return $qb;

		$qb->setParameter('type',$this->search->getType());
		return $qb->andWhere('e.type IN (:type)');
	}

	public function filterByDate($qb)
	{
		if($this->search->hasDate() === false) return $qb;
		$qb->setParameter('date',$this->search->getDate());
		return $qb->andWhere(
			$qb->expr()->eq('e.date',':date')
			);		
	}

	public function filterByTime($qb)
	{
		if($this->search->hasTime('start')) $qb->andWhere($qb->expr()->gte('e.time',':timestart'))->setParameter('timestart',$this->search->getTime('start'));
		if($this->search->hasTime('end')) $qb->andWhere($qb->expr()->lte('e.time',':timeend'))->setParameter('timeend',$this->search->getTime('end'));

		return $qb;				
	}

	public function filterByPrice($qb)
	{
		if($this->search->hasPrice() === false) return $qb;
		if($this->search->getPrice() > 0) return $qb->andWhere($qb->expr()->lt('e.price',':price'))->setParameter('price',$this->search->getPrice());
		if($this->search->getPrice() == 0 ) return $qb->andWhere($qb->expr()->eq('e.price',0));
	}

	public function filterByOrganizer($qb)
	{
		if($this->search->hasOrganizer() === false) return $qb;

		return $qb->andWhere($qb->expr()->eq('e.organizer',':organizer'))->setParameter('organizer',$this->search->getOrganizer());		
	}

	public function filterByDayOfWeek($qb)
	{
		if($this->search->hasDayOfWeek() === false) return $qb;

		return $qb->andWhere('DAYNAME(e.date) IN (:days)')->setParameter('days',$this->search->getDayOfWeek());
	}

	public function filterByOnline($qb)
	{
		return $qb->andWhere('e.online = 1');
	}

	public function filterByOffline($qb)
	{
		return $qb->andWhere('e.online = 1');
	}

	public function filterByBothline($qb)
	{
		return $qb->andWhere('e.online = 1 OR e.online = 0');
	}

	




	public function findRecentUniqueEventPosted()
	{
		$series = $this->_em->getRepository('WsEventsBundle:Serie')->findRecentSeriePosted();

		$events = array();
		foreach ($series as $serie) {
			$events[] = $this->findFirstEventOfSerie($serie);
		}

		return $events;
	}	

	public function findFirstEventOfSerie($serie)
	{
		$qb = $this->_em->createQueryBuilder('e');

		$qb->select('e')
			->from('Event','e')
			->where('e.serie',$serie)
			->setMaxResults(1);

		return $qb->getQuery()->getResult();

	}


}
