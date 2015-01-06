<?php

namespace Ws\EventsBundle\Tests\Manager;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Ws\EventsBundle\Manager\CalendarManager;
use Ws\EventsBundle\Manager\CalendarUrlGenerator;
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
		$this->now = new \Datetime('now');
	}

	protected function tearDown()
	{
		parent::tearDown();
		//...
	}


	public function getTestData()
	{
		$search1 = new Search();
		$search1->setNbDays(1);
		$search1->setPrice(5);
		$search1->setType(array('asso','pro'));	
		$search1->setTimeStart($this->now);	
		$search1->setDayOfWeek(array('monday','wednesday'));
		$search1->setLevel(array('beginner','average'));

		$search2 = new Search();
		$search2->setCountry($this->em->getRepository('MyWorldBundle:Country')->findCountryByName('France'));
		$search2->setLocation($this->em->getRepository('MyWorldBundle:Location')->findLocationByCityName('Dijon'));
		$search2->setSports($this->em->getRepository('WsSportsBundle:Sport')->findAll());
		$search2->setArea(100);
		$search2->setType(array('person','pro'));
		$search2->setNbDays(4);
		$search2->setTimeEnd($this->now);

		$search3 = new Search();
		$search3->setCountry($this->em->getRepository('MyWorldBundle:Country')->findCountryByName('France'));
		$search3->setLocation($this->em->getRepository('MyWorldBundle:Location')->findLocationByCityName('Beaune'));
		$search3->setSports($this->em->getRepository('WsSportsBundle:Sport')->findAll());
		$search3->setArea(100);
		$search3->setType(array('person','pro','asso'));
		$search3->setNbDays(14);
		$search3->setTimeEnd($this->now);
		$search3->setPrice(50);
		$search3->setTimeStart($this->now);	
		$search3->setDayOfWeek(array('monday','wednesday','friday','sunday'));
		$search3->setLevel(array('beginner','average','expert'));
		$search3->setOrganizer($this->em->getRepository('MyUserBundle:User')->findOneByUsername('admin'));

		return array(
			$search1,
			$search2,
			$search3,
			);
	}

	public function testCalendarGeneratorUrl()
	{

		foreach ($this->getTestData() as $search_to_test) {			

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