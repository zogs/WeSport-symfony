<?php

namespace Ws\ConvertSQLBundle\Converter;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class Purger
{
	private $purger;

	public function __construct(EntityManager $em)
	{
		$this->purger = new ORMPurger($em);
	}

	public function setManager(EntityManager $em)
	{
		$this->purger = new ORMPurger($em);
	}

	public function purge()
	{
		return $this->purger->purge();
	}

	public function getDatabase()
	{
		return $this->purger->getObjectManager()->getConnection()->getDatabase();
	}

}