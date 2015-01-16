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

	/**
	 * Return the nearest city Location from latitude and longitude
	 *
	 *@param number $lat
	 *@param number $lon
	 *@param (option) string $countryName
	 *
	 *@return object Location
	 */
	public function getLocationFromNearestCityLatLon($lat,$lon,$countryName = null)
	{
		//get country code
		$countryCode = (isset($countryName))? $this->em->getRepository('MyWorldBundle:Country')->findCodeByCountryName($countryName) : null;

		$cities = $this->em->getRepository('MyWorldBundle:City')->findCitiesArround(2,$lat,$lon,$countryCode,'km');

		//look for cities in a radius of 1km, and multiply radius per 2 if no result, until result, within 100km maximum
		for($i=1;$i<=100;$i++){
			$cities = $this->em->getRepository('MyWorldBundle:City')->findCitiesArround($i,$lat,$lon,$countryCode,'km');
			//return the Location object from the city id	
			if(isset($cities[0])) return $this->getLocationFromCityId($cities[0]->getId());
		}
	}
}
?>