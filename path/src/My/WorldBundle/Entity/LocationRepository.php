<?php

namespace My\WorldBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * LocationRepository
 *
 */
class LocationRepository extends EntityRepository
{
	public function findLocatiosnOfObject($object)
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
		$location = array();
		$location['country'] = $this->_em->getRepository("MyWorldBundle:Country")->findOneById($loc->getCountry());			
		$location['region'] = $this->_em->getRepository('MyWorldBundle:State')->findOneById($loc->getRegion());
		$location['departement'] = $this->_em->getRepository('MyWorldBundle:State')->findOneById($loc->getDepartement());
		$location['district'] = $this->_em->getRepository('MyWorldBundle:State')->findOneById($loc->getDistrict());
		$location['division'] = $this->_em->getRepository('MyWorldBundle:State')->findOneById($loc->getDivision());
		$location['city'] = $this->_em->getRepository('MyWorldBundle:City')->findOneById($loc->getCity());
		
		return $location;
	}

	public function findAllDataFromCode($obj){

		$location = array();

		if(!empty($obj->CC1))
			$location['country'] = $this->_em->getRepository('MyWorldBundle:Country')->findCountryByCode($obj->CC1);

		if(!empty($obj->ADM1))
			$location['region'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCodes($obj->CC1,$obj->ADM1,'ADM1');

		if(!empty($obj->ADM2))
			$location['departement'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCodes($obj->CC1,$obj->ADM2,'ADM2');

		if(!empty($obj->ADM3))
			$location['district'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCodes($obj->CC1,$obj->ADM3,'ADM3');

		if(!empty($obj->ADM4))
			$location['division'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCodes($obj->CC1,$obj->ADM4,'ADM4');

		if(!empty($obj->city))
			$location['city'] = $this->_em->getRepository('MyWorldBundle:City')->findCityByUNI($obj->city);

		return $location;
	}


	public function findStatesListByLocationLevel($location, $level)
	{
		if($level == 'country') {
            $list = $this->_em->getRepository('MyWorldBundle:Country')->findCountryList();
        	$list['level'] = 'country';
        	$list['list'] = $list;
        }
        if($level == 'region')
            $list = $this->findStatesListByCode($location->getCountry()->getCode());
        if($level == 'department')
            $list = $this->findStatesListByCode($location->getCountry()->getCode(),$location->getRegion()->getADMCODE());
        if($level == 'district')
            $list = $this->findStatesListByCode($location->getCountry()->getCode(),$location->getRegion()->getADMCODE(),$location->getDepartement()->getADMCODE());
        if($level == 'division')
            $list = $this->findStatesListByCode($location->getCountry()->getCode(),$location->getRegion()->getADMCODE(),$location->getDepartement()->getADMCODE(),$location->getDistrict()->getADMCODE());
        if($level == 'city')
            $list = $this->findStatesListByCode($location->getCountry()->getCode(),$location->getRegion()->getADMCODE(),$location->getDepartement()->getADMCODE(),$location->getDistrict()->getADMCODE(),$location->getDivision()->getADMCODE());		

        return $list;

	}

	public function findStatesListByCode($countryCode, $regionCode = null, $departementCode = null, $districtCode = null, $divisionCode = null)
	{
		$states = $this->findStatesByCode($countryCode, $regionCode, $departementCode, $districtCode, $divisionCode);

		foreach ($states as $state) {
			$r[$state->getId()] = $state->getName();
		}

		return array(
			'level'=>$states[0]->getLevel(),
			'list'=> $r
			);
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

	/*
	public function findLocationFromStateObject($state)
	{
		$level = $state->getLevel();
		if($level=='country')
			$location = $this->findOneByCountry($state);
		if($level=='region')
			$location = $this->findOneByRegion($state);
		if($level=='departement')
			$location = $this->findOneByDepartement($state);
		if($level=='district')
			$location = $this->findOneByDistrict($state);
		if($level=='division')
			$location = $this->findOneByDivision($state);
		if($level=='city')
			$location = $this->findOneByCity($state);

		return $location;
	}

	*/

	public function findLocationFromOneState($state)
	{
		//find all parents
		$parents = $this->findParentsFromState($state);

		//add current state
		$parents[$state->getLevel()] = $state;
		
		//find the location object
		$location = $this->findLocationFromStates($parents);
		
		return $location;
	}

	public function findLocationFromStates($states)
	{
		$qb = $this->_em->createQueryBuilder('l');
		
		$qb->select('l')
			->from($this->_entityName,'l');			

		if(isset($states['country']) && is_object($states['country']) && $states['country']->exist()) 
			$qb->andWhere($qb->expr()->eq('l.country',$states['country']->getId()));
		elseif(!empty($states['country']) && is_numeric($states['country']))
			$qb->andWhere($qb->expr()->eq('l.country',$states['country']));
		else
			$qb->andWhere($qb->expr()->isNull('l.country'));
		
		if(isset($states['region']) && is_object($states['region']) && $states['region']->exist()) 
			$qb->andWhere($qb->expr()->eq('l.region',$states['region']->getId()));
		elseif(!empty($states['region']) && is_numeric($states['region']))
			$qb->andWhere($qb->expr()->eq('l.region',$states['region']));
		else
			$qb->andWhere($qb->expr()->isNull('l.region'));

		if(isset($states['department']) && is_object($states['department']) && $states['department']->exist()) 
			$qb->andWhere($qb->expr()->eq('l.departement',$states['department']->getId()));
		elseif(!empty($states['department']) && is_numeric($states['department']))
			$qb->andWhere($qb->expr()->eq('l.departement',$states['department']));
		else
			$qb->andWhere($qb->expr()->isNull('l.departement'));

		if(isset($states['district']) && is_object($states['district']) && $states['district']->exist()) 
			$qb->andWhere($qb->expr()->eq('l.district',$states['district']->getId()));
		elseif(!empty($states['district']) && is_numeric($states['district']))
			$qb->andWhere($qb->expr()->eq('l.district',$states['district']));
		else
			$qb->andWhere($qb->expr()->isNull('l.district'));

		if(isset($states['division']) && is_object($states['division']) && $states['division']->exist()) 
			$qb->andWhere($qb->expr()->eq('l.division',$states['division']->getId()));
		elseif(!empty($states['division']) && is_numeric($states['division']))
			$qb->andWhere($qb->expr()->eq('l.division',$states['division']));
		else
			$qb->andWhere($qb->expr()->isNull('l.division'));

		if(isset($states['city']) && is_object($states['city']) && $states['city']->exist()) 
			$qb->andWhere($qb->expr()->eq('l.city',$states['city']->getId()));
		elseif(!empty($states['city']) && is_numeric($states['city']))
			$qb->andWhere($qb->expr()->eq('l.city',$states['city']));
		else
			$qb->andWhere($qb->expr()->isNull('l.city'));
		

		$location = $qb->getQuery()->getOneOrNullResult();

		if(empty($location)){
			$location = $this->createLocation($states);
		}

		return $location;
	}

	public function findParentsFromState($state)
	{
		while($state){
			$state = $this->findParentFromState($state);
			$parents[$state->getLevel()] = $state;
			if($state->getLevel()=='country') break;			
		}
		return $parents;
	}

	public function findParentFromState($state)
	{
		$level = $state->getLevel();		
		if($level=='country')
			$parent = $state; //have no parent, return false;

		elseif($level=='city'){
			$parent = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($state->getCC1(),$state->getLastADM(),$state->getLastADMLevel());			
		}
		else{
			$ADM = $state->getDSG();
			if($ADM=='ADM4')
				$parent = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($state->getCC1(),$state->getADMPARENT(),'ADM3');
			if($ADM=='ADM3')
				$parent = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($state->getCC1(),$state->getADMPARENT(),'ADM2');
			if($ADM=='ADM2')
				$parent = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($state->getCC1(),$state->getADMPARENT(),'ADM1');
			if($ADM=='ADM1')
				$parent = $this->_em->getRepository('MyWorldBundle:Country')->findCountryByCode($state->getCC1());
		}

		return $parent;
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
		$states['city'] = $city;
		$states['country'] = $this->_em->getRepository('MyWorldBundle:Country')->findCountryByCode($city->getCC1());
		if($city->getADM1() != null) $states['region'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($city->getCC1(),$city->getADM1(),'ADM1');
		if($city->getADM2() != null) $states['departement'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($city->getCC1(),$city->getADM2(),'ADM2');
		if($city->getADM3() != null) $states['district'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($city->getCC1(),$city->getADM3(),'ADM3');
		if($city->getADM4() != null) $states['division'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($city->getCC1(),$city->getADM4(),'ADM4');

		return $this->createLocation($states);
	}

	/**
	 * create location from states
	 * @param associative array of states 
	 */
	public function createLocation($states)
	{
		$location = new Location();

		if(isset($states['country']))
			$location->setCountry($states['country']);
		if(isset($states['region']))
			$location->setRegion($states['region']);
		if(isset($states['department']))
			$location->setDepartement($states['department']);
		if(isset($states['district']))
			$location->setDistrict($states['district']);
		if(isset($states['division']))
			$location->setDivision($states['division']);
		if(isset($states['city']))
			$location->setCity($states['city']);

		$this->_em->getConnection()->executeUpdate("SET FOREIGN_KEY_CHECKS=0;"); 
		$this->_em->persist($location);
		$this->_em->flush();
		$this->_em->getConnection()->executeUpdate("SET FOREIGN_KEY_CHECKS=1;"); 

		return $location;

	}

	/*
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
	*/

	public function findStateById($level,$id)
	{
		if($level=='country')
			return $this->_em->getRepository('MyWorldBundle:Country')->findByCodeOrId($id);
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
		if($level=='city')
			return null; //no child
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
