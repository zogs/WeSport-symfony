<?php

namespace My\WorldBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * StateRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StateRepository extends EntityRepository
{
	public function findStateByCodesQB($CC1, $code, $level)
	{

		$qb = $this->createQueryBuilder('s');

		$qb->select('s')
			->where(
				$qb->expr()->eq('s.CC1',':CC1'),
				$qb->expr()->eq('s.ADM_CODE',':code'),
				$qb->expr()->eq('s.DSG',':level')
				);

		$qb->setParameters(array(
			'CC1'=>$CC1,
			'code'=>$code,
			'level'=>$level)
		);

		return $qb->getQuery()->getResult();
	}


	public function findStateByCodes($CC1, $code, $level)
	{

		$q = $this->getEntityManager()->createQuery("
			SELECT s 
			FROM MyWorldBundle:State s
			JOIN MyWorldBundle:Country c
			WITH c.code = s.CC1
			WHERE s.CC1 = :CC1			
			AND s.ADM_CODE = :code
			AND s.DSG = :level
			AND s.lang = c.lang
			");

		$q->setParameters(array(
			'CC1'=>$CC1,
			'code'=>$code,
			'level'=>$level)
		);

		return $q->getSingleResult();
	}
}
