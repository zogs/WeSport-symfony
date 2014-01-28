<?php

namespace Ws\EventsBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Ws\EventsBundle\Entity\Event;

class LoadEventsData extends AbstractFixture implements OrderedFixtureInterface
{

	public function load(ObjectManager $manager)
	{		

		$event1 = new Event();
		$event1->setTitle("Petit match entre amis");
		$event1->setDate(new \DateTime());
		$event1->setTime(new \DateTime());
		$event1->setSerie($this->getReference('serie1'));
		$event1->setSport($this->getReference('sport_football'));
		$event1->setDescription("Venez faire un petit 4contre4 :)");
		$event1->setAddress("10 rue Henry Dunant BEAUNE 21200");
		$event1->setOrganizer($this->getReference('user1'));
		$event1->setLocation($this->getReference('location_beaune'));

		$manager->persist($event1);

		$this->addReference('event_football', $event1);
		

		//serie 
		for($i=0; $i<=3; $i++){

			$event = new Event();
			$event->setTitle("Cherche bon nageur");
			$event->setDate(new \DateTime());
			$event->setTime(new \DateTime());
			$event->setSerie($this->getReference('serie2'));
			$event->setSport($this->getReference('sport_natation'));
			$event->setDescription("Cherche un ami pour pas nager seul");
			$event->setAddress("Piscine Olympic 21000 DIJON");
			$event->setOrganizer($this->getReference('user2'));
			$event->setLocation($this->getReference('location_dijon'));
			$event->setOccurence($i+1);

			$manager->persist($event);
		
			$this->addReference('event_natation'.$i, $event);
		}

		$manager->flush();

	}

	public function getOrder(){

		return 4; // the order in which fixtures will be loaded
	}
}

?>