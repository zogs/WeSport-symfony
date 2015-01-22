<?php

namespace My\WorldBundle\Tests\Repository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CityRepositoryTest extends WebTestCase
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
		$this->repo = $this->em->getRepository('MyWorldBundle:City');
	}
	/**
	 * PHPUnit close up
	 */
	protected function tearDown()
	{
		$this->em->close();
		unset($this->client, $this->em);
	}

	public function testFindCityByUNI()
	{
		$cities = array(
			array('id'=>-2145387,'name'=>'Galway'),
			array('id'=>-2041884,'name'=>'Dijon'),
			array('id'=>-2107231,'name'=>'Tarbes'),
			);
		foreach ($cities as $key => $city) {
			
			$result = $this->repo->findCityByUNI($city['id']);
			$this->assertEquals($city['name'],$result->getName());
		}
	}

	public function testFindCityById()
	{
		$cities = array(
			array('id'=>2928207,'name'=>'Galway'),
			array('id'=>2568787,'name'=>'Dijon'),
			array('id'=>2599076,'name'=>'Tarbes'),
			);

		foreach ($cities as $key => $city) {
			
			$result = $this->repo->findCityById($city['id']);
			$this->assertEquals($city['name'],$result->getName());
		}
	}


	public function testFindCityByName()
	{
		$city = $this->repo->findCityByName('Bradbury');
		$this->assertEquals('Bradbury',$city->getName());

		$city = $this->repo->findCityByName('Bradbury','UK');
		$this->assertEquals('Bradbury',$city->getName());

		$city = $this->repo->findCityByName('Bradbury','UK','ENG');
		$this->assertEquals('Bradbury',$city->getName());

		$city = $this->repo->findCityByName('Bradbury','UK','ENG','A');
		$this->assertEquals('Bradbury',$city->getName());

		$city = $this->repo->findCityByName('Bradbury','UK','ENG','A','17');
		$this->assertEquals('Bradbury',$city->getName());

		$city = $this->repo->findCityByName('Bradbury','UK','ENG','A','17','D8');
		$this->assertEquals('Bradbury',$city->getName());
	}

	public function testFindCititesSuggestions()
	{
		//au niveau internationnal les deux villes les plus importantes commençant par "Beau" sont :
		$cities = $this->repo->findCitiesSuggestions(2,'Beau');
		$this->assertEquals('Beauvais',$cities[0]->getName());
		$this->assertEquals('Beaune',$cities[1]->getName());

		//En france
		$cities = $this->repo->findCitiesSuggestions(2,'Beau','FR');
		$this->assertEquals('Beauvais',$cities[0]->getName());
		$this->assertEquals('Beaune',$cities[1]->getName());
		
		//En bourgogne
		$cities = $this->repo->findCitiesSuggestions(2,'Beau','FR','A1');
		$this->assertEquals('Beaune',$cities[0]->getName());
		$this->assertEquals('Beaurepaire-en-Bresse',$cities[1]->getName());

		//En côte d'or
		$cities = $this->repo->findCitiesSuggestions(2,'Beau','FR','A1','21');
		$this->assertEquals('Beaune',$cities[0]->getName());
		$this->assertEquals('Beaumont-sur-Vingeanne',$cities[1]->getName());	
	}

	public function testCitiesByStateParent()
	{
		$cotedor = $this->em->getRepository('MyWorldBundle:State')->findStateByCode('FR','21','ADM2');
		$cities = $this->repo->findCitiesByStateParent($cotedor);

		$this->assertEquals(883,count($cities));
	}

	public function testFindCitiesByCode()
	{
		$cities = $this->repo->findCitiesByCode('FR','A1','21');
		$this->assertEquals(883,count($cities));
	}

	public function testfindCitiesArround()
	{
		//Dijon
		$lat = 47.321868;
		$lon = 5.039458;
		$pays = 'France';
		$cities = $this->repo->findCitiesArround(10,$lat,$lon,'FR');
		$this->assertEquals('Dijon',$cities[0]->getName());
		$this->assertEquals('Larrey',$cities[1]->getName());
		$this->assertEquals('La Fillotte',$cities[2]->getName());
		$this->assertEquals('Talant',$cities[3]->getName());
		$this->assertEquals(36,count($cities));	

		//Le Conquet
		$lat = 48.359109;
		$lon = -4.763050;
		$cities = $this->repo->findCitiesArround(10,$lat,$lon);
		$this->assertEquals('Le Conquet',$cities[0]->getName());
		$this->assertEquals('Lochrist',$cities[1]->getName());
		$this->assertEquals('Lanfeust',$cities[2]->getName());
		$this->assertEquals('Trebabu',$cities[3]->getName());
		$this->assertEquals(23,count($cities));	
	}

	public function testFindCitiesByStateParent()
	{
		$london = $this->em->getRepository('MyWorldBundle:Location')->findLocationByCityName('London','UK');

		//TOO LONG TO TEST cities of all UK
		//$cities = $this->repo->findCitiesByStateParent($london->getCountry());
		//$this->assertEquals(14905,count($cities));

		//TOO LONG TO TEST cities of all England
		//$cities = $this->repo->findCitiesByStateParent($london->getRegion());
		//$this->assertEquals(10427,count($cities));

		//cities of departement
		$cities = $this->repo->findCitiesByStateParent($london->getDepartement());
		$this->assertEquals(239,count($cities));

		//cities of district
		$cities = $this->repo->findCitiesByStateParent($london->getDistrict());
		$this->assertEquals(235,count($cities));

		//cities of division
		$cities = $this->repo->findCitiesByStateParent($london->getDivision());
		$this->assertEquals(3,count($cities));
	}

}