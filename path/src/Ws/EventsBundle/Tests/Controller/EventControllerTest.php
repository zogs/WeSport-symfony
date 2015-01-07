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

		$crawler = $this->client->request('GET',$this->router->generate('ws_event_delete',array('event'=>$event->getId(),'token'=>$this->csrfProvider->generateCsrfToken('event_delete'))));
		$crawler = $this->client->request('GET',$this->router->generate('ws_event_view',array('event'=>$event->getId(),'slug'=>$event->getSlug())));
		dump($this->client->getResponse()->getStatusCode());
		$this->assertTrue(404 === $this->client->getResponse()->getStatusCode());
	}

}