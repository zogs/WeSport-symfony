<?php

namespace My\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use My\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
	private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

	public function load(ObjectManager $manager)
	{		

		$admin = new User();
		$admin->setUsername('admin');
		$admin->setEmail('guichardsim@gmail.com');
		$admin->setEnabled(true);
		$admin->setPlainPassword('fatboy');
		$admin->setRoles(array('ROLE_SUPER_ADMIN','ROLE_ADMIN'));
		$admin->setLocation($this->getReference('location_dijon'));

		$manager->persist($admin);

		$this->addReference('user_admin',$admin);



		$user1 = new User();
		$user1->setUsername('user1');
		$user1->setEmail('guichardsim+user1@gmail.com');
		$user1->setEnabled(true);
		$user1->setPlainPassword('fatboy');
		$user1->setRoles(array('ROLE_USER'));
		$user1->setLocation($this->getReference('location_beaune'));

		$manager->persist($user1);

		$this->addReference('user1',$user1);


		$asso1 = new User();
		$asso1->setUsername('asso1');
		$asso1->setEmail('guichardsim+user2@gmail.com');
		$asso1->setType('asso');
		$asso1->setEnabled(true);
		$asso1->setPlainPassword('fatboy');
		$asso1->setRoles(array('ROLE_USER','ROLE_ASSO'));
		$asso1->setLocation($this->getReference('location_moloy'));

		$manager->persist($asso1);

		$this->addReference('asso1',$asso1);


		$manager->flush();
	}

	public function getOrder(){

		return 1; // the order in which fixtures will be loaded
	}
}

?>