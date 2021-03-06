<?php

namespace Ws\StatisticBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Ws\SportsBundle\Entity\Sport;
use FOS\UserBundle\Model\UserInterface;
/**
 * UserSportStatRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserSportStatRepository extends EntityRepository
{
	public function setSportCreated(Sport $sport,UserInterface $user)
	{
		$data = $this->findSportStat($sport,$user);

		if($data == null) $data = new UserSportStat($sport,$user);

		$data->created += 1;
		
		$this->save($data,true);
	}

	public function setSportParticiped(Sport $sport, UserInterface $user)
	{
		$data = $this->findSportStat($sport,$user);

		if($data == null) $data = new UserSportStat($sport,$user);

		$data->participated += 1;
		
		$this->save($data,true);
	}

	public function findSportStat(Sport $sport, UserInterface $user)
	{
		$qb = $this->createQueryBuilder('u');
		$qb->where(
			$qb->expr()->eq('u.sport',':sport'),
			$qb->expr()->eq('u.user',':user')
			);
		$qb->setParameter('sport',$sport)->setParameter('user',$user);

		return $this->getQuery()->getOneOrNullResult();
	}
}
