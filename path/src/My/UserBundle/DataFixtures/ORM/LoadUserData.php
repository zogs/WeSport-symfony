<?php

namespace My\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use My\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{

	public function load(ObjectManager $manager)
	{
		$admin = new User();
		$admin->setUsername('admin');
		$admin->setEmail('guichardsim@gmail.com');
		$admin->setEnabled(1);
		$admin->setPassword('fatboy');

		$manager->persist($admin);

		$this->addReference('user_admin',$admin);



		$user1 = new User();
		$user1->setUsername('user1');
		$user1->setEmail('guichardsim+user1@gmail.com');
		$user1->setEnabled(1);
		$user1->setPassword('fatboy');

		$manager->persist($user1);

		$this->addReference('user1',$user1);


		$user2 = new User();
		$user2->setUsername('user2');
		$user2->setEmail('guichardsim+user2@gmail.com');
		$user2->setEnabled(1);
		$user2->setPassword('fatboy');

		$manager->persist($user2);

		$this->addReference('user2',$user2);


		$manager->flush();
	}

	public function getOrder(){

		return 1; // the order in which fixtures will be loaded
	}
}

?>