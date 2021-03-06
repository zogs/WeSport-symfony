<?php

namespace Ws\SportsBundle\Entity;

use Doctrine\ORM\EntityRepository;


class SportRepository extends EntityRepository
{
	public function autocomplete($prefix)
	{
		$qb = $this->createQueryBuilder('s');

		$qb->innerJoin('s.category','c');

		$qb->andWhere($qb->expr()->like('s.name',':prefix'));
		$qb->orWhere($qb->expr()->like('s.keywords',':prefix'));
		$qb->orWhere($qb->expr()->like('c.keywords',':prefix'));

		$qb->setParameter('prefix','%'.$prefix.'%');
		
		return $qb->getQuery()->getResult();

	}	

	public function findRawById($id)
	{
		$qb = $this->createQueryBuilder('s');
		$qb->where($qb->expr()->eq('s.id',':id'));
		$qb->setParameter('id',$id);

		return $qb->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
	}

	public function findRawBySlug($slug)
	{
		$qb = $this->createQueryBuilder('s');
		$qb->where($qb->expr()->eq('s.slug',':slug'));
		$qb->orWhere($qb->expr()->eq('s.name',':slug'));
		$qb->setParameter('slug',$slug);

		return $qb->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
	}
}

?>