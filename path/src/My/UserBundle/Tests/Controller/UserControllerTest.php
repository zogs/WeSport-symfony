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
		$crawler = $this->client->request('GET',$this->router->generate('fos_user_registration_register'));

		$location = $this->em->getRepository('MyWorldBundle:Location')->findLocationByCityName('Dijon','FR');

		$form = $crawler->selectButton("S'inscrire")->form(array(
			'fos_user_registration_form[username]' => 'testname',
			'fos_user_registration_form[email]' => 'testemail@local.host',
			'fos_user_registration_form[plainPassword][first]' => 'pa$$word',
			'fos_user_registration_form[plainPassword][second]' => 'pa$$word',
			'fos_user_registration_form[birthday][day]' => 1,
			'fos_user_registration_form[birthday][month]' => 1,
			'fos_user_registration_form[birthday][year]' => 1979,
			'fos_user_registration_form[gender]' => 'm',
			'fos_user_registration_form[location][country]' => $location->getCountry()->getCode(),
			'fos_user_registration_form[location][region]' => $location->getRegion()->getId(),
			'fos_user_registration_form[location][departement]' => $location->getDepartement()->getId(),
			'fos_user_registration_form[location][city]' => $location->getCity()->getId(),
			));

		$crawler = $this->client->submit($form);

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

	public function testActivationMail()
	{
		$user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername('testname');

		$crawler = $this->client->request('GET',$this->router->generate('fos_user_registration_confirm',array('token'=>$user->getConfirmationToken())));

		$crawler = $this->client->followRedirect();

		$this->assertEquals('Ws\EventsBundle\Controller\CalendarController::loadAction',$this->client->getRequest()->attributes->get('_controller'));	
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);
	}

	public function testDelete()
    {
        $user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername('testname');

        $this->client->getContainer()->get('fos_user.user_manager')->deleteUser($user);

        $this->assertNull($this->em->getRepository('MyUserBundle:User')->findOneByUsername('testname'));
    }

	
	
}