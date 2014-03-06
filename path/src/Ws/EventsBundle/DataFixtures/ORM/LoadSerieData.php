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
		$today = new \DateTime();

		$serie = new Serie();
		$serie->setOccurences(1);
		$serie->setStartDate($today);
		$serie->setEndDate($today);
		$serie->setOrganizer($this->getReference('user1'));

		$serie2 = new Serie();
		$serie2->setOccurences(3);
		$serie2->setStartDate($today);
		$serie2->setEndDate($today);
		$serie2->setFriday(1);
		$serie2->setOrganizer($this->getReference('asso1'));

		$serie3 = new Serie();
		$serie3->setOccurences(3);
		$serie3->setStartDate($today);
		$enddate = new \DateTime();
		$enddate->modify('+ 22 days');
		$serie3->setEndDate($enddate);
		$serie3->setOrganizer($this->getReference('user1'));

		$manager->persist($serie);
		$manager->persist($serie2);
		$manager->persist($serie3);

		$manager->flush();

		$this->addReference('serie1', $serie);
		$this->addReference('serie2', $serie2);
		$this->addReference('serie3', $serie3);
	}

	public function getOrder(){

		return 3; // the order in which fixtures will be loaded
	}
}

?>