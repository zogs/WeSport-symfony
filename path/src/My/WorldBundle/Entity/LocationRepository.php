<?php

namespace My\WorldBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * LocationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LocationRepository extends EntityRepository
{
	public function findWorldLocationOf($object)
	{

		//Si l'object a une methode getLocation
		if(in_array('getLocation',get_class_methods($object))){

			$location = $object->getLocation();

			//Si cest une class de Location
			if($location instanceof Location){

				return $this->findAllDataFromLocation($location);

			}
			else{
				exit('not instanceof Location');
			}
		}
		else{
			
			return $this->findAllDataFromCode($object);
		}

		return array();

	}

	public function findAllDataFromLocation(Location $loc)
	{
		$locations = array();
		$locations['country'] = $this->_em->getRepository("MyWorldBundle:Country")->findOneById($loc->getCountry());			
		$locations['region'] = $this->_em->getRepository('MyWorldBundle:State')->findOneById($loc->getRegion());
		$locations['departement'] = $this->_em->getRepository('MyWorldBundle:State')->findOneById($loc->getDepartement());
		$locations['district'] = $this->_em->getRepository('MyWorldBundle:State')->findOneById($loc->getDistrict());
		$locations['division'] = $this->_em->getRepository('MyWorldBundle:State')->findOneById($loc->getDivision());
		$locations['city'] = $this->_em->getRepository('MyWorldBundle:City')->findOneById($loc->getCity());
		
		return $locations;
	}

	public function findAllDataFromCode($obj){

		$locations = array();

		if(!empty($obj->CC1))
			$locations['country'] = $this->_em->getRepository('MyWorldBundle:Country')->findCountryByCode($obj->CC1);

		if(!empty($obj->ADM1))
			$locations['region'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCodes($obj->CC1,$obj->ADM1,'ADM1');

		if(!empty($obj->ADM2))
			$locations['departement'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCodes($obj->CC1,$obj->ADM2,'ADM2');

		if(!empty($obj->ADM3))
			$locations['district'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCodes($obj->CC1,$obj->ADM3,'ADM3');

		if(!empty($obj->ADM4))
			$locations['division'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCodes($obj->CC1,$obj->ADM4,'ADM4');

		if(!empty($obj->city))
			$locations['city'] = $this->_em->getRepository('MyWorldBundle:City')->findCityByUNI($obj->city);

		return $locations;
	}

	public function findStatesByCode($countryCode, $regionCode = null, $departementCode = null, $districtCode = null, $divisionCode = null)
	{
		$states = array();

		if(!empty($districtCode))
			$states = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM4',$countryCode, $districtCode);

		elseif(!empty($departementCode))
			$states = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM3',$countryCode, $departementCode);
		
		elseif(!empty($regionCode))
			$states = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM2',$countryCode, $regionCode);

		elseif(!empty($countryCode))
			$states = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM1',$countryCode);

		if(empty($states))
			$states = $this->_em->getRepository('MyWorldBundle:City')->findCitiesByCode($countryCode, $regionCode, $departementCode, $districtCode, $divisionCode);

		return $states;
	}

	public function findStatesByParent($countryCode, $level, $parentCode = null)
	{
		$states = array();

		if($level == 'ADM4')
			$states = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM4',$countryCode, $parentCode);

		elseif($level == 'ADM3')
			$states = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM3',$countryCode, $parentCode);
		
		elseif($level == 'ADM2')
			$states = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM2',$countryCode, $parentCode);

		elseif($level == 'ADM1')
			$states = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM1',$countryCode);

		if(empty($states))
			$states = $this->_em->getRepository('MyWorldBundle:City')->findCitiesByParent($countryCode, $regionCode, $departementCode, $districtCode, $divisionCode);

		return $states;
	}

	/**
	 * find location from city name 
	 * @param string $name
	 * @param string $countryCode
	 */
	public function findLocationByCityName($name,$countryCode = null)
	{
		$city = $this->_em->getRepository('MyWorldBundle:City')->findCityByName($name,$countryCode);

		return $this->findLocationByCityId($city->getId());
	}

	/**
	 * find location from city ID
	 * if not exist create it
	 * @param integer id
	 */
	public function findLocationByCityId($id)
	{

		if($location = $this->findOneByCity($id)){
			return $location;
		}
		else {

			return $this->createLocationFromCityId($id);	
		}
	}

	/**
	 * create location from a city
	 * @param integer id
	 */
	public function createLocationFromCityId($id)
	{
		$city = $this->_em->getRepository('MyWorldBundle:City')->findOneById($id);
		$country = $this->_em->getRepository('MyWorldBundle:Country')->findCountryByCode($city->getCC1());
		$region = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($city->getCC1(),$city->getADM1(),'ADM1');
		$departement = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($city->getCC1(),$city->getADM2(),'ADM2');
		$district = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($city->getCC1(),$city->getADM3(),'ADM3');
		$division = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($city->getCC1(),$city->getADM4(),'ADM4');

		return $this->createLocation(
			$country,
			$region,
			$departement,
			$district,
			$division,
			$city
			);
	}

	/**
	 * create location from all data
	 * @param object country $country
	 * @param object state $region
	 * @param object state $departement
	 * @param object state $district
	 * @param object state $division
	 * @param object city $city
	 */
	public function createLocation($country,$region,$departement = null,$district = null,$division = null,$city = null)
	{
		$location = new Location();

		if($country->exist())	
			$location->setCountry($country);
		if($region->exist()) 
			$location->setRegion($region);
		if($departement->exist()) 
			$location->setDepartement($departement);
		if($district->exist()) 
			$location->setDistrict($district);
		if($division->exist()) 
			$location->setDivision($division);
		if($city->exist()) 
			$location->setCity($city);

		$this->_em->getConnection()->executeUpdate("SET FOREIGN_KEY_CHECKS=0;"); 
		$this->_em->persist($location);
		$this->_em->flush();
		$this->_em->getConnection()->executeUpdate("SET FOREIGN_KEY_CHECKS=1;"); 

		return $location;
	}


	public function findStateById($level,$id)
	{
		if($level=='country')
			return $this->_em->getRepository('MyWorldBundle:Country')->findOneById($id);
		if($level=='region')
			return $this->_em->getRepository('MyWorldBundle:State')->findOneById($id);
		if($level=='department')
			return $this->_em->getRepository('MyWorldBundle:State')->findOneById($id);
		if($level=='district')
			return $this->_em->getRepository('MyWorldBundle:State')->findOneById($id);
		if($level=='division')
			return $this->_em->getRepository('MyWorldBundle:State')->findOneById($id);
		if($level=='city')
			return $this->_em->getRepository('MyWorldBundle:City')->findOneById($id);
	}

	public function findChildrenStatesByParent($parent)
	{

		$level = $parent->getLevel();
		if($level=='country')
			$children = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM1',$parent->getCode(),'');
		if($level=='region')
			$children = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM2',$parent->getCC1(),$parent->getADMCODE());
		if($level=='department')
			$children = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM3',$parent->getCC1(),$parent->getADMCODE());
		if($level=='district')
			$children = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM4',$parent->getCC1(),$parent->getADMCODE());
		if($level=='division')
			$children = $this->_em->getRepository('MyWorldBundle:City')->findCitiesByStateParent($parent);

		if(empty($children))
			$children = $this->_em->getRepository('MyWorldBundle:City')->findCitiesByStateParent($parent);
		

		return $children;
	}
	
}
