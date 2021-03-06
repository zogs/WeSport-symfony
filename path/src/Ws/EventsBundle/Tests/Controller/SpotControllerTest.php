<?php

namespace Ws\EventsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SpotControllerTest extends WebTestCase
{
	public $client;
	public $router;
	public $em;
	/**
	 * PHPUnit setup
	 */
	public function setUp()
	{
		$this->client = static::createClient(array(),array(
			'PHP_AUTH_USER' => 'user1',
			'PHP_AUTH_PW' => 'pass',
			));

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

	public function testCreate()
	{
		$crawler = $this->client->request('GET',$this->router->generate('ws_spot_create'));
		
		$form = $crawler->filter("form[name=spot_type]")->form(array(
			'spot_type[spot_slug]' => '',
			'spot_type[location][city_name]' => 'Paris',
			'spot_type[name]' => 'PhpUnit Automated Spot Test',
			'spot_type[address]' => 'Test Adress',			
			));

		$crawler = $this->client->submit($form);

		$crawler = $this->client->followRedirect();

		$this->assertEquals('Ws\EventsBundle\Controller\SpotController::indexAction',$this->client->getRequest()->attributes->get('_controller'));
	}

	public function testDelete()
	{
		$spot = $this->em->getRepository('WsEventsBundle:Spot')->findOneByName('PhpUnit Automated Spot Test');
		
		$this->client->getContainer()->get('ws_events.spot.manager')->deleteSpot($spot);

		$this->assertNull($spot->getId());
	}

	public function testAutoComplete()
	{
		$string = 'beaune';
		$suggestions = $this->em->getRepository('WsEventsBundle:Spot')->findSuggestions(20,$string,'FR');
		$this->assertEquals(2,count($suggestions));

		$string = 'parc';
		$suggestions = $this->em->getRepository('WsEventsBundle:Spot')->findSuggestions(20,$string,'FR');
		$this->assertEquals(3,count($suggestions));

		$string = 'piscine';
		$suggestions = $this->em->getRepository('WsEventsBundle:Spot')->findSuggestions(20,$string,'FR');
		$this->assertEquals(1,count($suggestions));

	}

	public function testAjaxAutoComplete()
	{
		$crawler = $this->client->request('GET',$this->router->generate('ws_spot_autocomplete',array('country'=>'FR','search'=>'parc')));

		$this->assertSame(200, $this->client->getResponse()->getStatusCode()); // Test if response is OK
		$this->assertSame('application/json', $this->client->getResponse()->headers->get('Content-Type')); // Test if Content-Type is valid application/json
		$this->assertNotEmpty($this->client->getResponse()->getContent()); // Test that response is not empty
		$this->assertRegExp('/parc/',$this->client->getResponse()->getContent());
	}
}