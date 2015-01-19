<?php

namespace My\WorldBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LocationRepositoryTest extends WebTestCase
{
	private $client;
	private $router;
	private $em;
	private $manager;

	/**
	 * PHPUnit setup
	 */
	public function setUp()
	{	
		$this->client = self::createClient(array(),array('PHP_AUTH_USER' => 'user1','PHP_AUTH_PW' => 'fatboy'));	
		$this->router = $this->client->getContainer()->get('router');
		$this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
		$this->repo = $this->em->getRepository('MyWorldBundle:Location');
	}
	/**
	 * PHPUnit close up
	 */
	protected function tearDown()
	{
		$this->em->close();
		unset($this->client, $this->em);
	}

	public function testFindStatesFromCodes()
	{
		$array = array(
			'CC1' => 'FR',
			'ADM1' => 'A1',
			'ADM2' => '21',
			'city' => -2041884
			);
		$states = $this->repo->findStatesFromCodes($array);
		$this->assertEquals('France',$states['country']->getName());
		$this->assertEquals('Bourgogne',$states['region']->getName());
		$this->assertEquals("Departement de la Cote-d' Or",$states['departement']->getName());
		$this->assertEquals('Dijon',$states['city']->getName());
	}

	public function testFindStatesByParentCode()
	{
		$states = $this->repo->findStatesByParentCode('FR','ADM2','A1');
		$this->assertEquals("Departement de la Cote-d' Or",$states[0]->getName());
		$this->assertEquals("Departement de la Nievre",$states[1]->getName());
		//...
	}

	public function testFindStatesByCode()
	{
		
	}
}