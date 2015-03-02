<?php

namespace Ws\EventsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InvitationControllerTest extends WebTestCase
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

	/**
	 * Test the creation of an Invitation
	 */
	public function testCreate()
	{
		$event = $this->em->getRepository('WsEventsBundle:Event')->findOneByTitle('Petit match entre amis');

		$crawler = $this->client->request('GET',$this->router->generate('ws_invit_create',array('event'=>$event->getId())));

		$form = $crawler->filter("form[name=invitation]")->form(array(
			'invitation[emails]' => 'invited1@local.host,invited2@local.host',
			'invitation[content]' => "Texte de l'invitation de test",
			'invitation[event]' => $event->getId(),			
			));

		$crawler = $this->client->submit($form);

		$this->assertEquals('Ws\EventsBundle\Controller\InvitationController::createAction',$this->client->getRequest()->attributes->get('_controller'));
	}

	/**
	 * Test the ajax email suggestions 
	 */
	public function testAjaxEmailAutoComplete()
	{
		$crawler = $this->client->request('GET',$this->router->generate('ws_inviter_emails_ajax'));

		$this->assertSame(200, $this->client->getResponse()->getStatusCode()); // Test if response is OK
		$this->assertSame('application/json', $this->client->getResponse()->headers->get('Content-Type')); // Test if Content-Type is valid application/json
		$this->assertNotEmpty($this->client->getResponse()->getContent()); // Test that response is not empty
		$this->assertRegExp('/id/',$this->client->getResponse()->getContent());
	}

	public function testResend()
	{
		$invitation = $this->em->getRepository('WsEventsBundle:Invitation')->findOneByContent("Texte de l'invitation de test");
		$invited = $invitation->getInvited();
		$invited = $invited[0];

		//try first resend
		$crawler = $this->client->request('GET',$this->router->generate('ws_invit_resend',array('invited'=>$invited->getId())));
		$crawler = $this->client->followRedirect();
		//test
		$this->assertEquals('Ws\EventsBundle\Controller\EventController::viewAction',$this->client->getRequest()->attributes->get('_controller'));
		$this->assertTrue($crawler->filter('.alert-info')->count() >=1);


		//try second resend
		$crawler = $this->client->request('GET',$this->router->generate('ws_invit_resend',array('invited'=>$invited->getId())));
		$crawler = $this->client->followRedirect();
		//test
		$this->assertEquals('Ws\EventsBundle\Controller\EventController::viewAction',$this->client->getRequest()->attributes->get('_controller'));
		$this->assertTrue($crawler->filter('.alert-info')->count() >=1);

		//try third resend
		$crawler = $this->client->request('GET',$this->router->generate('ws_invit_resend',array('invited'=>$invited->getId())));
		$crawler = $this->client->followRedirect();
		//test fail
		$this->assertEquals('Ws\EventsBundle\Controller\EventController::viewAction',$this->client->getRequest()->attributes->get('_controller'));
		$this->assertTrue($crawler->filter('.alert-warning')->count() >=1);
	}

	public function testConfirm()
	{
		$invitation = $this->em->getRepository('WsEventsBundle:Invitation')->findOneByContent("Texte de l'invitation de test");
		$invited = $invitation->getInvited();
		$invited = $invited[0];

		$crawler = $this->client->request('GET',$this->router->generate('ws_invited_confirm',array('invited'=>$invited->getId())));
		$crawler = $this->client->followRedirect();

		$this->assertEquals('Ws\EventsBundle\Controller\EventController::viewAction',$this->client->getRequest()->attributes->get('_controller'));
		$this->assertTrue($crawler->filter('.alert-success')->count() >=1);
	}

	public function testDeny()
	{
		$invitation = $this->em->getRepository('WsEventsBundle:Invitation')->findOneByContent("Texte de l'invitation de test");
		$invited = $invitation->getInvited();
		$invited = $invited[1];

		$crawler = $this->client->request('GET',$this->router->generate('ws_invited_deny',array('invited'=>$invited->getId())));
		$crawler = $this->client->followRedirect();

		$this->assertEquals('Ws\EventsBundle\Controller\EventController::viewAction',$this->client->getRequest()->attributes->get('_controller'));
		$this->assertTrue($crawler->filter('.alert-warning')->count() >=1);
	}

	public function testAddBlacklisted()
	{
		$crawler = $this->client->request('GET',$this->router->generate('ws_invit_blacklist_add',array('emails'=>'invited1@local.host')));

		$crawler = $this->client->followRedirect();

		$this->assertEquals('Ws\EventsBundle\Controller\CalendarController::loadAction',$this->client->getRequest()->attributes->get('_controller'));
		$this->assertTrue($crawler->filter('.alert-success')->count() >=1);
	}

	public function testRemoveBlacklisted()
	{
		$crawler = $this->client->request('GET',$this->router->generate('ws_invit_blacklist_remove',array('emails'=>'invited1@local.host')));

		$crawler = $this->client->followRedirect();

		$this->assertEquals('Ws\EventsBundle\Controller\CalendarController::loadAction',$this->client->getRequest()->attributes->get('_controller'));
		$this->assertTrue($crawler->filter('.alert-success')->count() >=1);
	}

	public function testDelete()
	{
		$invitation = $this->em->getRepository('WsEventsBundle:Invitation')->findOneByContent("Texte de l'invitation de test");

		$this->assertTrue($this->client->getContainer()->get('ws_events.invit.manager')->deleteInvit($invitation));
	}

}	