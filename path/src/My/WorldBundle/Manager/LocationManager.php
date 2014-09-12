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

	public function getLocationFromNearestCityLatLon($lat,$lon,$countryName = null)
	{
		$countryCode = (isset($countryName))? $this->em->getRepository('MyWorldBundle:Country')->findCodeByCountryName($countryName) : null;

		$cities = $this->em->getRepository('MyWorldBundle:City')->findCitiesArround('10',$lat,$lon,$countryCode,'km');
		$city = $cities[0]; //la ville la plus proche est en début de tableau
		
		return $this->getLocationFromCityId($city->getId());
	}
}
?>