<?php

namespace My\WorldBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StateRepositoryTest extends WebTestCase
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
		$this->repo = $this->em->getRepository('MyWorldBundle:State');
	}
	/**
	 * PHPUnit close up
	 */
	protected function tearDown()
	{
		$this->em->close();
		unset($this->client, $this->em);
	}

	public function testFindStateByName()
	{
	    $bourgogne = $this->repo->findStateByName('Bourgogne','FR');
	    $this->assertEquals('Bourgogne',$bourgogne->getName());

	}

	public function testFindStateByCode()
	{
		$bourgogne = $this->repo->findStateByCode('FR','A1','ADM1');
		$this->assertEquals('Bourgogne',$bourgogne->getName());

		$cotedor = $this->repo->findStateByCode('FR','21','ADM2');
		$this->assertEquals("Departement de la Cote-d' Or",$cotedor->getName());
	}

	public function testFindStateByCodes()
	{
		$bourgogne = $this->repo->findStateByCodes('FR','A1');
		$this->assertEquals('Bourgogne',$bourgogne->getName());

		$cotedor = $this->repo->findStateByCodes('FR','A1','21');
		$this->assertEquals("Departement de la Cote-d' Or",$cotedor->getName());
	}

	public function testFindStatesByParent()
	{
		$states = $this->repo->findStatesByParent('ADM2','FR','A1');
		$this->assertEquals("Departement de la Cote-d' Or",$states[0]->getName());
		$this->assertEquals("Departement de la Nievre",$states[1]->getName());
		$this->assertEquals("Departement de Saone-et-Loire",$states[2]->getName());
		$this->assertEquals("Departement de l'Yonne",$states[3]->getName());
		
	}
}