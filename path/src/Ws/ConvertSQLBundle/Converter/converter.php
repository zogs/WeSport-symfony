<?php

namespace Ws\ConvertSQLBundle\Converter;

use Symfony\Component\Yaml\Parser;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

class Converter
{
	private $db;
	private $em;
	private $container;	
	private $config;
	private $yaml;

	public function __construct($db,EntityManager $em,Container $container)
	{
		$this->db = $db;
		$this->em = $em;
		$this->container = $container;
		$this->yaml = new Parser();
	}

	public function importYml($path)
	{
		

		$config = $this->yaml->parse(file_get_contents($path));

		foreach ($config['tables'] as $entityName => $fieldName) {
			
			$config['entities'][$entityName] = $this->yaml->parse(file_get_contents(__DIR__.'/../Resources/config/mapping/'.$entityName.'.yml'));
		}

		$this->config = $config;
	}

	public function convertAll()
	{
		$errors = array();
		$success = array();

		foreach ($this->config['tables'] as $entityName => $fieldName)
		{
			$results = $this->convert($entityName,$fieldName);

			$errors = array_merge($errors,$results['errors']);
			$success = array_merge($success,$results['success']);
		}

		return array('success'=>$success,'errors'=>$errors);		
	}

	public function convertOne($entityName)
	{
		$errors = array();
		$success = array();

		$fieldName = $this->config['tables'][$entityName];

		$results = $this->convert($entityName,$fieldName);

		$errors = array_merge($errors,$results['errors']);
		$success = array_merge($success,$results['success']);
		return array('success'=>$success,'errors'=>$errors);	
	}


	public function convert($entityName,$fieldName)
	{
		$errors = array();
		$success = array();
			
		//get ancien results from the previous database
		$stmt = $this->db->prepare("SELECT * FROM ".$fieldName);
		$stmt->execute();
		$old_entries = $stmt->fetchAll();


		$config = $this->config['entities'][$entityName];

		//loop for each entry
		foreach ($old_entries as $k => $entry) {
			

			try
			{
				//map the new entity with the data of the old entry
				$entity = $this->mapEntity($config,$entry);	

				dump($entry);
				dump($entity);
				
				exit();

				//presist new entity
				$this->em->persist($entity);

				//set IdGeneratorType to null in order to keep id from previous database
				$metadata = $this->em->getClassMetaData(get_class($entity));
				$metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
				//flush
				$this->em->flush();	
				//stock success
				$success[] = get_class($entity);
				//performance tricks
				$entity = null;				
				if($k % 10 === 0) $this->em->clear();//clear memory every 100 entity
							
			}
			catch(\Exception $e)
			{
				$errorMsg = $e->getMessage();
				
				//because the EntityManager close when there is a Exception, we need to reopen it
				// reset the EM and all aias
				$this->container->set('doctrine.orm.entity_manager', null);
				$this->container->set('doctrine.orm.default_entity_manager', null);
				// get a fresh EM
				$this->em = $this->container->get('doctrine.orm.entity_manager');


				//If the error is about a forbidden duplicate content, stock the msg and continue the loop
				if (strpos($errorMsg,'SQLSTATE[23000]') !== false) {

					$errorMsg = 'Duplicate entity for "'.get_class($entity).'" with ID='.$entity->getId();

				}
				
				//stock the error msg
				$errors[] = $errorMsg;
				
			}

		}

		return array('success'=>$success,'errors'=>$errors);			
		
	}

	private function mapEntity($config,$entry)
	{
		$class = $config['class'];
		$relations =  $config['relations'];

		//create the new entity
		$entity = new $class;

		foreach ($relations as $property => $field) {
			
			//the field is directly mapped by a old field
			if( ! is_array($field)){
				$value = $entry[$field];	
			}
			//or by a special operation
			else {

				//the type of mapping is missing
				if(empty($field['type'])) {

					throw new \Exception('The type need to be define for the '.ucfirst($property).' property of '.$class.' in '.get_class($class).'.yml');
				}
				//field is mapped by a caller
				elseif($field['type'] == 'call'){

					$class = $field['class'];
					$caller = new $class($this->container,$entry,$entity);
					$method = $field['method'];
					$parameters = (isset($field['parameters']))? $field['parameters'] : array();

					//call the method 
					$value = call_user_func_array(array($caller,$method), $parameters);

					if('skip'===$value) {

						throw new \Exception('A record have been avoided. Try to convert an instance of "'.get_class($entity).'" with ID='.$entity->getId());						
					}
				}
				//field is mapped by a entity
				elseif($field['type'] == 'entity') {

					$conf = $relations[$property];
					$value = $this->mapEntity($conf,$entry);
				}
				//field is mapped by a commun type
				else {
					$value = $this->mapField($entry,$field);
				}
			}		

			//set the field to the entity
			$setter = $this->formatSetter($property);
			$entity->$setter($value);
		}

		return $entity;
	}


	private function mapField($entry,$config)
	{
		$type = $config['type'];

		if($type == 'datetime' || $type == 'date'){

			$date = new \DateTime();
			$date->createFromFormat($config['format'],$entry[$config['field']]);
			return $date;
		}

		if($type == 'integer'){
			if(is_integer($config['value'])) return $config['value'];
			else return null;			
		}

		if($type == 'string'){
			if(is_string($config['value'])) return $config['value'];
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