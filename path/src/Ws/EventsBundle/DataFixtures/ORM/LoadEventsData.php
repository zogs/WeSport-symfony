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
		$today = new \DateTime();
		$event1->setDate($today); 
		$event1->setTime($today);
		$event1->setSerie($this->getReference('serie1'));
		$event1->setSport($this->getReference('sport_football'));
		$event1->setDescription("Venez faire un petit 4contre4 :)");
		$event1->setAddress("10 rue Henry Dunant BEAUNE 21200");
		$event1->setOrganizer($this->getReference('user1'));
		$event1->setLocation($this->getReference('location_beaune'));
		$event1->setPrice(10);

		$manager->persist($event1);

		$this->addReference('event_football', $event1);
		

		//serie 
		for($i=0; $i<=3; $i++){

			$event = new Event();
			$event->setTitle("Cherche copain nageur");
			$today = new \DateTime();
			$event->setDate($today->modify('+'.$i.' days'));
			$event->setTime($today);
			$event->setSerie($this->getReference('serie2'));
			$event->setSport($this->getReference('sport_natation'));
			$event->setDescription("Cherche un ami pour pas nager seul");
			$event->setAddress("Piscine Olympic 21000 DIJON");
			$event->setOrganizer($this->getReference('asso1'));
			$event->setConfirmed(true);
			$event->setLocation($this->getReference('location_dijon'));
			$event->setOccurence($i+1);
			$event->setType('asso');

			$manager->persist($event);
		
			$this->addReference('event_natation'.$i, $event);
		}

		//serie 
		for($i=0; $i<=3; $i++){

			$event = new Event();
			$event->setTitle("Sparing partner boxe");
			$d = $i*7;
			$today = new \DateTime();
			$event->setDate($today->modify('+'.$d.' days'));
			$event->setTime($today);
			$event->setSerie($this->getReference('serie3'));
			$event->setSport($this->getReference('sport_boxe'));
			$event->setDescription("Cherche un Sparing pour entrainement");
			$event->setAddress("Salle de boxe 21000 DIJON");
			$event->setOrganizer($this->getReference('user1'));
			$event->setLocation($this->getReference('location_dijon'));
			$event->setOccurence($i+1);
			$event->setType('pro');

			$manager->persist($event);
		
			$this->addReference('event_boxe'.$i, $event);
		}


		$manager->flush();

	}

	public function getOrder(){

		return 4; // the order in which fixtures will be loaded
	}
}

?>