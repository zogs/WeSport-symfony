<?php

namespace Ws\EventsBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
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

	public function testCreate()
	{
		$location = $this->em->getRepository('MyWorldBundle:Location')->findLocationByCityName('Dijon','FR');

		$crawler = $this->client->request('POST',$this->router->generate('fos_user_registration_register'),array(
			'fos_user_registration_form' => array(
				'type' => 'person',
				'username' => 'testname',
				'email' => 'testemail@local.host',
				'plainPassword' => array(
					'first' => 'pa$$word',
					'second' => 'pa$$word'),
				'birthday' => array(
					'day' => 1,
					'month' => 1,
					'year' => 1979),
				'gender' => 'f',
				'location'=> array(
					'country' => $location->getCountry()->getCode(),
					'region' => $location->getRegion()->getId(),
					'departement' => $location->getDepartement()->getId(),
					'city' => $location->getCity()->getId()
					),
				'_token' => $this->client->getContainer()->get('form.csrf_provider')->generateCsrfToken('user_registration_intention'),
				)
			)
		);

		$crawler = $this->client->followRedirect();

		$this->assertEquals('Ws\EventsBundle\Controller\CalendarController::loadAction',$this->client->getRequest()->attributes->get('_controller'));	
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);
	}

	public function testRequestActivationMail()
	{
		$crawler = $this->client->request('GET',$this->router->generate('my_user_request_activation_mail'));

		$form = $crawler->selectButton('Envoyer')->form(array(
			'form[email]' => 'testemail@local.host'
			));

		$crawler = $this->client->submit($form);

		$this->assertEquals('My\UserBundle\Controller\UserController::requestActivationMailAction',$this->client->getRequest()->attributes->get('_controller'));	
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);
	}
	/*
	public function testLoginBeforeActivation()
	{
		$crawler = $this->client->request('GET',$this->router->generate('fos_user_security_login'));

		$form = $crawler->selectButton('Connexion')->form(array(
			'_username' => 'testname',
			'_password' => 'pa$$word',
			));

		$crawler = $this->client->submit($form);

		$crawler = $this->client->followRedirect();

		dump($crawler->filter('body')->text());
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);

	}
	*/
	public function testActivationMail()
	{
		$user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername('testname');

		$crawler = $this->client->request('GET',$this->router->generate('fos_user_registration_confirm',array('token'=>$user->getConfirmationToken())));

		$crawler = $this->client->followRedirect();

		$this->assertEquals('Ws\EventsBundle\Controller\CalendarController::loadAction',$this->client->getRequest()->attributes->get('_controller'));	
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);
	}

	public function testLoginActivation()
	{
		$crawler = $this->client->request('GET',$this->router->generate('fos_user_security_login'));

		$form = $crawler->selectButton('Connexion')->form(array(
			'_username' => 'testname',
			'_password' => 'pa$$word',
			));

		$crawler = $this->client->submit($form);

		$crawler = $this->client->followRedirect();

		$this->assertTrue($crawler->filter('.nav-username:contains("testname")')->count() == 1);

	}

	public function testDelete()
	{
		$user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername('testname');

		$this->client->getContainer()->get('fos_user.user_manager')->deleteUser($user);

		$this->assertNull($this->em->getRepository('MyUserBundle:User')->findOneByUsername('testname'));
	}

	
	
}