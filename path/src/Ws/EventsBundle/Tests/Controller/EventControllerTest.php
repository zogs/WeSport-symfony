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

	public function testCreate()
	{
		$crawler = $this->client->request('GET',$this->router->generate('ws_event_new'));

		$spots = $this->em->getRepository('WsEventsBundle:Spot')->findAll();
		$sports = $this->em->getRepository('WsSportsBundle:Sport')->findAll();
		$now = new \Datetime('now');

		$form = $crawler->selectButton('Sauvegarder')->form(array(
				'event[sport]' => $sports[0]->getId(),
				'event[title]' => 'Test-event to be deleted',
				'event[spot][spot_id]' => $spots[0]->getId(),
				'event[date]' => $now->format('d/m/Y'),
				'event[time]' => $now->format('H:i'),
				'event[description]' => 'Created by automated tests - to be destroy be automated test -',
				'event[nbmin]' => 10,
				'event[level]' => 'expert',
				'event[price]' => 4,				
			));

		$crawler = $this->client->submit($form);

		$crawler = $this->client->followRedirect();

		//Test if the page display the name of the event
		$this->assertTrue($crawler->filter('.event-title:contains("Test-event to be deleted")')->count() == 1);

	}		
	
	/*	 
	public function testView()
	{		
		//Test the view of an event 
		$crawler = $this->client->request('GET',$this->router->generate('ws_calendar_reset'));
		$link = $crawler->filter('.events:contains("Petit match entre amis")')->filter('.events-link')->link();
		$viewcrawler = $this->client->click($link);

		//Test if the page display the name of the event
		$this->assertTrue($viewcrawler->filter('.event-title:contains("Petit match entre amis")')->count() == 1);
		//Test the slug of the url
		$this->assertRegExp('/petit-match-entre-amis/',$this->client->getRequest()->attributes->get('slug'));		

	}

	public function testDelete()
	{
		$events = $this->em->getRepository('WsEventsBundle:Event')->findByTitle('To be deleted');
		$event = $events[0];

		//run the delete url
		$crawler = $this->client->request('GET',$this->router->generate('ws_event_edit',array('event'=>$event->getId())));
		$link = $crawler->selectLink("Supprimer l'annonce")->link();
		$crawler = $this->client->click($link);

		//check if view event return 404
		$crawler = $this->client->request('GET',$this->router->generate('ws_event_view',array('event'=>$event->getId(),'slug'=>$event->getSlug())));		
		$this->assertTrue(404 === $this->client->getResponse()->getStatusCode());
	}

	
	public function testDeleteSerie()
	{
		$events = $this->em->getRepository('WsEventsBundle:Event')->findByTitle('To be deleted');
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