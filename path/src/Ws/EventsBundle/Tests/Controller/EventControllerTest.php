<?php

namespace Ws\EventsBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AlertControllerTest extends WebTestCase
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
	
		 
	public function testView()
	{		
		//Test the view of an event 
		$crawler = $this->client->request('GET',$this->router->generate('ws_calendar_reset'));
		$link = $crawler->filter('.events:contains("Petit match entre amis")')->filter('.events-link')->link();
		$viewcrawler = $this->client->click($link);

		//Test if the page display the name of the events
		$this->assertTrue($viewcrawler->filter('.event-title:contains("Petit match entre amis")')->count() == 1);
		//Test the slug of the url
		$this->assertRegExp('/petit-match-entre-amis/',$this->client->getRequest()->attributes->get('slug'));		

	}

	public function testDelete()
	{
		$events = $this->em->getRepository('WsEventsBundle:Event')->findByTitle('To be deleted');
		$event = $events[0];

		$crawler = $this->client->request('GET',$this->router->generate('ws_event_edit',array('event'=>$event->getId())));
		$link = $crawler->selectLink("Supprimer l'annonce")->link();
		$crawler = $this->client->click($link);

		dump($crawler->filter('body')->text());
		dump($this->client->getResponse()->getStatusCode());
		//$crawler = $this->client->request('GET',$this->router->generate('ws_event_view',array('event'=>$event->getId(),'slug'=>$event->getSlug())));		

		//$this->assertTrue(404 === $this->client->getResponse()->getStatusCode());
	}

	/*
	public function testDeleteSerie()
	{
		$events = $this->em->getRepository('WsEventsBundle:Event')->findByTitle('To be deleted');
		$event = $events[0];
		$serie = $event->getSerie();

		//crawl to the delete url
		$crawler = $this->client->request('GET',$this->router->generate('ws_serie_delete',array('serie'=>$serie,'token'=>$this->csrfProvider->generateCsrfToken('serie_delete'))));

		//check if the page of the first event of the serie exist
		$crawler = $this->client->request('GET',$this->router->generate('ws_event_view',array('event'=>$event->getId(),'slug'=>$event->getSlug())));
		$this->assertTrue(404 === $this->client->getResponse()->getStatusCode());

		//check if the page of the second event of the serie exist
		$event = $events[1];
		$crawmer = $this->client->request('GET',$this->router->generate('ws_event_view',array('event'=>$event->getId(),'slug'=>$event->getSlug())));
		$this->assertTrue(404 === $this->client->getResponse()->getStatusCode());		

	}
	*/

}