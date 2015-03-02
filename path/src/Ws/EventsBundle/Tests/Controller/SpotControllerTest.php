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