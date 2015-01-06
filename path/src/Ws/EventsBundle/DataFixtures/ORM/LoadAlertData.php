<?php

namespace Ws\EventsBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Ws\EventsBundle\Entity\Alert;
use Ws\EventsBundle\Entity\Search;

class LoadAlertData extends AbstractFixture implements OrderedFixtureInterface
{

	public function load(ObjectManager $manager)
	{		
		$today = new \DateTime();
		$start = new \DateTime();
		$end = new \DateTime('+6 month');

		$search1 = new Search();
		$search1->setPrice(5);
		$search1->setType(array('person','asso','pro'));	
		$search1->setDayOfWeek(array('monday','tuesday','wednesday','thursday','friday','saturday','sunday'));
		$search1->setLevel(array('beginner','average','confirmed'));

		$alert1 = new Alert();
		$alert1->setUser($this->getReference('user1'));
		$alert1->setEmail($this->getReference('user1')->getEmail());
		$alert1->setSearch($search1);
		$alert1->setFrequency('daily');
		$alert1->setDuration(6);
		$alert1->setDateStart($start);
		$alert1->setDateStop($end);
		$alert1->setDateCreated($today);

		$manager->persist($alert1);
		$this->addReference('event_alert_1', $alert1);



		$search2 = new Search();
		$search2->setCountry($manager->getRepository('MyWorldBundle:Country')->findCountryByName('France'));
		$search2->setLocation($manager->getRepository('MyWorldBundle:Location')->findLocationByCityName('Dijon'));
		$search2->setSports($manager->getRepository('WsSportsBundle:Sport')->findAll());
		$search2->setArea(100);
		$search2->setType(array('person','pro'));
		$search2->setNbDays(4);
		$search2->setPrice(5);
		$search2->setTimeEnd($today);
		$search2->setLevel(array('beginner','average','expert'));

		$alert2 = new Alert();
		$alert2->setUser($this->getReference('user1'));
		$alert2->setEmail($this->getReference('user1')->getEmail());
		$alert2->setSearch($search2);
		$alert2->setFrequency('daily');
		$alert2->setDuration(6);
		$alert2->setDateStart($start);
		$alert2->setDateStop($end);
		$alert2->setDateCreated($today);
		$alert2->setActive(false);

		$manager->persist($alert2);
		$this->addReference('event_alert_2', $alert2);




		$search3 = new Search();
		$search3->setOrganizer($manager->getRepository('MyUserBundle:User')->findOneByUsername('admin'));

		$alert3 = new Alert();
		$alert3->setUser($this->getReference('user1'));
		$alert3->setEmail($this->getReference('user1')->getEmail());
		$alert3->setSearch($search3);
		$alert3->setFrequency('weekly');
		$alert3->setDuration(6);
		$alert3->setDateStart($start);
		$alert3->setDateStop($end);
		$alert3->setDateCreated($today);

		$manager->persist($alert3);
		$this->addReference('event_alert_3', $alert3);


		$manager->flush();

	}

	public function getOrder(){

		return 5; // the order in which fixtures will be loaded
	}
}

?>