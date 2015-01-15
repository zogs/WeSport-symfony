<?php

namespace Ws\EventsBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CityControllerTest extends WebTestCase
{

	public $client;
	public $router;
	public $em;

	/**
	 * PHPUnit setup
	 */
	public function setUp()
	{
		
		$this->client = self::createClient();	


		$this->router = $this->client->getContainer()->get('router');
		$this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
	}
	/**
	 * PHPUnit close up
	 */
	protected function tearDown()
	{
		$this->em->close();
		unset($this->client, $this->em);
	}

	public function testFromId()
	{

		$crawler = $this->client->request('GET',$this->router->generate('my_world_city_search'));
		$form = $crawler->selectButton('Rechercher')->form(array(
			'city_to_location_type[city_id]'=>2568787,
			));
		$crawler = $this->client->submit($form);
		$crawler = $this->client->followRedirect();
		$this->assertEquals('My\WorldBundle\Controller\CityController::viewAction',$this->client->getRequest()->attributes->get('_controller'));		
		$this->assertTrue($crawler->filter('body:contains("Dijon")')->count() == 1);

	}

	public function testFromFailId()
	{
		//Test a city that do not exist
		$crawler = $this->client->request('GET',$this->router->generate('my_world_city_search'));
		$form = $crawler->selectButton('Rechercher')->form(array(
			'city_to_location_type[city_id]'=>2525,
			));
		$this->client->submit($form);		
		
		$this->assertEquals(500,$this->client->getResponse()->getStatusCode()); //id cant be find in database so trigger an 500 server error



	}

}