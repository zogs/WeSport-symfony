<?php

namespace Ws\EventsBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Ws\EventsBundle\Entity\Serie;

class LoadSerieData extends AbstractFixture implements OrderedFixtureInterface
{

	public function load(ObjectManager $manager)
	{
		$serie = new Serie();
		$serie->setOccurences(1);

		$serie2 = new Serie();
		$serie2->setOccurences(3);
		$serie2->setStartDate(new \DateTime());
		$serie2->setEndDate(new \DateTime());
		$serie2->setFriday(1);

		$manager->persist($serie);
		$manager->persist($serie2);

		$manager->flush();

		$this->addReference('serie1', $serie);
		$this->addReference('serie2', $serie2);
	}

	public function getOrder(){

		return 3; // the order in which fixtures will be loaded
	}
}

?>