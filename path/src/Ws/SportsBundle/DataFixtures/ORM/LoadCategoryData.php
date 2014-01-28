<?php

namespace Ws\SportsBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Ws\SportsBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{

	public function load(ObjectManager $manager)
	{
		$category1 = new Category();
		$category1->setName("Sport de combat");
		$category1->setIcon("fight-icon");

		$category2 = new Category();
		$category2->setName("Sport de ballon");
		$category2->setIcon("foot");

		$category2 = new Category();
		$category2->setName("Sport de ballon");
		$category2->setIcon("ballon-icon");

		$category3 = new Category();
		$category3->setName("Sport aquatique");
		$category3->setIcon("wave-icon");


		$manager->persist($category1);
		$manager->persist($category2);
		$manager->persist($category3);

		$manager->flush();

		$this->addReference('cat1', $category1);
		$this->addReference('cat2', $category2);
		$this->addReference('cat3', $category3);
	}

	public function getOrder(){

		return 1; // the order in which fixtures will be loaded
	}
}

?>