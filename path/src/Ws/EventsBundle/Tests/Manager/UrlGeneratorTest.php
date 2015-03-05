<?php

namespace Ws\EventsBundle\Tests\Manager;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Ws\EventsBundle\Manager\CalendarManager;
use Ws\EventsBundle\Manager\CalendarUrlGenerator;
use Ws\EventsBundle\Tests\Manager\DataSearchProvider;
use Ws\EventsBundle\Entity\Search;

class UrlGeneratorTest extends KernelTestCase
{
	private $container;
	private $extractor;
	private $generator;
	private $em;
	private $now;

	public function setUp()
	{
		self::bootKernel();
		$this->container = static::$kernel->getContainer();
		$this->em = $this->container->get('doctrine')->getManager();
		$this->extractor = new CalendarManager($this->container);
		$this->generator = new CalendarUrlGenerator($this->container->get('router'));
		$this->searchProvider = new DataSearchProvider($this->em);
		$this->now = new \Datetime('now');
	}

	protected function tearDown()
	{
		parent::tearDown();
		//...
	}


	public function getTestData()
	{
		return $this->searchProvider->all();
	}

	public function testCalendarGeneratorUrl()
	{

		foreach ($this->getTestData() as $key =>$search_to_test) {			

			$url_to_test = $this->generator->setSearch($search_to_test)->getUrl();
			
			$search_generated = $this->extractor
									->resetParams()
									->resetSearch()
									->addParamsFromUrl($url_to_test)
									->prepareParams()
									->getSearch();
			
			//test Search values			
			if($search_to_test->hasDate()) $this->assertEquals($search_to_test->getDate(),$search_generated->getDate());
			if($search_to_test->hasLocation()) $this->assertEquals($search_to_test->getLocation(),$search_generated->getLocation());
			if($search_to_test->hasArea()) $this->assertEquals($search_to_test->getArea(),$search_generated->getArea());
			if($search_to_test->hasSports()) $this->assertEquals($search_to_test->getSports(),$search_generated->getSports());
			if($search_to_test->hasNbDays()) $this->assertEquals($search_to_test->getNbDays(),$search_generated->getNbDays());
			if($search_to_test->hasType()) $this->assertEquals($search_to_test->getType(),$search_generated->getType());
			if($search_to_test->hasTimeStart()) $this->assertEquals($search_to_test->getTimeStart()->format('d m Y H i'),$search_generated->getTimeStart()->format('d m Y H i'));
			if($search_to_test->hasTimeEnd()) $this->assertEquals($search_to_test->getTimeEnd()->format('d m Y H i'),$search_generated->getTimeEnd()->format('d m Y H i'));
			if($search_to_test->hasPrice()) $this->assertEquals($search_to_test->getPrice(),$search_generated->getPrice());
			if($search_to_test->hasLevel()) $this->assertEquals($search_to_test->getLevel(),$search_generated->getLevel());

			//test Search url generator
			$url = $this->generator->setSearch($search_generated)->getUrl();
			$this->assertEquals($url,$url_to_test);


		}		
	}


}