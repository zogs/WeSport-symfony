<?php

namespace Ws\EventsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Ws\EventsBundle\Manager\CalendarUrlGenerator;

class AlertControllerTest extends WebTestCase
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
	 * Test the index of Alert of User1
	*/
		 
	public function testIndex()
	{		
		$crawler = $this->client->request('GET',$this->router->generate('ws_alerts_index'));
		$this->assertEquals(3,$crawler->filter('.row-alert')->count());		
		$this->assertEquals(2,$crawler->filter('.row-alert-active')->count());
		$this->assertEquals(1,$crawler->filter('.row-alert-pending')->count());
	}

	/**
	 * Test the creation of an Alert
	 */
	public function testCreate()
	{
		$crawler = $this->client->request('GET',$this->router->generate('ws_alerts_create'));
		
		$sports = $this->em->getRepository('WsSportsBundle:Sport')->findAll();
		foreach ($sports as $k => $sport) {
			$sports[$k] = $sport->getId();
		}

		$form = $crawler->filter("form[name=alert]")->form(array(
			'alert[frequency]' => 'daily',
			'alert[duration]' => 6,
			'alert[email]' => 'test@local.host',
			'alert[search][sports]' => $sports,
			'alert[search][level]' => array('beginner','confirmed','expert'), 
			'alert[search][type]' => array('person','pro','asso'),
			'alert[search][price]' => 0, 
			'alert[search][location][city_name]' => 'Dijon', 
			'alert[search][area]' => 50,
			'alert[search][dayofweek]' => array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
			'alert[search][timestart]' => '01:00',
			'alert[search][timeend]' => '23:00',
			));

		$crawler = $this->client->submit($form);

		$crawler = $this->client->followRedirect();

		$this->assertEquals('Ws\EventsBundle\Controller\AlertController::indexAction',$this->client->getRequest()->attributes->get('_controller'));
	}

	/**
	 * Test the view of the first Alert
	 *
	 * Must have results > 0
	 */
	public function testView()
	{
		$alert = $this->em->getRepository('WsEventsBundle:Alert')->findOneByEmail('test@local.host');
		$id = $alert->getId();

		$crawler = $this->client->request('GET',$this->router->generate('ws_alerts_view',array('alert'=>$id)));					
		$this->assertTrue($crawler->filter('.list-table tr')->count() > 0);
	}

	/**
	 * Test if user can disable an Alert
	 *
	 */
	public function testDisable()
	{
		$alert = $this->em->getRepository('WsEventsBundle:Alert')->findOneByEmail('test@local.host');
		$id = $alert->getId();

		$crawler = $this->client->request('GET',$this->router->generate('ws_alerts_disable',array('alert'=>$id)));		
		$this->client->followRedirect();
		$this->assertEquals('Ws\EventsBundle\Controller\AlertController::indexAction',$this->client->getRequest()->attributes->get('_controller'));		

	}

	/**
	 * Test if user can enable an Alert
	 *
	 */
	public function testEnable()
	{

		$alert = $this->em->getRepository('WsEventsBundle:Alert')->findOneByEmail('test@local.host');
		$id = $alert->getId();

		$crawlerDisabled = $this->client->request('GET',$this->router->generate('ws_alerts_enable',array('alert'=>$id)));
		$this->client->followRedirect();
		$this->assertEquals('Ws\EventsBundle\Controller\AlertController::indexAction',$this->client->getRequest()->attributes->get('_controller'));		

	}

	/**
	 * Test if user can extend an Alert
	 *
	 */
	 public function testExtend()
	{
		$alert = $this->em->getRepository('WsEventsBundle:Alert')->findOneByEmail('test@local.host');
		$id = $alert->getId();

		$crawler = $this->client->request('GET',$this->router->generate('ws_alerts_extend',array('alert'=>$id,'nbmonth'=>6)));

		$this->client->followRedirect();
		$this->assertEquals('Ws\EventsBundle\Controller\AlertController::indexAction',$this->client->getRequest()->attributes->get('_controller'));		
	}

	/**
	 * Test if user can delete an Alert
	*
	 */
	public function testDelete()
	{
		$alert = $this->em->getRepository('WsEventsBundle:Alert')->findOneByEmail('test@local.host');
		$id = $alert->getId();

		$crawler = $this->client->request('DELETE',$this->router->generate('ws_alerts_delete',array('alert'=>$id)));

		$this->client->followRedirect();
		$this->assertEquals('Ws\EventsBundle\Controller\AlertController::indexAction',$this->client->getRequest()->attributes->get('_controller'));	

	}

	/**
	 * Test if admin can send the alerts
	 *
	 
	 public function testSendingDailyAlerts()
	{
		$client = static::createClient(array(),array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW' => 'pass',
			));

		//Try to send the daily alerts
		$crawler = $this->client->request('GET',$this->router->generate('ws_alerts_mailing',array('type'=>'daily')));
		$this->assertTrue($crawler->filter('body:contains("daily alertes")')->count() >= 1);
		

	}

	/**
	 * Test if admin can send the alerts
	 *
	
	public function testSendingWeeklyAlerts()	
	{
		$client = static::createClient(array(),array(
				'PHP_AUTH_USER' => 'admin',
				'PHP_AUTH_PW' => 'pass',
			));

		//Try to send the weekly alerts
		$crawler = $this->client->request('GET',$this->router->generate('ws_alerts_mailing',array('type'=>'weekly')));
		$this->assertTrue($crawler->filter('body:contains("weekly alertes")')->count() >= 1);

	}
	*/
	
}