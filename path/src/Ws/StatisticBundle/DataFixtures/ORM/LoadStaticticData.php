<?php

namespace Ws\EventsBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Ws\StatisticBundle\Entity\GeneralStat;

class LoadStatisticData extends AbstractFixture implements OrderedFixtureInterface
{

	public function load(ObjectManager $manager)
	{		

		$general = new GeneralStat();
		$manager->persist($general);

		$manager->flush();

	}

	public function getOrder(){

		return 10; // the order in which fixtures will be loaded
	}
}

?>