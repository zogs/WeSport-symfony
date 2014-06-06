<?php

namespace Ws\EventsBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Ws\EventsBundle\Entity\Serie;
/**
 * SerieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SerieRepository extends EntityRepository
{
	public function findRecentSeriePosted()
	{
		$qb = $this->createQueryBuilder('s');
		$qb->select('s')
			->orderBy('s.date_depot','DESC');

		return $qb->getQuery()->getResult();
	}
}