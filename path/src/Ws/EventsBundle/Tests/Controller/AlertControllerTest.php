<?php

namespace Ws\EventsBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Ws\EventsBundle\Manager\CalendarUrlGenerator;

class AlertControllerTest extends WebTestCase
{

	public $client = null;

	public function __construct()
	{
		$this->client = static::createClient(array(),array(
			'PHP_AUTH_USER' => 'user1',
			'PHP_AUTH_PW' => 'fatboy',
			));
	}
	
	/**
	 * Test the index of alerts of User1
	*/	 
	public function testIndex()
	{		
		$crawler = $this->client->request('GET','/fr/event/alert/index');		
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
		$crawler = $this->client->request('GET','/fr/event/alert/index');	

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
		//Test is disabling works
		$crawler = $this->client->request('GET','/fr/event/alert/index');
		$firstAlert = $crawler->filter('.row-alert-active')->first();
		$url = $crawler->selectLink('Désactiver')->link()->getUri();
		$crawlerDisable = $this->client->request('GET',$url);
		$this->assertTrue($crawlerDisable->filter('body:contains("Redirecting to /fr/event/alert/index")')->count() == 1);

	}

}