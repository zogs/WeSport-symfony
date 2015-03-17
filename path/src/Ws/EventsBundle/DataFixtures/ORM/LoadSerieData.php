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
		$manager->persist($serie);

		$serie2 = new Serie();
		$serie2->setOccurences(10);
		$serie2->setStartDate($today);
		$serie2->setEndDate($today);
		$serie2->setFriday(1);
		$serie2->setOrganizer($this->getReference('asso1'));
		$manager->persist($serie2);

		$serie3 = new Serie();
		$serie3->setOccurences(20);
		$serie3->setStartDate($today);
		$enddate = new \DateTime();
		$enddate->modify('+ 20 days');
		$serie3->setEndDate($enddate);
		$serie3->setOrganizer($this->getReference('user1'));
		$manager->persist($serie3);

		$serie4 = new Serie();
		$serie4->setOccurences(1);
		$serie4->setStartDate($today);
		$enddate = new \DateTime();
		$enddate->modify('+ 1 days');
		$serie4->setEndDate($enddate);
		$serie4->setOrganizer($this->getReference('user1'));
		$manager->persist($serie4);

		$serie5 = new Serie();
		$serie5->setOccurences(1);
		$serie5->setStartDate($today);
		$enddate = new \DateTime();
		$enddate->modify('+ 1 days');
		$serie5->setEndDate($enddate);
		$serie5->setOrganizer($this->getReference('user1'));
		$manager->persist($serie5);

		$manager->flush();

		$this->addReference('serie1', $serie);
		$this->addReference('serie2', $serie2);
		$this->addReference('serie3', $serie3);
		$this->addReference('serie4', $serie4);
		$this->addReference('serie5', $serie5);
	}

	public function getOrder(){

		return 3; // the order in which fixtures will be loaded
	}
}

?>