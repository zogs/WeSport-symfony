<?php

namespace Ws\EventsBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Ws\EventsBundle\Manager\CalendarUrlGenerator;

class CalendarControllerTest extends WebTestCase
{
	
	public function testIndex()
	{
		$client = static::createClient();

		//Test the default calendar view
		$crawler = $client->request('GET','/fr/event/calendar');
		$this->assertTrue($crawler->filter('.events')->count() == 12);
		$this->assertTrue($crawler->filter('.events-confirmed')->count() == 7);

		//Test the calendar on Dijon+100km for Natation and Football
		$crawler = $client->request('GET','/fr/event/calendar/now/Dijon+100/natation+football');
		$this->assertTrue($crawler->filter('.events-title:contains("Petit match entre amis")')->count() == 1);
		$this->assertTrue($crawler->filter('.events-title:contains("Cherche copain nageur")')->count() == 7);
		$this->assertTrue($crawler->filter('.colomn-1 .events')->count() == 2);

		//Test calendar for one day and cheaper than 5€
		$url = new CalendarUrlGenerator();
		$crawler = $client->request('GET','/fr/event/calendar/now/'.$url->defaults['city'].'/'.$url->defaults['sports'].'/'.$url->defaults['type'].'/1/'.$url->defaults['time'].'/5');
		$this->assertTrue($crawler->filter('.events')->count() == 2);
	}

}