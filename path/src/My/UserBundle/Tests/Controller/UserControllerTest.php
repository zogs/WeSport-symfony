<?php

namespace Ws\EventsBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserControllerTest extends WebTestCase
{

	public $client;
	public $router;
	public $em;
	private $username = 'testname';
	private $password = 'password';
	private $email    = 'testemail@local.host';

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
				'username' => $this->username,
				'email' => $this->email,
				'plainPassword' => array(
					'first' => $this->password,
					'second' => $this->password),
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
			'form[email]' => $this->email
			));

		$crawler = $this->client->submit($form);

		$this->assertEquals('My\UserBundle\Controller\UserController::requestActivationMailAction',$this->client->getRequest()->attributes->get('_controller'));	
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);
	}

	public function testActivation()
	{
		$user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername($this->username);

		$crawler = $this->client->request('GET',$this->router->generate('fos_user_registration_confirm',array('token'=>$user->getConfirmationToken())));

		$crawler = $this->client->followRedirect();

		$this->assertEquals('Ws\EventsBundle\Controller\CalendarController::loadAction',$this->client->getRequest()->attributes->get('_controller'));	
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);
	}

	private function logIn()
	{
		$crawler = $this->client->request('GET',$this->router->generate('fos_user_security_login'));
		$form = $crawler->selectButton('Connexion')->form(array('_username' => $this->username,'_password' => $this->password));
		$crawler = $this->client->submit($form);
		
		return $crawler;
	}

	private function logOut()
	{
		$crawler = $this->client->request('GET',$this->router->generate('fos_user_security_logout'));
	}

	public function testLogIn()
	{
		$this->logIn();
		$crawler = $this->client->followRedirect();
		$this->assertTrue($crawler->filter('body:contains("'.ucfirst($this->username).'")')->count() == 1);

	}

	public function testEdit()
	{
		$this->logIn();

		//$this->editProfile();

		$this->editInfo();

		$this->editMailingSettings();

		$this->editAvatar();

		$this->editPassword('newpass');
		$this->logOut();
		$this->logIn();
		$this->editPassword('password'); //reset password to the default one
	}

	public function editAvatar()
	{
		$user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername($this->username);
		$now = new \DateTime('now');
		$crawler = $this->client->request('GET',$this->router->generate('fos_user_profile_edit',array('action'=>'avatar')));
		$form = $crawler->selectButton('Mettre à jour')->form( array(
			'fos_user_profile_form[username]'=>$user->getUsername(),
			'fos_user_profile_form[email]' => $user->getEmail(),
			'fos_user_profile_form[action]'=>'avatar',
			'fos_user_profile_form[id]'=>$user->getId(),
			'fos_user_profile_form[avatar]' => array(
				'updated' => $now->format('Y-m-d H:i:s'),
				'file' => __DIR__.'/../../Resources/public/images/avatars/sim.jpg',
				)
			)
		);		   

		$crawler = $this->client->submit($form);
		$crawler = $this->client->followRedirect();

		$this->assertEquals('FOS\UserBundle\Controller\ProfileController::editAction',$this->client->getRequest()->attributes->get('_controller'));	
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);
	}

	public function editMailingSettings()
	{
		$user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername($this->username);

		$crawler = $this->client->request('GET',$this->router->generate('fos_user_profile_edit',array('action'=>'mailing')));
		$form = $crawler->selectButton('Mettre à jour')->form(array(
			'fos_user_profile_form[username]'=>$user->getUsername(),
			'fos_user_profile_form[email]' => $user->getEmail(),
			'fos_user_profile_form[action]'=>'mailing',
			'fos_user_profile_form[id]'=>$user->getId(),
			'fos_user_profile_form[settings]' => array(
				'event_confirmed' => 1,
				)
			));
		$crawler = $this->client->submit($form);
		$crawler = $this->client->followRedirect();

		$this->assertEquals('FOS\UserBundle\Controller\ProfileController::editAction',$this->client->getRequest()->attributes->get('_controller'));	
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);
	}

	public function editPassword($new_password)
	{
		$user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername($this->username);

		$crawler = $this->client->request('GET',$this->router->generate('fos_user_profile_edit',array('action'=>'password')));
		$form = $crawler->selectButton('Mettre à jour')->form(array(
			'fos_user_profile_form[username]'=>$user->getUsername(),
			'fos_user_profile_form[email]' => $user->getEmail(),
			'fos_user_profile_form[action]'=>'info',
			'fos_user_profile_form[id]'=>$user->getId(),
			'fos_user_profile_form[oldpassword]' => $this->password,
			'fos_user_profile_form[plainPassword]' => array(
				'first' => $new_password,
				'second' => $new_password
				)
			));
		$crawler = $this->client->submit($form);
		$crawler = $this->client->followRedirect();

		$this->assertEquals('FOS\UserBundle\Controller\ProfileController::editAction',$this->client->getRequest()->attributes->get('_controller'));	
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);

		$this->password = $new_password;

	}



	public function editInfo()
	{
		$user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername($this->username);
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

	public function testLogOut()
	{
		$this->logIn();
		$this->logOut();

		$crawler = $this->client->followRedirect();
		$this->assertTrue($crawler->filter('body:contains("'.ucfirst($this->username).'")')->count() == 0);
	}

	public function testDelete()
	{
		$crawler = $this->logIn();

		$user = $this->em->getRepository('MyUserBundle:User')->findOneByUsername($this->username);

		$crawler = $this->client->request('GET',$this->router->generate('fos_user_profile_edit',array('action'=>'delete')));
		
		$form = $crawler->selectButton('Confirmer')->form(array(
			'form[confirm]'=>'yes',
			));
		$crawler = $this->client->submit($form);
		$crawler = $this->client->followRedirect();

		$this->assertEquals('Ws\EventsBundle\Controller\CalendarController::loadAction',$this->client->getRequest()->attributes->get('_controller'));	
		$this->assertTrue($crawler->filter('.alert-success')->count() >= 1);
	}
}