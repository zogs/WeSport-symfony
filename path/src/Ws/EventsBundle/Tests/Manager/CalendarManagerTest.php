<?php

namespace Ws\EventsBundle\Tests\Manager;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Ws\EventsBundle\Manager\CalendarManager;
use Ws\EventsBundle\Manager\CalendarUrlGenerator;
use Ws\EventsBundle\Entity\Search;

class UrlGeneratorTest extends KernelTestCase
{
	private $container;
	private $manager;
	private $generator;
	private $em;
	private $now;

	public function setUp()
	{
		self::bootKernel();
		$this->container = static::$kernel->getContainer();
		$this->em = $this->container->get('doctrine')->getManager();
		$this->manager = new CalendarManager($this->container);
		$this->generator = new CalendarUrlGenerator($this->container->get('router'));
		$this->searchProvider = new DataSearchProvider($this->em);
		$this->now = new \Datetime('now');
	}

	protected function tearDown()
	{
		parent::tearDown();
		//...
	}

	

}