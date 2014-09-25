<?php

namespace Ws\EventsBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Ws\EventsBundle\Entity\Spot;

class LoadSpotsData extends AbstractFixture implements OrderedFixtureInterface
{

	public function load(ObjectManager $manager)
	{		

		$spot1 = new Spot();
		$spot1->setName('Piscine Olympique');
		$spot1->setAddress('12 Rue Alain Bombard');
		$spot1->setLocation($this->getReference('location_dijon'));
		$manager->persist($spot1);
		$this->addReference('spot_piscine_dijon', $spot1);

		$spot2 = new Spot();
		$spot2->setName('Parc des Biches');
		$spot2->setAddress('44 Allées du parc');
		$spot2->setLocation($this->getReference('location_dijon'));
		$manager->persist($spot2);
		$this->addReference('spot_parc_dijon', $spot2);

		$spot3 = new Spot();
		$spot3->setName('Salle Michel Bon');
		$spot3->setAddress('Rue du parcours');
		$spot3->setLocation($this->getReference('location_beaune'));
		$manager->persist($spot3);
		$this->addReference('spot_salle_beaune', $spot3);

		$spot4 = new Spot();
		$spot4->setName('Parc de la Bouzaize');
		$spot4->setLocation($this->getReference('location_beaune'));
		$manager->persist($spot4);
		$this->addReference('spot_parc_beaune', $spot4);
	

		$manager->flush();

	}

	public function getOrder(){

		return 3; // the order in which fixtures will be loaded
	}
}

?>