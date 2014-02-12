<?php

namespace My\WorldBundle\Manager;

use Doctrine\ORM\EntityManager;
use Ws\WorldBundle\Entity\Location;

class LocationManager 
{
	protected $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function getLocationFromCityArray($array)
	{
		if(!empty($array['city_id']))
			$location = $this->getLocationFromCityId($array['city_id']);
		elseif(!empty($array['city_name']))
			$location = $this->getLocationFromCityName($array['city_name']);

		return (!empty($location))? $location : NULL;
	}

	public function getLocationFromCityId($id)
	{
		return $this->em->getRepository('MyWorldBundle:Location')->findLocationByCityId($id);
	}

	public function getLocationFromCityName($name)
	{
		$city = $this->em->getRepository('MyWorldBundle:City')->findCityByName($name);

		return $this->getLocationFromCityId($city->getId()); 
	}
}
?>