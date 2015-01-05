<?php

namespace Ws\EventsBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Ws\StatisticBundle\Entity\GlobalStat;

class LoadStatisticData extends AbstractFixture implements OrderedFixtureInterface
{

	public function load(ObjectManager $manager)
	{		

		$general = new GlobalStat();
		$manager->persist($general);

		$manager->flush();

	}

	public function getOrder(){

		return 10; // the order in which fixtures will be loaded
	}
}

?>