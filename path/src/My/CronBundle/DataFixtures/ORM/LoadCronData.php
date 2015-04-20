<?php

namespace My\CronBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use My\CronBundle\Entity\CronTask;

class LoadCronData extends AbstractFixture implements OrderedFixtureInterface
{

	public function load(ObjectManager $manager)
	{

		$day = new \DateTime('now');
		$crontask = new CronTask();
		$crontask->setName("Envoyer les alertes quotidiennes aux utilisateurs");
		$crontask->setCommands(array('send:alerts daily'));
		$crontask->setInterval(86400); //every hour
		$crontask->setLastRun($day->setTime(06,30));

		$manager->persist($crontask);
		$manager->flush();

		$day = new \DateTime('now');
		$crontask = new CronTask();
		$crontask->setName("Envoyer les alertes hebdomadaires aux utilisateurs");
		$crontask->setCommands(array('send:alerts weekly'));
		$crontask->setInterval(604800); //every day
		$crontask->setLastRun($day->setTime(06,30));

		$manager->persist($crontask);
		$manager->flush();

		$day = new \DateTime('now');
		$crontask = new CronTask();
		$crontask->setName("Envoyer les daily stats. aux admins");
		$crontask->setCommands(array('statistic:daily --email'));
		$crontask->setInterval(86400); //every day
		$day->setTime(23,30);
		$crontask->setLastRun($day);

		$manager->persist($crontask);
		$manager->flush();

	}

	public function getOrder(){

		return 6; // the order in which fixtures will be loaded
	}
}

?>