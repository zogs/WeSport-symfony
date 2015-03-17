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
		//Evenement unique 
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
		$event1->setNbmin(5);
		$event1->setPrice(10);
		

		$manager->persist($event1);

		$this->addReference('event_football', $event1);
		

		//Serie d'événeement d'un user
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
			$event->setType('person');
			$event->setLevel('confirmed');
			$manager->persist($event);
		
			$this->addReference('event_natation'.$i, $event);
		}

		//Serie d'évenement de l'asso
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
			$event->setSpot($this->getReference('spot_parc_beaune'));
			$event->setLocation($this->getReference('location_beaune'));
			$event->setOccurence($i+1);			
			$event1->setNbmin(5);
			$manager->persist($event);
		
			$this->addReference('event_boxe'.$i, $event);
		}

		// Evenement privé
		$event3 = new Event();
		$event3->setTitle("(privé) Petit match entre amis");
		$event3->setSlug($event1->getTitle().'-1');
		$today = new \DateTime();
		$event3->setDate($today); 
		$event3->setTime($today);
		$event3->setSerie($this->getReference('serie4'));
		$event3->setSport($this->getReference('sport_football'));
		$event3->setDescription("Venez faire un petit 4contre4 :)");
		$event3->setOrganizer($this->getReference('user1'));
		$event3->setSpot($this->getReference('spot_parc_beaune'));
		$event3->setLocation($this->getReference('location_beaune'));
		$event3->setPrice(10);
		$event3->setPublic(false);
		$manager->persist($event3);

		$this->addReference('event_football_private', $event3);

		//Evenement offline
		$event4 = new Event();
		$event4->setTitle("(offline) Petit match entre amis");
		$event4->setSlug($event1->getTitle().'-1');
		$today = new \DateTime();
		$event4->setDate($today); 
		$event4->setTime($today);
		$event4->setSerie($this->getReference('serie5'));
		$event4->setSport($this->getReference('sport_football'));
		$event4->setDescription("Venez faire un petit 4contre4 :)");
		$event4->setOrganizer($this->getReference('user1'));
		$event4->setSpot($this->getReference('spot_parc_beaune'));
		$event4->setLocation($this->getReference('location_beaune'));
		$event4->setPrice(10);
		$event4->setOnline(false);
		$manager->persist($event4);

		$this->addReference('event_football_offline', $event4);
		$manager->flush();

	}

	public function getOrder(){

		return 4; // the order in which fixtures will be loaded
	}
}

?>