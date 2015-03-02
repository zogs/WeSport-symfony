<?php

namespace My\WorldBundle\Tests\Manager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LocationManagerTest extends WebTestCase
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
		$this->client = self::createClient(array(),array('PHP_AUTH_USER' => 'user1','PHP_AUTH_PW' => 'pass'));	
		$this->router = $this->client->getContainer()->get('router');
		$this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
		$this->manager = $this->client->getContainer()->get('world.location_manager');
	}
	/**
	 * PHPUnit close up
	 */
	protected function tearDown()
	{
		$this->em->close();
		unset($this->client, $this->em);
	}


	public function testLocationFromCityId()
	{
		$cities = array(
			array('id'=>2928207,'name'=>'Galway'),
			array('id'=>2568787,'name'=>'Dijon'),
			array('id'=>2599076,'name'=>'Tarbes'),
			);

		foreach ($cities as $key => $city) {
			
			$location = $this->manager->getLocationFromCityId($city['id']);
			$this->assertEquals($city['name'],$location->getCity()->getName());
		}
	}

	public function testLocationFromCityName()
	{
		$cities = array('Dijon','Beaune','Le Conquet','London');

		foreach ($cities as $key => $name) {
			
			$location = $this->manager->getLocationFromCityName($name);
			$this->assertEquals($name,$location->getCity()->getName());
		}
	}

	public function testGetLocationFromNearestCityLatLon()
	{
		//Dijon
		$lat = 47.321868;
		$lon = 5.039458;
		$pays = 'France';
		$location = $this->manager->getLocationFromNearestCityLatLon($lat,$lon,$pays);
		$this->assertEquals('Dijon',$location->getCity()->getName());	

		//Le Conquet
		$lat = 48.359109;
		$lon = -4.763050;
		$location = $this->manager->getLocationFromNearestCityLatLon($lat,$lon);
		$this->assertEquals('Le Conquet',$location->getCity()->getName());	
	}
}