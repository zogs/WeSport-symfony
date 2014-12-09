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
		$event1->setSlug($event1->getTitle().'-1');
		$today = new \DateTime();
		$event1->setDate($today); 
		$event1->setTime($today);
		$event1->setSerie($this->getReference('serie1'));
		$event1->setSport($this->getReference('sport_football'));
		$event1->setDescription("Venez faire un petit 4contre4 :)");
		$event1->setOrganizer($this->getReference('user1'));
		$event1->setSpot($this->getReference('spot_parc_beaune'));
		$event1->setLocation($this->getReference('location_beaune'));
		$event1->setPrice(10);
		$event1->setLevel(rand(0,4));

		$manager->persist($event1);

		$this->addReference('event_football', $event1);
		

		//serie 
		for($i=0; $i<=10; $i++){

			$event = new Event();
			$event->setTitle("Cherche copain nageur");
			$event->setSlug($event->getTitle().'-'.$i);
			$today = new \DateTime();
			$event->setDate($today->modify('+'.$i.' days'));
			$event->setTime($today->modify('+'.$i.' hours'));
			$event->setSerie($this->getReference('serie2'));
			$event->setSport($this->getReference('sport_natation'));
			$event->setDescription("Cherche un ami pour pas nager seul");
			$event->setOrganizer($this->getReference('asso1'));
			$event->setConfirmed(true);
			$event->setSpot($this->getReference('spot_piscine_dijon'));
			$event->setLocation($this->getReference('location_dijon'));
			$event->setOccurence($i+1);
			$event->setType(rand(0,3));
			$event->setLevel(rand(0,4));
			$manager->persist($event);
		
			$this->addReference('event_natation'.$i, $event);
		}

		//serie 
		for($i=0; $i<=20; $i++){

			$event = new Event();
			$event->setTitle("Capoera !");
			$event->setSlug($event->getTitle().'-'.$i);
			$d = $i*2;
			$today = new \DateTime();
			$event->setDate($today->modify('+'.$d.' days'));
			$event->setTime($today->modify('+'.$d.' hours'));
			$event->setSerie($this->getReference('serie3'));
			$event->setSport($this->getReference('sport_boxe'));
			$event->setDescription("Cherche un Sparing pour entrainement");
			$event->setOrganizer($this->getReference('user1'));
			$event->setSpot($this->getReference('spot_parc_dijon'));
			$event->setLocation($this->getReference('location_dijon'));
			$event->setOccurence($i+1);
			$event->setType(rand(0,3));
			$event->setLevel(rand(0,4));

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