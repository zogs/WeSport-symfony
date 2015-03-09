<?php

namespace Ws\EventsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParticipationControllerTest extends WebTestCase 
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

	public function testParticipations()
	{
		$event = $this->em->getRepository('WsEventsBundle:Event')->findOneByTitle('Petit match entre amis');


		$user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername('user1');

		if($this->client->getContainer()->get('ws_events.manager')->isParticipating($event,$user)){

			$this->tryCancel($event);
			$this->tryAdd($event);
		}
		else {
			$this->tryAdd($event);
			$this->tryCancel($event);
			$this->tryAdd($event);
		}	
	}

	public function tryAdd($event)
	{
		$crawler = $this->client->request('GET',$this->router->generate('ws_participation_add',array('event'=>$event->getId(),'token'=>$this->client->getContainer()->get('form.csrf_provider')->generateCsrfToken('participation_add'))));

		$this->assertEquals("Ws\EventsBundle\Controller\ParticipationController::addAction",$this->client->getRequest()->attributes->get('_controller'));

		$crawler = $this->client->followRedirect();
		$this->assertEquals("Ws\EventsBundle\Controller\EventController::viewAction",$this->client->getRequest()->attributes->get('_controller'));
		$this->assertTrue($crawler->filter('.event-title:contains("'.$event->getTitle().'")')->count() >= 1);
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);
	}	

	public function tryCancel($event)
	{
		$this->client->request('GET',$this->router->generate('ws_participation_cancel',array('event'=>$event->getId(),'token'=>$this->client->getContainer()->get('form.csrf_provider')->generateCsrfToken('participation_cancel'))));

		$this->assertEquals("Ws\EventsBundle\Controller\ParticipationController::cancelAction",$this->client->getRequest()->attributes->get('_controller'));

		$crawler = $this->client->followRedirect();

		$this->assertEquals("Ws\EventsBundle\Controller\EventController::viewAction",$this->client->getRequest()->attributes->get('_controller'));
		$this->assertTrue($crawler->filter('.event-title:contains("'.$event->getTitle().'")')->count() >= 1);
		$this->assertTrue($crawler->filter('.alert-info')->count() >= 1);
	}
	
}