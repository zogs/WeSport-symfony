<?php

namespace My\WorldBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LocationControllerTest extends WebTestCase
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

	public function testFormSelectLocation()
	{
		$london = $this->em->getRepository('MyWorldBundle:Location')->findLocationByCityName('London','UK');

		$crawler = $this->client->request('POST',$this->router->generate('my_world_location_select_test'),array(
			'location_select' => array(
				'country' => $london->getCountry()->getCode(),
				'region' => $london->getRegion()->getId(),
				'departement' => $london->getDepartement()->getId(),
				'district' => $london->getDistrict()->getId(),
				'division' => $london->getDivision()->getId(),
				'city' => $london->getCity()->getId(),
				'_token' => $this->client->getContainer()->get('form.csrf_provider')->generateCsrfToken('location_select'),
				)
			));

		$this->assertEquals('My\WorldBundle\Controller\LocationController::formSelectLocationAction',$this->client->getRequest()->attributes->get('_controller'));
		$this->assertTrue($crawler->filter('span.sf-dump-num:contains("'.$london->getId().'")')->count() >= 1);	
		
	}

	public function testNextGeoLevelAjax()
	{
		$london = $this->em->getRepository('MyWorldBundle:Location')->findLocationByCityName('London','UK');

		//country children
		$crawler = $this->client->request('GET',$this->router->generate('my_world_location_select_nextlevel',array('level'=>'country','value'=>$london->getCountry()->getId())));
		$response = $this->client->getResponse();
		$this->assertSame(200, $this->client->getResponse()->getStatusCode()); // Test if response is OK
		$this->assertSame('application/json', $response->headers->get('Content-Type')); // Test if Content-Type is valid application/json
		$this->assertNotEmpty($this->client->getResponse()->getContent()); // Test that response is not empty
		$count = substr_count($this->client->getResponse()->getContent(), 'value');
		$this->assertEquals(5,$count);

		//region children
		$crawler = $this->client->request('GET',$this->router->generate('my_world_location_select_nextlevel',array('level'=>'region','value'=>$london->getRegion()->getId())));
		$response = $this->client->getResponse();
		$this->assertSame(200, $this->client->getResponse()->getStatusCode()); // Test if response is OK
		$this->assertSame('application/json', $response->headers->get('Content-Type')); // Test if Content-Type is valid application/json
		$this->assertNotEmpty($this->client->getResponse()->getContent()); // Test that response is not empty
		$count = substr_count($this->client->getResponse()->getContent(), 'value');
		$this->assertEquals(10,$count);
		

		//departement children
		$crawler = $this->client->request('GET',$this->router->generate('my_world_location_select_nextlevel',array('level'=>'departement','value'=>$london->getDepartement()->getId())));
		$response = $this->client->getResponse();
		$this->assertSame(200, $this->client->getResponse()->getStatusCode()); // Test if response is OK
		$this->assertSame('application/json', $response->headers->get('Content-Type')); // Test if Content-Type is valid application/json
		$this->assertNotEmpty($this->client->getResponse()->getContent()); // Test that response is not empty
		$count = substr_count($this->client->getResponse()->getContent(), 'value');
		$this->assertEquals(2,$count);

		//district children
		$crawler = $this->client->request('GET',$this->router->generate('my_world_location_select_nextlevel',array('level'=>'district','value'=>$london->getDistrict()->getId())));
		$response = $this->client->getResponse();
		$this->assertSame(200, $this->client->getResponse()->getStatusCode()); // Test if response is OK
		$this->assertSame('application/json', $response->headers->get('Content-Type')); // Test if Content-Type is valid application/json
		$this->assertNotEmpty($this->client->getResponse()->getContent()); // Test that response is not empty
		$count = substr_count($this->client->getResponse()->getContent(), 'value');
		$this->assertEquals(34,$count);
		
		//division children
		$crawler = $this->client->request('GET',$this->router->generate('my_world_location_select_nextlevel',array('level'=>'division','value'=>$london->getDivision()->getId())));
		$response = $this->client->getResponse();
		$this->assertSame(200, $this->client->getResponse()->getStatusCode()); // Test if response is OK
		$this->assertSame('application/json', $response->headers->get('Content-Type')); // Test if Content-Type is valid application/json
		$this->assertNotEmpty($this->client->getResponse()->getContent()); // Test that response is not empty
		$count = substr_count($this->client->getResponse()->getContent(), 'value');
		$this->assertEquals(4,$count);

		//city can not have children		
	}

}