<?php

namespace Ws\EventsBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Ws\EventsBundle\Manager\CalendarUrlGenerator;

class AlertControllerTest extends WebTestCase
{

	public $client;
	public $router;

	public function __construct()
	{
		$this->client = static::createClient(array(),array(
			'PHP_AUTH_USER' => 'user1',
			'PHP_AUTH_PW' => 'fatboy',
			));

		$this->router = $this->client->getContainer()->get('router');

	}
	
	/**
	 * Test the index of alerts of User1
	*/	 
	public function testIndex()
	{		
		$crawler = $this->client->request('GET',$this->router->generate('ws_alerts_index'));
		$this->assertEquals($crawler->filter('.row-alert')->count(),3);		
		$this->assertEquals($crawler->filter('.row-alert-active')->count(),2);
		$this->assertEquals($crawler->filter('.row-alert-pending')->count(),1);
	}

	/**
	 * Test the view of the first alert of User1
	 *
	 * Must have results > 0
	*/
	public function testView()
	{
		$crawler = $this->client->request('GET',$this->router->generate('ws_alerts_index'));	

		$firstAlert = $crawler->filter('.row-alert-active')->first();
		$url = $crawler->selectLink('Voir les activités')->link()->getUri();

		$crawlerView = $this->client->request('GET',$url);
		$this->assertTrue($crawlerView->filter('.list-table tr')->count() > 0);
	}

	/**
	 * Test if user can disable an Alert
	 */
	public function testDisable()
	{
		
		$crawler = $this->client->request('GET',$this->router->generate('ws_alerts_index'));
		$firstAlert = $crawler->filter('.row-alert-active')->first();
		$url = $crawler->selectLink('Désactiver')->link()->getUri();
		$crawlerDisable = $this->client->request('GET',$url);
		$this->assertTrue($crawlerDisable->filter('body:contains("Redirecting to /fr/event/alert/index")')->count() == 1);

	}

	/**
	 * Test if user can enable an Alert
	 */
	public function testEnable()
	{

		$crawler = $this->client->request('GET',$this->router->generate('ws_alerts_index'));
		$firstAlert = $crawler->filter('.row-alert-pending')->first();
		$url = $crawler->selectLink('Réactiver')->link()->getUri();
		$crawlerDisabled = $this->client->request('GET',$url);
		$this->assertTrue($crawlerDisabled->filter('body:contains("Redirecting to /fr/event/alert/index")')->count() == 1);

	}

	/**
	 * Test if user can delete an Alert
	*/
	 
	public function testDelete()
	{

		$crawler = $this->client->request('GET',$this->router->generate('ws_alerts_index'));
		$url = $crawler->filter('.row-alert-pending')->first()->filter('td.cell-action')->selectLink('Supprimer')->link()->getUri();		
		$crawlerDeleted = $this->client->request('GET',$url);
		$this->assertTrue($crawlerDeleted->filter('body:contains("Redirecting to /fr/event/alert/index")')->count() == 1);
		//check if the alert has been by verifying the count of remaining alerts equals to 2
		$this->assertTrue($this->client->request('GET','/fr/event/alert/index')->filter('.row-alert')->count() == 2);
	}

	/**
	 * Test if user can extend an Alert
	 */
	public function testExtend()
	{
		$crawler = $this->client->request('GET',$this->router->generate('ws_alerts_index'));
		$url = $crawler->filter('.row-alert')->first()->selectLink('Prolonger')->link()->getUri();
		$crawlerExtended = $this->client->request('GET',$url);
		$this->assertTrue($crawlerExtended->filter('body:contains("Redirecting to /fr/event/alert/index")')->count() == 1);		
	}

	
	public function testSendingAlerts()
	{
		$client = static::createClient(array(),array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW' => 'fatboy',
			));

		$crawler = $this->client->request('GET',$this->router->generate('ws_alerts_mailing',array('type'=>'daily')));

		$this->assertTrue($crawler->filter('body:contains("1 daily alertes")')->count() == 1);
		$this->assertTrue($crawler->filter('body:contains("guichardsim+user1@gmail.com --> 1 événements")')->count() == 1);


		$crawler = $this->client->request('GET',$this->router->generate('ws_alerts_mailing',array('type'=>'weekly')));

		$this->assertTrue($crawler->filter('body:contains("1 weekly alertes")')->count() == 1);
		$this->assertTrue($crawler->filter('body:contains("guichardsim+user1@gmail.com --> 1 événements")')->count() == 1);

	}
	

}