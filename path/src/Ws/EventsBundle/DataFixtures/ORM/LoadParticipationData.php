<?php

namespace Ws\EventsBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Ws\EventsBundle\Entity\Participation;

class LoadParticipationData extends AbstractFixture implements OrderedFixtureInterface
{

	public function load(ObjectManager $manager)
	{
		
		$footballeur = new Participation();
		$footballeur->setUser($this->getReference('user1'));
		$footballeur->setEvent($this->getReference('event_football'));
		$manager->persist($footballeur);


		for($i=0; $i<=3; $i++)
		{
			$nageur = new Participation();
			$nageur->setUser($this->getReference('asso1'));
			$nageur->setEvent($this->getReference('event_natation'.$i));

			$manager->persist($nageur);
		}

		$manager->flush();

	}

	public function getOrder(){

		return 5; // the order in which fixtures will be loaded
	}
}

?>