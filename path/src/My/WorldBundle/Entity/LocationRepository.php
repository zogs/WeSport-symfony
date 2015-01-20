<?php

namespace My\WorldBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityNotFoundException;

use My\WorldBundle\Entity\City;
use My\WorldBundle\Entity\Location;
/**
 * LocationRepository
 *
 */
class LocationRepository extends EntityRepository
{

	/**
	 * find Location from city name 
	 * (if not finded, create it)
	 * @param string $name
	 * @param string $countryCode
	 * @return Location
	 */
	public function findLocationByCityName($name,$countryCode = null)
	{
		$city = $this->_em->getRepository('MyWorldBundle:City')->findCityByName($name,$countryCode);

		if(NULL !== $city)
			return $this->findLocationByCityId($city->getId());
		else
			return NULL;
	}

	/**
	 * find Location from city ID
	 * (if not exist create it)
	 * @param integer id
	 */
	public function findLocationByCityId($id)
	{

		if($location = $this->findOneByCity($id)){

			return $location;
		}
		else {
			$city = $this->_em->getRepository('MyWorldBundle:City')->findOneById($id);
			return $this->createLocationFromCity($city);	
		}
	}

	/**
	 * find Location of a country by its code
	 * (create it if not find)
	 *
	 * @param string $code : 2 caracters database code
	 * @return object Location
	 */
	public function findLocationByCountryCode($code)
	{
		$country = $this->_em->getRepository('MyWorldBundle:Country')->findCountryByCode($code);
		if($location = $this->findOneByCountry($code)){
			return $location;
		}
		else {
			return $this->createLocation(array(
				'country'=>$country
				));
		}
	}

