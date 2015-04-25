<?php

namespace Ws\EventsBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Ws\EventsBundle\Entity\Serie;
use My\UserBundle\Entity\User;

/**
 * SerieRepository
 *
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

	public function findSeriesToComeByUser(User $user) {

		$qb = $this->createQueryBuilder('s');
		$qb->select('s')
			->where('s.organizer = :user')->setParameter('user',$user)
			->andWhere($qb->expr()->gte('DATE(s.endDate)','DATE(CURRENT_DATE())'))
			;

		$series = $qb->getQuery()->getResult();

		//remove past events from the collection
		//remove offline events too
		//remove empty series
		$today = new \DateTime('now');
		foreach ($series as $k => $serie) {			
			foreach ($serie->getEvents() as $event) {
				if($event->getDate()->format('Ymd') < $today->format('Ymd')) {
					$serie->getEvents()->removeElement($event);
				}
				if( false == $event->getOnline()) {
					$serie->getEvents()->removeElement($event);
				}
			}
			if($serie->getEvents()->isEmpty()){
				unset($series[$k]);
			}
		}

		return $series;
	}
}
