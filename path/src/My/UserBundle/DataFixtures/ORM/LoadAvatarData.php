<?php

namespace My\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use My\UserBundle\Entity\Avatar;

class LoadAvatarData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
	private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

	public function load(ObjectManager $manager)
	{		
		$date = new \Datetime();

		$a = new Avatar();
		$a->setPath('sim.jpg');
		$a->setUpdated($date);

		$manager->persist($a);
		$this->addReference('avatar_admin',$a);


		$a = new Avatar();
		$a->setPath('PS.jpg');
		$a->setUpdated($date);

		$manager->persist($a);
		$this->addReference('avatar_user1',$a);


		$a = new Avatar();
		$a->setPath('m2.jpg');
		$a->setUpdated($date);

		$manager->persist($a);
		$this->addReference('avatar_asso1',$a);


		$manager->flush();
	}

	public function getOrder(){

		return 0; // the order in which fixtures will be loaded
	}
}

?>