	/**
	 * Create Location from a city
	 *
	 * @param object City
	 * @return object Location
	 */
	public function createLocationFromCity(City $city)
	{
		$states['city'] = $city;
		$states['country'] = $this->_em->getRepository('MyWorldBundle:Country')->findCountryByCode($city->getCc1());
		if($city->getAdm1() != null) $states['region'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($city->getCc1(),$city->getAdm1(),'ADM1');
		if($city->getAdm2() != null) $states['departement'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($city->getCc1(),$city->getAdm2(),'ADM2');
		if($city->getAdm3() != null) $states['district'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($city->getCc1(),$city->getAdm3(),'ADM3');
		if($city->getAdm4() != null) $states['division'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($city->getCc1(),$city->getAdm4(),'ADM4');

		return $this->createLocation($states);
	}


	/**
	 * Create a Location object from an array of states level
	 *
	 * @param associative array of states (ex: array('country'=>Object:Country,'region'=>Object:State,etc...))
	 * @return object Location
	 */
	public function createLocation($states)
	{
		$location = new Location();

		if(isset($states['country']))
			$location->setCountry($this->_em->getRepository('MyWorldBundle:Country')->find($states['country']));
		if(isset($states['region']))
			$location->setRegion($this->_em->getRepository('MyWorldBundle:State')->find($states['region']));
		if(isset($states['departement']))
			$location->setDepartement($this->_em->getRepository('MyWorldBundle:State')->find($states['departement']));
		if(isset($states['district']))
			$location->setDistrict($this->_em->getRepository('MyWorldBundle:State')->find($states['district']));
		if(isset($states['division']))
			$location->setDivision($this->_em->getRepository('MyWorldBundle:State')->find($states['division']));
		if(isset($states['city']))
			$location->setCity($this->_em->getRepository('MyWorldBundle:City')->find($states['city']));

		$this->_em->getConnection()->executeUpdate("SET FOREIGN_KEY_CHECKS=0;"); 
		$this->_em->persist($location);
		$this->_em->flush();
		$this->_em->getConnection()->executeUpdate("SET FOREIGN_KEY_CHECKS=1;"); 

		return $location;
	}

	/**
	 * Return a state level object by its id
	 *
	 * @param string level
	 * @param integer id
	 * @return state level (Country object OR City object OR State object)
	 */
	public function findStateById($level,$id)
	{
		if($level=='country')
			return $this->_em->getRepository('MyWorldBundle:Country')->findByCodeOrId($id);
		if($level=='region')
			return $this->_em->getRepository('MyWorldBundle:State')->findOneById($id);
		if($level=='departement')
			return $this->_em->getRepository('MyWorldBundle:State')->findOneById($id);
		if($level=='district')
			return $this->_em->getRepository('MyWorldBundle:State')->findOneById($id);
		if($level=='division')
			return $this->_em->getRepository('MyWorldBundle:State')->findOneById($id);
		if($level=='city')
			return $this->_em->getRepository('MyWorldBundle:City')->findOneById($id);
	}


	/**
	 * Return list of children states of a parent
	 *
	 * @param object City|State|Country
	 *
	 * @return array of state
	 */
	public function findChildrenStatesByParent($parent)
	{

		$level = $parent->getLevel();
		if($level=='city')
			return null; //no child
		if($level=='country')
			$children = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM1',$parent->getCode(),'');
		if($level=='region')
			$children = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM2',$parent->getCc1(),$parent->getAdmCode());
		if($level=='departement')
			$children = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM3',$parent->getCc1(),$parent->getAdmCode());
		if($level=='district')
			$children = $this->_em->getRepository('MyWorldBundle:State')->findStatesByParent('ADM4',$parent->getCc1(),$parent->getAdmCode());
		if($level=='division')
			$children = $this->_em->getRepository('MyWorldBundle:City')->findCitiesByStateParent($parent);

		if(empty($children))
			$children = $this->_em->getRepository('MyWorldBundle:City')->findCitiesByStateParent($parent);
		

		return $children;
	}

	/**
	 * Find all states from an array (or object) that contains cc1|ADM1|ADM2|ADM3|ADM4|city fields
	 *
	 *@param array|object $obj
	 *@return array of different level of locations
	 */
	public function findStatesFromCodes($obj){

		if(is_array($obj)) {
			$obj = (object) $obj;
		}
		//important// make CC1 as cc1
		if(!empty($obj->CC1)) $obj->cc1 = $obj->CC1;
		
		$location = array();

		if(empty($obj->cc1) || trim($obj->cc1) == '') return null;

		if(isset($obj->cc1) && !empty($obj->cc1) && trim($obj->cc1) != '')
			$location['country'] = $this->_em->getRepository('MyWorldBundle:Country')->findCountryByCode($obj->cc1);

		if(isset($obj->ADM1) && !empty($obj->ADM1) && trim($obj->ADM1) != '')
			$location['region'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($obj->cc1,$obj->ADM1,'ADM1');

		if(isset($obj->ADM2) && !empty($obj->ADM2) && trim($obj->ADM2) != '')
			$location['departement'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($obj->cc1,$obj->ADM2,'ADM2');

		if(isset($obj->ADM3) && !empty($obj->ADM3) && trim($obj->ADM3) != '')
			$location['district'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($obj->cc1,$obj->ADM3,'ADM3');

		if(isset($obj->ADM4) && !empty($obj->ADM4) && trim($obj->ADM4) != '')
			$location['division'] = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($obj->cc1,$obj->ADM4,'ADM4');

		if(isset($obj->city) && !empty($obj->city) && trim($obj->city) != '')
			$location['city'] = $this->_em->getRepository('MyWorldBundle:City')->findCityByUNI($obj->city);

		return $location;
	}

	/**
	 * Return array of states level from a parent 
	 *
	 * @param string $coutryCode the two-caracter country code
	 * @param string $level the level code that we want to retrieve (ex ADM2)
	 * @param string $parentCode the code of the parent
	 *
	 * @return array of State|City
	 */
	public function findStatesByParentCode($countryCode, $level, $parentCode = null)
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
	 * Return array of states from codes
	 *
	 * @param string $countryCode
	 * @param string $regionCode
	 * @param string $departementCode
	 * @param string $districtCode
	 * @param string $divisionCode
	 *
	 * @return array 
	 */
	public function findStatesByCodes($countryCode, $regionCode = null, $departementCode = null, $districtCode = null, $divisionCode = null)
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

	/**
	 * Return formatted array of level id=>name
	 * ex: array(
	 *	'level'=>'departement',
	 *	'list'=>array(
	 *		id=>name,
	 *	))
	 *
	 * @param string $countryCode
	 * @param string $regionCode
	 * @param string $departementCode
	 * @param string $districtCode
	 * @param string $divisionCode
	 *
	 * @return array
	 */
	public function findStatesListByCodes($countryCode, $regionCode = null, $departementCode = null, $districtCode = null, $divisionCode = null)
	{
		$states = $this->findStatesByCodes($countryCode, $regionCode, $departementCode, $districtCode, $divisionCode);

		foreach ($states as $state) {
			$r[$state->getId()] = $state->getName();
		}

		return array(
			'level'=>$states[0]->getLevel(),
			'list'=> $r
			);
	}

	public function findStatesListByLevel($location, $level)
	{
		if($level == 'country') {
			$list = $this->_em->getRepository('MyWorldBundle:Country')->findCountryList();
			$list['level'] = 'country';
			$list['list'] = $list;
		}
		elseif($level == 'region')
			$list = $this->findStatesListByCodes($location->getCountry()->getCode());
		elseif($level == 'departement')
			$list = $this->findStatesListByCodes($location->getCountry()->getCode(),$location->getRegion()->getAdmCode());
		elseif($level == 'district')
			$list = $this->findStatesListByCodes($location->getCountry()->getCode(),$location->getRegion()->getAdmCode(),$location->getDepartement()->getAdmCode());
		elseif($level == 'division')
			$list = $this->findStatesListByCodes($location->getCountry()->getCode(),$location->getRegion()->getAdmCode(),$location->getDepartement()->getAdmCode(),$location->getDistrict()->getAdmCode());
		elseif($level == 'city')
			$list = $this->findStatesListByCodes($location->getCountry()->getCode(),$location->getRegion()->getAdmCode(),$location->getDepartement()->getAdmCode(),$location->getDistrict()->getAdmCode(),$location->getDivision()->getAdmCode());		
		else
			throw new Exception("Level is not correctly defined", 1);
		

		return $list;

	}



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

		if(isset($states['departement']) && is_object($states['departement']) && $states['departement']->exist()) 
			$qb->andWhere($qb->expr()->eq('l.departement',$states['departement']->getId()));
		elseif(!empty($states['departement']) && is_numeric($states['departement']))
			$qb->andWhere($qb->expr()->eq('l.departement',$states['departement']));
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
			$parent = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($state->getCc1(),$state->getLastAdm(),$state->getLastAdmLevel());			
		}
		else{
			$adm = $state->getDsg();
			if($adm=='ADM4')
				$parent = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($state->getCc1(),$state->getAdmParent(),'ADM3');
			if($adm=='ADM3')
				$parent = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($state->getCc1(),$state->getAdmParent(),'ADM2');
			if($adm=='ADM2')
				$parent = $this->_em->getRepository('MyWorldBundle:State')->findStateByCode($state->getCc1(),$state->getAdmParent(),'ADM1');
			if($adm=='ADM1')
				$parent = $this->_em->getRepository('MyWorldBundle:Country')->findCountryByCode($state->getCc1());
		}

		return $parent;
	}


	
}
