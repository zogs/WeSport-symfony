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
					'first' => 'pass',
					'second' => 'pass'),
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
			'_password' => 'pass',
			));

		$crawler = $this->client->submit($form);

		$crawler = $this->client->followRedirect();

		dump($crawler->filter('body')->text());
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);

	}
	*/
	public function testActivation()
	{
		$user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername('testname');

		$crawler = $this->client->request('GET',$this->router->generate('fos_user_registration_confirm',array('token'=>$user->getConfirmationToken())));

		$crawler = $this->client->followRedirect();

		$this->assertEquals('Ws\EventsBundle\Controller\CalendarController::loadAction',$this->client->getRequest()->attributes->get('_controller'));	
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);
	}

	public function testLogin()
	{
		$this->logIn();

		$crawler = $this->client->followRedirect();
		$this->assertTrue($crawler->filter('body:contains("Testname")')->count() == 1);

	}

	private function logIn()
	{
		$crawler = $this->client->request('GET',$this->router->generate('fos_user_security_login'));
		$form = $crawler->selectButton('Connexion')->form(array('_username' => 'testname','_password' => 'pass'));
		$crawler = $this->client->submit($form);
	}

	public function testEditProfile()
	{
		$this->logIn();

		$user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername('testname');
		$loc_moloy = $this->em->getRepository('MyWorldBundle:Location')->findLocationByCityName('Moloy','FR');

		$crawler = $this->client->request('GET',$this->router->generate('fos_user_profile_edit',array('action'=>'info')));
		$form = $crawler->selectButton('Mettre à jour')->form(array(
			'fos_user_profile_form[username]'=>$user->getUsername(),
			'fos_user_profile_form[email]' => $user->getEmail(),
			'fos_user_profile_form[action]'=>'info',
			'fos_user_profile_form[id]'=>$user->getId(),
			'fos_user_profile_form[firstname]' => 'my firstname',
			'fos_user_profile_form[lastname]' => 'my lastname',
			'fos_user_profile_form[description]' => 'my description',
			'fos_user_profile_form[gender]' => 0,
			'fos_user_profile_form[birthday]'=>array(
				'day'=> '1',
				'month'=> '1',
				'year'=> '2001',
				),
			'fos_user_profile_form[location]'=> array(
				'country'=>$loc_moloy->getCountry()->getCode(),
				'region'=>$loc_moloy->getRegion()->getId(),
				'departement'=>$loc_moloy->getDepartement()->getId(),
				'district'=>'',
				'division'=>'',
				'city'=>$loc_moloy->getCity()->getId(),
				),
			));
		$crawler = $this->client->submit($form);
		$crawler = $this->client->followRedirect();

		$this->assertEquals('FOS\UserBundle\Controller\ProfileController::editAction',$this->client->getRequest()->attributes->get('_controller'));	
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);
		

	}

	public function testLogout()
	{
		$this->logIn();
		$crawler = $this->client->request('GET',$this->router->generate('fos_user_security_logout'));
		$crawler = $this->client->followRedirect();
		$this->assertTrue($crawler->filter('body:contains("Testname")')->count() == 0);
	}

	public function testDelete()
	{
		$user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername('testname');

		$this->client->getContainer()->get('fos_user.user_manager')->deleteUser($user);

		$this->assertNull($this->em->getRepository('MyUserBundle:User')->findOneByUsername('testname'));
	}
}