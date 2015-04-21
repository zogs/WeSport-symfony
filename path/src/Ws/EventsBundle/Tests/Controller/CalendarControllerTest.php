<?php

namespace Ws\EventsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Ws\EventsBundle\Manager\CalendarUrlGenerator;

class CalendarControllerTest extends WebTestCase
{	
	public $client;
	public $router;

	public function __construct()
	{
		$this->client = static::createClient(array(),array(
			'PHP_AUTH_USER' => 'user1',
			'PHP_AUTH_PW' => 'pass',
			));

		$this->router = $this->client->getContainer()->get('router');
	}

	public function testIndex()
	{

		//Test the calendar on Dijon+100km for Natation and Football
		$crawler = $this->client->request('GET',$this->router->generate('ws_calendar',array('date'=>'now','city'=>'Dijon+100','sports'=>'natation+football')));
		$this->assertEquals(1,$crawler->filter('.events-title:contains("Petit match entre amis")')->count());
		$this->assertEquals(7,$crawler->filter('.events-title:contains("Cherche copain nageur")')->count());
		$this->assertEquals(2,$crawler->filter('.colomn-1 .events')->count());

		//Test calendar for one day and cheaper than 5€
		$url = new CalendarUrlGenerator();
		$crawler = $this->client->request('GET',$this->router->generate('ws_calendar',array('date'=>'now','city'=>$url->getDefault('city'),'sports'=>$url->getDefault('sports'),'type'=>$url->getDefault('type'),'nbdays'=>1,'time'=>$url->getDefault('time'),'price'=>5)));
		$this->assertTrue($crawler->filter('.events')->count() == 2);
		
	}

	public function testAjax()
	{
		$crawler = $this->client->request('GET',$this->router->generate('ws_calendar_ajax',array('date'=>'now')));
		$this->assertTrue($crawler->filter('.events')->count() > 0);
	}

}