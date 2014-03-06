<?php

namespace Ws\SportsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Ws\SportsBundle\Entity\Sport;

class LoadSportsData extends AbstractFixture implements OrderedFixtureInterface
{
	
	public function load(ObjectManager $manager)
	{

		$sport1 = new Sport();
		$sport1->setName('Boxe');
		$sport1->setIcon('fighting');
		$sport1->setAction('do');
		$sport1->setCategory($this->getReference('cat1'));

		$sport2 = new Sport();
		$sport2->setName('Kung-Fu');
		$sport2->setIcon('fighting');
		$sport2->setAction('do');
		$sport2->setCategory($this->getReference('cat1'));

		$sport3 = new Sport();
		$sport3->setName('Football');
		$sport3->setIcon('foot');
		$sport3->setAction('play');
		$sport3->setCategory($this->getReference('cat2'));

		$sport4 = new Sport();
		$sport4->setName('Rugby');
		$sport4->setIcon('rugby');
		$sport4->setAction('play');
		$sport4->setCategory($this->getReference('cat2'));

		$sport5 = new Sport();
		$sport5->setName('Handball');
		$sport5->setIcon('handball');
		$sport5->setAction('play');
		$sport5->setCategory($this->getReference('cat2'));

		$sport6 = new Sport();
		$sport6->setName('Natation');
		$sport6->setIcon('swimming');
		$sport6->setAction('go');
		$sport6->setCategory($this->getReference('cat3'));

		$manager->persist($sport1);
		$manager->persist($sport2);
		$manager->persist($sport3);
		$manager->persist($sport4);
		$manager->persist($sport5);
		$manager->persist($sport6);

		$manager->flush();

		$this->addReference('sport_boxe', $sport1);
		$this->addReference('sport_kungfu', $sport2);
		$this->addReference('sport_football', $sport3);
		$this->addReference('sport_rugby', $sport4);
		$this->addReference('sport_handball', $sport5);
		$this->addReference('sport_natation', $sport6);

	}

	public function getOrder()
	{
		return 2;
	}

}

?>