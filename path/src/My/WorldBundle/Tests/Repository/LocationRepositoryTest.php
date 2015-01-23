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

	public function testFindStatesListFromLocationByLevel()
	{
		//get london Location
		$london = $this->repo->findLocationByCityName('London','UK');
		//country
		$countries = $this->repo->findStatesListFromLocationByLevel($london,'country');
		$this->assertEquals(265,count($countries['list']));		
		//region
		$regions = $this->repo->findStatesListFromLocationByLevel($london,'region');
		$this->assertEquals('England',$regions['list'][12532]);
		$this->assertEquals('Northern Ireland',$regions['list'][12549]);
		$this->assertEquals('Scotland',$regions['list'][12566]);
		$this->assertEquals('Wales',$regions['list'][12583]);
		//departement
		$dpts = $this->repo->findStatesListFromLocationByLevel($london,'departement');
		$this->assertEquals('North East England',$dpts['list'][12600]);
		$this->assertEquals(9,count($dpts['list']));
		//district
		$dists = $this->repo->findStatesListFromLocationByLevel($london,'district');
		$this->assertEquals('Greater London',$dists['list'][12756]);
		//division
		$divs = $this->repo->findStatesListFromLocationByLevel($london,'division');
		$this->assertEquals(33,count($divs['list']));
		//cities
		$cities = $this->repo->findStatesListFromLocationByLevel($london,'city');
		$this->assertEquals('City of London',$cities['list'][3902000]);
		$this->assertEquals('London',$cities['list'][3902006]);
		$this->assertEquals('Puddle Dock',$cities['list'][3902020]);
		$this->assertEquals(3,count($cities['list']));


		//City of Finistere
		$brest = $this->repo->findLocationByCityName('Brest','FR');
		$cities = $this->repo->findStatesListFromLocationByLevel($brest,'city');
		$this->assertEquals(1205,count($cities['list']));
		$this->assertEquals('Plouguerneau',$cities['list'][2573632]);		
		
	}

	public function testFindLocationFromStates()
	{
		//get Location
		$london = $this->repo->findLocationByCityName('London','UK');

		$states = array(
			'country' => $london->getCountry(),
			'region' => $london->getRegion(),
			'departement' => $london->getDepartement(),
			'district' => $london->getDistrict(),
			'division' => $london->getDivision(),
			'city' => $london->getCity(),
			);

		$test = $this->repo->findLocationFromStates($states);

		$this->assertEquals($london->getId(),$test->getId());
	}
}