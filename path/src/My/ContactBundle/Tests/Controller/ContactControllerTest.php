<?php

namespace My\ContactBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
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

	public function testContactPage()
	{
		$now = new \DateTime('now');
		$now->modify('- 15 seconds');


		$crawler = $this->client->request('GET',$this->router->generate('my_contact'));
		
		$form = $crawler->selectButton('Envoyer')->form(array(
			'contact_form[email]' => 'testemail@local.host',
			'contact_form[title]' => 'Titre du message',
			'contact_form[message]' => 'Contenu du message',
			'contact_form[date]' => $now->format('Y-m-d H:i:s'),
			));
		$crawler = $this->client->submit($form);

		$this->assertEquals('My\ContactBundle\Controller\ContactController::contactAction',$this->client->getRequest()->attributes->get('_controller'));	
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);
	}

	public function testEmbeddedContactPage()
	{
		$now = new \DateTime('now');
		$now->modify('- 15 seconds');


		$crawler = $this->client->request('GET',$this->router->generate('ws_calendar'));
		
		$form = $crawler->selectButton('Envoyer')->form(array(
			'contact_form[email]' => 'testemail@local.host',
			'contact_form[title]' => 'Titre du message',
			'contact_form[message]' => 'Contenu du message',
			'contact_form[date]' => $now->format('Y-m-d H:i:s'),
			));
		$crawler = $this->client->submit($form);

		$this->assertEquals('My\ContactBundle\Controller\ContactController::contactAction',$this->client->getRequest()->attributes->get('_controller'));	
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);
	}

	public function testBotContactPage()
	{
		$now = new \DateTime('now');
		$data = array(
			'email' => 'botemail@local.host',
			'title' => 'iamabot',
			'message' => 'and i post bad message muaahaha',
			'date' => $now->format('Y-m-d H:i:s'),
			'login' => '',
			'_token' => $this->client->getContainer()->get('form.csrf_provider')->generateCsrfToken('my_contact'),);

		//submit the form too fast for human
		$crawler = $this->client->request('POST',$this->router->generate('my_contact'),array('contact_form' =>$data));
		$this->assertTrue($crawler->filter('body:contains("RobotUsingContactFormException")')->count() >= 1);
		
		//submit the form filled with the hidden filed
		$data['login'] = 'youshouldnotenteranythinghere';
		$data['date'] = $now->modify('- 15 seconds')->format('Y-m-d H:i:s');
		$crawler = $this->client->request('POST',$this->router->generate('my_contact'),array('contact_form'=>$data));
		$this->assertTrue($crawler->filter('body:contains("RobotUsingContactFormException")')->count() >= 1);
	}

}