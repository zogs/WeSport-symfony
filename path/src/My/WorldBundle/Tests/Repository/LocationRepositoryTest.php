<?php

namespace My\WorldBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LocationRepositoryTest extends WebTestCase
{
	private $client;
	private $router;
	private $em;
	private $manager;

	/**
	 * PHPUnit setup
	 */
	public function setUp()
	{	
		$this->client = self::createClient(array(),array('PHP_AUTH_USER' => 'user1','PHP_AUTH_PW' => 'fatboy'));	
		$this->router = $this->client->getContainer()->get('router');
		$this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
		$this->repo = $this->em->getRepository('MyWorldBundle:Location');
	}
	/**
	 * PHPUnit close up
	 */
	protected function tearDown()
	{
		$this->em->close();
		unset($this->client, $this->em);
	}

	public function testFindLocationByCityName()
	{
		//test an existing Location
		$location = $this->repo->findLocationByCityName('Dijon','FR');
		$this->assertEquals('Dijon',$location->getCity()->getName());

		//force to create Location
		$location = $this->repo->findLocationByCityName('Marseille','FR');
		$this->assertEquals('Marseille',$location->getCity()->getName());
		$this->assertEquals("Provence-Alpes-Cote d'Azur",$location->getRegion()->getName());
		$this->assertEquals('Departement des Bouches-du-Rhone',$location->getDepartement()->getName());
		$this->assertEquals('France',$location->getCountry()->getName());

		$location = $this->repo->findLocationByCityName('London','UK');
		$this->assertEquals('London',$location->getCity()->getName());

	}

	public function testFindLocationByCityId()
	{
		//test an existing Location
		$city = $this->em->getRepository('MyWorldBundle:City')->findCityByName('Dijon','FR');
		$location = $this->repo->findLocationByCityId($city->getId());
		$this->assertEquals('Dijon',$location->getCity()->getName());

		//force to create a Location
		$city = $this->em->getRepository('MyWorldBundle:City')->findCityByName('Dublin','EI');
		$location = $this->repo->findLocationByCityId($city->getId());
		$this->assertEquals('Dublin',$location->getCity()->getName());
	}

	public function testFindLocationByCountryCode()
	{
		//test an existing Location
		$location = $this->repo->findLocationByCountryCode('FR');
		$this->assertEquals('France',$location->getCountry()->getName());

		//force to create a Location
		$location = $this->repo->findLocationByCountryCode('UK');
		$this->assertEquals('United Kingdom',$location->getCountry()->getName());
	}

	public function testFindStateById()
	{
		//get london Location
		$london = $this->repo->findLocationByCityName('London','UK');
		//country
		$country = $this->repo->findStateById('country',$london->getCountry()->getId());
		$this->assertEquals('United Kingdom',$country->getName());
		//region
		$region = $this->repo->findStateById('region',$london->getRegion()->getId());
		$this->assertEquals('England',$region->getName());
		//departement
		$departement = $this->repo->findStateById('departement',$london->getDepartement()->getId());
		$this->assertEquals('London',$departement->getName());
		//district
		$district = $this->repo->findStateById('district',$london->getDistrict()->getId());
		$this->assertEquals('Greater London',$district->getName());
		//division
		$division = $this->repo->findStateById('division',$london->getDivision()->getId());
		$this->assertEquals('City of London',$division->getName());
		//city
		$city = $this->repo->findStateById('city',$london->getCity()->getId());
		$this->assertEquals('London',$city->getName());
	}

	public function testFindChildrenStatesByParent()
	{
		$france = $this->em->getRepository('MyWorldBundle:Country')->findCountryByName('France');
		$children = $this->repo->findChildrenStatesByParent($france);
		$this->assertEquals('Aquitaine',$children[0]->getName());
		$this->assertEquals('Auvergne',$children[1]->getName());
		$this->assertEquals('Basse-Normandie',$children[2]->getName());
		//...

		$bretagne = $this->em->getRepository('MyWorldBundle:State')->findStateByName('Bretagne','FR');
		$children = $this->repo->findChildrenStatesByParent($bretagne);
		$this->assertEquals("Departement des Cotes-d'Armor",$children[0]->getName());
		$this->assertEquals("Departement des Cotes-du-Nord",$children[1]->getName());
		$this->assertEquals("Departement du Finistere",$children[2]->getName());
		//...

		$finistere = $this->em->getRepository('MyWorldBundle:State')->findStateByName('Departement du Finistere','FR');
		$children = $this->repo->findChildrenStatesByParent($finistere);
		$this->assertEquals("Anteren",$children[0]->getName());
		$this->assertEquals("Argenton",$children[1]->getName());
		$this->assertEquals("Argol",$children[2]->getName());
		//...
	}

	public function testFindStatesFromCodes()
	{
		$array = array(
			'CC1' => 'FR',
			'ADM1' => 'A1',
			'ADM2' => '21',
			'city' => -2041884
			);
		$states = $this->repo->findStatesFromCodes($array);
		$this->assertEquals('France',$states['country']->getName());
		$this->assertEquals('Bourgogne',$states['region']->getName());
		$this->assertEquals("Departement de la Cote-d' Or",$states['departement']->getName());
		$this->assertEquals('Dijon',$states['city']->getName());
	}

	public function testFindStatesByParentCode()
	{
		$states = $this->repo->findStatesByParentCode('FR','ADM2','A1');
		$this->assertEquals("Departement de la Cote-d' Or",$states[0]->getName());
		$this->assertEquals("Departement de la Nievre",$states[1]->getName());
		//...
	}

	public function testFindStatesByCodes()
	{
		//find regions
		$states = $this->repo->findStatesByCodes('FR');
		$this->assertEquals('Aquitaine',$states[0]->getName());
		$this->assertEquals('Auvergne',$states[1]->getName());
		$this->assertEquals('Basse-Normandie',$states[2]->getName());
		//find departements
		$states = $this->repo->findStatesByCodes('FR','A1');
		$this->assertEquals("Departement de la Cote-d' Or",$states[0]->getName());
		$this->assertEquals("Departement de la Nievre",$states[1]->getName());
		//skip district test
		//skip divisiion test
		//find cities
		$states = $this->repo->findStatesByCodes('FR','A1','21');
		$this->assertEquals("Agencourt",$states[0]->getName());
		$this->assertEquals("Agey",$states[1]->getName());
	}

	public function testFindStatesListByCodes()
	{
		$states = $this->repo->findStatesListByCodes('FR','A1');
		$this->assertEquals('departement',$states['level']);
		$this->assertEquals("Departement de la Cote-d' Or",$states['list'][4963]);
	}
}