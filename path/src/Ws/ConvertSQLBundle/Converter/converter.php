<?php

namespace Ws\ConvertSQLBundle\Converter;

use Symfony\Component\Yaml\Parser;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class Converter
{
	private $db;
	private $em;
	private $container;	
	private $config;
	private $yaml;
	private $purger;

	public function __construct($db,EntityManager $em,Container $container)
	{
		$this->db = $db;
		$this->em = $em;
		$this->container = $container;
		$this->purger = new ORMPurger($em);
		$this->yaml = new Parser();
	}

	public function importYml($path)
	{
		

		$config = $this->yaml->parse(file_get_contents($path));

		foreach ($config['tables'] as $doctrinename => $dbname) {
			
			$config['params'][$doctrinename] = $this->yaml->parse(file_get_contents(__DIR__.'/../Resources/config/mapping/'.$doctrinename.'.yml'));
		}

		$this->config = $config;
	}

	public function purge()
	{
		$this->purger->purge();

		return $this;
	}

	public function convertAll()
	{
		$errors = array();
		$success = array();

		foreach ($this->config['tables'] as $doctrinename => $dbname)
		{
			$results = $this->convert($doctrinename,$dbname);

			$errors = array_merge($errors,$results['errors']);
			$success = array_merge($success,$results['success']);
		}

		return array('success'=>$success,'errors'=>$errors);		
	}

	public function convertOne($doctrinename)
	{
		$errors = array();
		$success = array();

		$dbname = $this->config['tables'][$doctrinename];

		$results = $this->convert($doctrinename,$dbname);

		$errors = array_merge($errors,$results['errors']);
		$success = array_merge($success,$results['success']);
		return array('success'=>$success,'errors'=>$errors);	
	}

	public function convert($doctrinename,$dbname)
	{
		$errors = array();
		$success = array();
			
		//get ancien results from the previous database
		$stmt = $this->db->prepare("SELECT * FROM ".$dbname);
		$stmt->execute();
		$old_entries = $stmt->fetchAll();


		//loop for each entry
		foreach ($old_entries as $key => $entry) {
			
			$class = $this->config['params'][$doctrinename]['class'];

			$new = new $class;

			$relations = $this->config['params'][$doctrinename]['relations'];
			foreach ($relations as $key => $value) {
					
				if(is_array($value)) $value = $this->formatValue($entry,$value);
				else $value = $entry[$value];

				$setter = $this->formatSetter($key);
				$new->$setter($value);
				
			}

			try
				{
					//presist new entity
					$this->em->persist($new);
					//set IdGeneratorType to null in order to keep id from previous database
					$metadata = $this->em->getClassMetaData(get_class($new));
					$metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
					//flush
					$this->em->flush();		

								
				}
				catch(\Doctrine\DBAL\DBALException $e)
				{
					$errorMsg = $e->getMessage();
					
					//because the EntityManager close when there is a Exception, we need to reopen it
					// reset the EM and all aias
					$this->container->set('doctrine.orm.entity_manager', null);
					$this->container->set('doctrine.orm.default_entity_manager', null);
					// get a fresh EM
					$this->em = $this->container->get('doctrine.orm.entity_manager');


					//If the error is about a forbidden duplicate content, dont save it and continue the loop
					if (strpos($errorMsg,'SQLSTATE[23000]') !== false) {
					    $errors[] = array(
					    	'type'=>$errorMsg,
					    	'class'=>$class,
					    	'entity'=>$new);
					    continue;
					}

					//throw error if no condition continues the loop
					throw($e);

				}

			//implement success
			$success[] = $class;
		}

		return array('success'=>$success,'errors'=>$errors);			
		
	}

	private function formatValue($entry,$array)
	{
		$value = $entry[$array[0]];
		$type = $array[1];
		$attr = $array[2];

		if($type == 'datetime'){

			$date = new \DateTime();
			$date->createFromFormat($attr,$value);
			return $date;
		}

		if($type == 'integer'){
			if(is_integer($attr)) return $attr;
			else return null;			
		}

		if($type == 'string'){
			if(is_string($attr)) return $attr;
			else return null;
		}

		return $value;
	}
	private function formatPropertyName($name)
	{
		$a = explode('_',$name);
		foreach ($a as $k => $v) {
			$a[$k] = ucfirst($v);
		}
		return implode('',$a);
	}

	private function formatSetter($name)
	{
		return 'set'.$this->formatPropertyName($name);
	}
}