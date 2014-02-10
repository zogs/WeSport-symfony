<?php

namespace My\WorldBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
/**
 * CityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CityRepository extends EntityRepository
{
	public function findCityByUNI($uni)
	{
		$qb = $this->createQueryBuilder('c');
		$qb->where(
			$qb->expr()->eq('c.UNI',':UNI')
			);
		$qb->setParameter('UNI',$uni);
		
		return $qb->getQuery()->getSingleResult();

	}

	public function findCityByName($name,$countryCode = null, $regionCode = null, $departementCode = null, $districtCode = null, $divisionCode = null)
	{
		$qb = $this->createQueryBuilder('c');

		if(isset($countryCode))
			$qb->andWhere($qb->expr()->eq('c.CC1',$qb->expr()->literal($countryCode)));
		if(isset($regionCode))
			$qb->andWhere($qb->expr()->eq('c.ADM1',$qb->expr()->literal($regionCode)));
		if(isset($departementCode))
			$qb->andWhere($qb->expr()->eq('c.ADM2',$qb->expr()->literal($departementCode)));
		if(isset($districtCode))
			$qb->andWhere($qb->expr()->eq('c.ADM3',$qb->expr()->literal($districtCode)));
		if(isset($divisionCode))
			$qb->andWhere($qb->expr()->eq('c.ADM4',$qb->expr()->literal($divisionCode)));

		$qb->andWhere(
			$qb->expr()->eq('c.FULLNAMEND',$qb->expr()->literal($name)));
		$qb->setMaxResults(1);

		return $qb->getQuery()->getSingleResult();
	}
	public function findCitiesSuggestions( $limit, $prefix , $countryCode = null, $regionCode = null, $departementCode = null, $districtCode = null, $divisionCode = null){

		$qb = $this->createQueryBuilder('c');

		if(isset($countryCode))
			$qb->andWhere($qb->expr()->eq('c.CC1',$qb->expr()->literal($countryCode)));			
		if(isset($regionCode))
			$qb->andWhere($qb->expr()->eq('c.ADM1',$qb->expr()->literal($regionCode)));			
		if(isset($departementCode))
			$qb->andWhere($qb->expr()->eq('c.ADM2',$qb->expr()->literal($departementCode)));
		if(isset($districtCode))
			$qb->andWhere($qb->expr()->eq('c.ADM3',$qb->expr()->literal($districtCode)));
		if(isset($divisionCode))
			$qb->andWhere($qb->expr()->eq('c.ADM4',$qb->expr()->literal($divisionCode)));			

		$qb->andWhere(
				$qb->expr()->like('c.FULLNAMEND',$qb->expr()->literal($prefix.'%'))
			);

		$qb->setMaxResults( $limit );
		$qb->orderBy('c.FULLNAMEND','ASC');

		return $qb->getQuery()->getResult();
	}


	public function findCitiesByCode($countryCode, $regionCode = null, $departementCode = null, $districtCode = null, $divisionCode = null)
	{
		$sql = "
			SELECT s
			FROM MyWorldBundle:City s 
			JOIN MyWorldBundle:Country c 
			WITH c.code = s.CC1
			WHERE s.CC1 = :CC1
			AND (
				";
		if(isset($regionCode))
			$sql .= " s.ADM1 = :ADM1 ";
		if(isset($departementCode))
			$sql .= " AND s.ADM2 = :ADM2 ";
		if(isset($districtCode))
			$sql .= " AND s.ADM3 = :ADM3 ";
		if(isset($divisionCode))
			$sql .= " AND s.ADM4 = :ADM4 ";
		$sql .="
			)
			AND (
				s.LC = c.lang
				OR
				s.LC = ''
				)
			ORDER BY s.FULLNAMEND ";
		
		$query = $this->getEntityManager()->createQuery($sql);
		$query->setParameter('CC1',$countryCode);
		if(isset($regionCode))
			$query->setParameter('ADM1',$regionCode);
		if(isset($departementCode))
			$query->setParameter('ADM2',$departementCode);
		if(isset($districtCode))
			$query->setParameter('ADM3',$districtCode);
		if(isset($divisionCode))
			$query->setParameter('ADM4',$divisionCode);

		return $query->getResult();
	}

	public function findCitiesArround($radius, $lat, $lon, $countryCode = null, $unit = 'km')
	{		
		//constante for units
		if($unit=='km')
		{
			$onedegree = 111.045;
			$earthradius = 6366.565;			
		}
		elseif($unit=='miles')
		{
			$onedegree = 69;
			$earthradius = 3956;
		}

		//calcul of the box
		$lon1 = $lon-$radius/abs(cos(deg2rad($lat))*$onedegree);
		$lon2 = $lon+$radius/abs(cos(deg2rad($lat))*$onedegree);
		$lat1 = $lat-($radius/$onedegree);
		$lat2 = $lat+($radius/$onedegree);

		//calculation of distance field
		$distance_formula = " $earthradius * 2 * ASIN(SQRT( POWER(SIN(($lat - C.LATITUDE) *  pi()/180 / 2), 2) +COS($lat * pi()/180) * COS(C.LATITUDE * pi()/180) * POWER(SIN(($lon - C.LONGITUDE) * pi()/180 / 2), 2) )) as distance ";

		$sql = 'SELECT *, '.$distance_formula;
		$sql .= 'FROM world_cities as C ';
		$sql .= 'WHERE 1=1 ';
		if(isset($countryCode))
			$sql .= 'AND CC1=:CC1 ';
		$sql .= ' AND C.LONGITUDE BETWEEN '.$lon1.' AND '.$lon2.' AND C.LATITUDE BETWEEN '.$lat1.' AND '.$lat2.' ';
		$sql .= 'having distance < '.$radius;

		
		$rsm = $this->resultSetMappingCity();
		
		$query = $this->_em->createNativeQuery($sql,$rsm);
		$query->setParameter('CC1',$countryCode);

		return $query->getResult();
		//return $this->getQuery($sql)->getResult();

	}

	private function resultSetMappingCity()
	{
		$rsm = new ResultSetMapping();
		$rsm->addEntityResult('My\WorldBundle\Entity\City', 'C');
		$rsm->addFieldResult('C', 'id', 'id');
		$rsm->addFieldResult('C', 'CHAR_CODE', 'char_code');		
		$rsm->addFieldResult('C', 'UNI', 'UNI');
		$rsm->addFieldResult('C', 'CC1', 'CC1');
		$rsm->addFieldResult('C', 'DSG', 'DSG');
		$rsm->addFieldResult('C', 'ADM1', 'ADM1');
		$rsm->addFieldResult('C', 'ADM2', 'ADM2');
		$rsm->addFieldResult('C', 'ADM3', 'ADM3');
		$rsm->addFieldResult('C', 'ADM3', 'ADM3');
		$rsm->addFieldResult('C', 'ADM4', 'ADM4');
		$rsm->addFieldResult('C', 'LC', 'LC');
		$rsm->addFieldResult('C', 'FULLNAMEND', 'FULLNAMEND');
		$rsm->addFieldResult('C', 'LATITUDE', 'LATITUDE');
		$rsm->addFieldResult('C', 'LONGITUDE', 'LONGITUDE');

		return $rsm;
	}
}
