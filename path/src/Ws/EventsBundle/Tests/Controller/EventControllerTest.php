<?php

namespace Ws\EventsBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{

	public $client;
	public $router;
	public $em;

	public function __construct()
	{
		
		$this->client = static::createClient(array(),array(
			'PHP_AUTH_USER' => 'user1',
			'PHP_AUTH_PW' => 'fatboy',
			));	

		$this->router = $this->client->getContainer()->get('router');
		$this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
		$this->csrfProvider = $this->client->getContainer()->get('form.csrf_provider');

	}
	/*
	public function testCreate()
	{
		$crawler = $this->client->request('GET',$this->router->generate('ws_event_new'));

		$spots = $this->em->getRepository('WsEventsBundle:Spot')->findAll();
		$sports = $this->em->getRepository('WsSportsBundle:Sport')->findAll();
		$now = new \Datetime('now');

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

		$crawler = $this->client->submit($form);

		$crawler = $this->client->followRedirect();

		$this->assertEquals('Ws\EventsBundle\Controller\EventController::viewAction',$this->client->getRequest()->attributes->get('_controller'));		
	}		
	
	
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
	*/

	public function testDelete()
	{
		//get the event to test
		$events = $this->em->getRepository('WsEventsBundle:Event')->findByTitle('Test-event');
		$event = $events[0];
		$event_id = $event->getId();
		$event_slug = $event->getSlug();

		//restart client
		$this->client->restart();
		
		//run the delete url
		$token = $this->csrfProvider->generateCsrfToken('event_delete');	
		$crawler = $this->client->request('GET',$this->router->generate('ws_event_delete',array('event'=>$event_id,'token'=>$token)));			
		
		//check if view event return 404
		$crawler = $this->client->request('GET',$this->router->generate('ws_event_view',array('event'=>$event_id,'slug'=>$event_slug)));			
		$this->assertTrue(404 === $this->client->getResponse()->getStatusCode());
	}

	/*
	public function testDeleteSerie()
	{
		$events = $this->em->getRepository('WsEventsBundle:Event')->findByTitle('Test-event');
		$event = $events[0];
		$serie = $event->getSerie();

		//crawl to the delete url
		$crawler = $this->client->request('GET',$this->router->generate('ws_event_edit',array('event'=>$event->getId())));
		$link = $crawler->selectLink("Supprimer toute la série")->link();
		$crawler = $this->client->click($link);		

		//check if the page of the first event of the serie return 404
		$crawler = $this->client->request('GET',$this->router->generate('ws_event_view',array('event'=>$event->getId(),'slug'=>$event->getSlug())));
		$this->assertTrue(404 === $this->client->getResponse()->getStatusCode());

		//check if the page of the second event of the serie return 404
		$event = $events[1];
		$crawmer = $this->client->request('GET',$this->router->generate('ws_event_view',array('event'=>$event->getId(),'slug'=>$event->getSlug())));
		$this->assertTrue(404 === $this->client->getResponse()->getStatusCode());		

	}
	*/

}