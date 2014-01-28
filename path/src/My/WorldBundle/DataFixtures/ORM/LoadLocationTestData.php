<?php

namespace My\WorldBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use My\WorldBundle\Entity\Location;

class LoadLocationTestData extends AbstractFixture implements OrderedFixtureInterface
{

	public function load(ObjectManager $manager)
	{
		$locate = new Location();
		$locate->setCountry(82);
		$locate->setRegion(3425);
		$locate->setDepartement(4963);
		$locate->setDistrict(null);
		$locate->setDivision(null);
		$locate->setCity(2569058);
		$this->addReference('location_moloy', $locate);

		$manager->persist($locate);
		

		$locate = new Location();
		$locate->setCountry(82);
		$locate->setRegion(3425);
		$locate->setDepartement(4963);
		$locate->setDistrict(null);
		$locate->setDivision(null);
		$locate->setCity(2568568);
		$this->addReference('location_beaune', $locate);

		$manager->persist($locate);
		

		$locate = new Location();
		$locate->setCountry(82);
		$locate->setRegion(3425);
		$locate->setDepartement(4963);
		$locate->setDistrict(null);
		$locate->setDivision(null);
		$locate->setCity(2568787);
		$this->addReference('location_dijon', $locate);

		$manager->persist($locate);
		

		$manager->flush();

	}

	public function getOrder(){

		return 3; // the order in which fixtures will be loaded
	}
}

?>