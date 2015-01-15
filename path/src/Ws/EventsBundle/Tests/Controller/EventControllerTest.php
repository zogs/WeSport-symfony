<?php

namespace Ws\EventsBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{

	public $client;
	public $router;
	public $em;

	/**
	 * PHPUnit setup
	 */
	public function setUp()
	{
		
		$this->client = self::createClient(array(),array(
			'PHP_AUTH_USER' => 'user1',
			'PHP_AUTH_PW' => 'fatboy',
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
	
	/**
	 * Test creation of an event
	 */
	public function testCreate()
	{
		//crawl to formular
		$crawler = $this->client->request('GET',$this->router->generate('ws_event_new'));
		//get needed entities
		$spots = $this->em->getRepository('WsEventsBundle:Spot')->findAll();
		$sports = $this->em->getRepository('WsSportsBundle:Sport')->findAll();
		$now = new \Datetime('now');
		//fill form
		$form = $crawler->selectButton('Sauvegarder')->form(array(
				'event[sport]' => $sports[0]->getId(),
				'event[title]' => 'Test-event',
				//'event[spot][spot_id]' => $spots[0]->getId(),
				'event[spot][location][city_name]' => 'Dijon',
				'event[spot][name]' => 'Spot de test',
				'event[spot][address]' => 'Adresse de test',
				//'event[date]' => $now->format('d/m/Y'),
				'event[serie][startdate]' => $now->format('d/m/Y'),
				'event[serie][enddate]' => $now->modify('+ 1 month')->format('d/m/Y'),
				'event[serie][monday]' => 1, 'event[serie][friday]' => 1,
				'event[time]' => $now->format('H:i'),
				'event[description]' => 'Created by automated tests - to be destroy be automated test -',
				'event[nbmin]' => 10,
				'event[level]' => 'expert',
				'event[price]' => 4,
				'event[public]' => false,				
			));
		//submit form
		$crawler = $this->client->submit($form);

		$crawler = $this->client->followRedirect();
		$this->assertEquals('Ws\EventsBundle\Controller\EventController::viewAction',$this->client->getRequest()->attributes->get('_controller'));	

	}

	/**
	 * Test failing creating of an error
	 */
	public function testCreateErrors()
	{
		$crawler = $this->client->request('GET',$this->router->generate('ws_event_new'));

		$now = new \Datetime('now');

		$form = $crawler->selectButton('Sauvegarder')->form(array(
				'event[spot][spot_id]' => 649681651658416,
				'event[spot][location][city_name]' => 'A-city-that-do-not-exist',
				'event[date]' => $now->modify('- 1 day')->format('d/m/Y'),
				'event[serie][startdate]' => $now->format('Y-m-d'),
				'event[serie][enddate]' => 'today',
				'event[time]' => $now->format('s:i:h'),
			));

		$crawler = $this->client->submit($form);

		$this->assertTrue($crawler->filter('.cell-error,.field_error')->count() > 0);
	}	

	/**
	 * Test edition of an event
	 */
	public function testEdit()
	{
		$event = $this->em->getRepository('WsEventsBundle:Event')->findOneByTitle('Test-event');

		$crawler = $this->client->request('GET',$this->router->generate('ws_event_edit',array('event'=>$event->getId())));

		$spots = $this->em->getRepository('WsEventsBundle:Spot')->findAll();
		$sports = $this->em->getRepository('WsSportsBundle:Sport')->findAll();
		$now = new \Datetime('now');

		$form = $crawler->selectButton('Sauvegarder')->form(array(
				'event[sport]' => $sports[1]->getId(),
				'event[title]' => 'Test-event',
				'event[spot][spot_id]' => $spots[0]->getId(),
				'event[date]' => $now->modify('+3 day')->format('d/m/Y'),
				'event[time]' => $now->format('H:i'),
				'event[description]' => '[Edited] Created by automated tests - to be destroy be automated test - ',
				'event[nbmin]' => 100,
				'event[level]' => 'beginner',
				'event[price]' => 2,
				'event[public]' => false,				
			));

		$crawler = $this->client->submit($form);

		$this->assertEquals('Ws\EventsBundle\Controller\EventController::editAction',$this->client->getRequest()->attributes->get('_controller'));
		$this->assertEquals($crawler->filter('.cell-error')->count(),0);	
	}	
	
	/**
	 * Test view of an event
	 */
	public function testView()
	{		
		$this->client->restart();
		//find test events
		$testevents = $this->em->getRepository('WsEventsBundle:Event')->findByTitle('Test-event');
		//crawl to the view page
		$crawler = $this->client->request('GET',$this->router->generate('ws_event_view',array('event'=>$testevents[0]->getId(),'slug'=>$testevents[0]->getSlug())));
		//Test if the page display the name of the event
		$this->assertTrue($crawler->filter('.event-title:contains("Test-event")')->count() == 1);
		//Test the slug of the url
		$this->assertRegExp('/test-event/',$this->client->getRequest()->attributes->get('slug'));		

	}
	
	/**
	 * Test deletion of an event
	 */
	public function testDelete()
	{		
		//get the event to test
		$event      = $this->em->getRepository('WsEventsBundle:Event')->findOneByTitle('Test-event');
		$event_id   = $event->getId();
		$event_slug = $event->getSlug();

		//restart client
		$this->client->restart();
		
		//run the delete url
		$token = $this->client->getContainer()->get('form.csrf_provider')->generateCsrfToken('event_delete');	
		$crawler = $this->client->request('DELETE',$this->router->generate('ws_event_delete',array('event'=>$event_id,'token'=>$token)));			
		
		//check if view event is foundable		
		$crawler = $this->client->request('GET',$this->router->generate('ws_event_view',array('event'=>$event_id,'slug'=>$event_slug)));
					
		$this->assertTrue(404 === $this->client->getResponse()->getStatusCode());
	}

	/**
	 * Test deletion of an entire serie
	 */
	public function testDeleteSerie()
	{
		//get the serie and the events to test
		$events = $this->em->getRepository('WsEventsBundle:Event')->findByTitle('Test-event');
		$serie = $events[0]->getSerie();
		foreach ($events as $k => $event) {
			$events[$k] = array(
				'id' => $event->getId(),
				'slug' => $event->getSlug()
				);
		}

		//crawl to the delete url
		$token = $this->client->getContainer()->get('form.csrf_provider')->generateCsrfToken('serie_delete');	
		$crawler = $this->client->request('DELETE',$this->router->generate('ws_serie_delete',array('serie'=>$serie->getId(),'token'=>$token)));	

		//check if the page of the events return 404
		foreach ($events as $event) {
						
			$crawler = $this->client->request('GET',$this->router->generate('ws_event_view',array('event'=>$event['id'],'slug'=>$event['slug'])));		
			$this->assertTrue(404 === $this->client->getResponse()->getStatusCode());			
		}
	}	
}