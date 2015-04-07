<?php

namespace Ws\ConvertSQLBundle\Converter;

use Symfony\Component\Yaml\Parser;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\NullOutput;

class Converter
{
	private $db;
	private $em;
	private $container;	
	private $config;
	private $yaml;
	private $output;
	private $callback = null;

	public function __construct($db,EntityManager $em,Container $container)
	{
		$this->db = $db;
		$this->em = $em;
		$this->container = $container;
		$this->yaml = new Parser();
		$this->output = new NullOutput();
	}

	/**
	 * set output interface for command line tool
	 *
	 * @param OutputInfergace $output
	 */
	public function setOutput(OutputInterface $output)
	{
		$this->output = $output;
	}

	/**
	 * import the main config file
	 *
	 * @param string $path the relative path to the yml conf file
	 * @return class $this
	 */
	public function importConfig($path = null)
	{		
		$path = (isset($path))? $path : __DIR__.'/../Resources/config/tables.yml';

		$mainConfig = $this->yaml->parse(file_get_contents($path));

		foreach ($mainConfig['tables'] as $entityName => $tableName) {
			
			$mainConfig['entities'][$entityName] = $this->yaml->parse(file_get_contents(__DIR__.'/../Resources/config/mapping/'.$entityName.'.yml'));
		}

		$this->mainConfig = $mainConfig;

		return $this;
	}

	/**
	 * start conversion for all the tables defined in the conf file
	 *
	 * @return array of success and errors
	 */
	public function convertAll()
	{
		$errors = array();
		$success = array();

		$this->output->writeln('Start processing all tables...');
		foreach ($this->mainConfig['tables'] as $entityName => $tableName)
		{
			$this->output->writeln('Start processing '.ucfirst($entityName).' entities...');
			$results = $this->convert($entityName,$tableName);
			$this->output->writeln('');
			$this->output->writeln('End processing '.ucfirst($tableName).' entities !');

			$errors = array_merge($errors,$results['errors']);
			$success = array_merge($success,$results['success']);
		}
		$this->output->writeln('End processing all tables !');
		$this->output->writeln('');

		return array('success'=>$success,'errors'=>$errors);		
	}

	/**
	 * start conversion for a entity name
	 *
	 * @param string $entityName 
	 * @return array of success and errors
	 */
	public function convertOne($entityName)
	{
		$errors = array();
		$success = array();

		$tableName = $this->mainConfig['tables'][$entityName];

		$this->output->writeln('Start processing '.ucfirst($entityName).' entities...');
		$results = $this->convert($entityName,$tableName);
		$this->output->writeln('');
		$this->output->writeln('End processing '.ucfirst($tableName).' entities !');
		$this->output->writeln('');

		$errors = array_merge($errors,$results['errors']);
		$success = array_merge($success,$results['success']);
		return array('success'=>$success,'errors'=>$errors);	
	}


	/**
	 * do the conversion between standard sql data and doctrine entities
	 *
	 * @param string $entityName
	 * @param string $tableName
	 */
	private function convert($entityName,$tableName)
	{
		$errors = array();
		$success = array();
			
		//get entries of the previous database table
		$stmt = $this->db->prepare("SELECT * FROM ".$tableName);
		$stmt->execute();
		$old_entries = $stmt->fetchAll();
		$nb_entries = count($old_entries);

		//get config of the fields to convert
		$config = $this->mainConfig['entities'][$entityName];

		//set progress bar for command line
		$progressBar = new ProgressBar($this->output,$nb_entries);
		$progressBar->setFormat(' %current%/%max% [%bar%] %percent%% %elapsed% Eta:%remaining% Mem:%memory%');		
		$f = 100;
		if($nb_entries >= 50000) $f = floor($nb_entries/1000);
		if($nb_entries <= 50000) $f = floor($nb_entries/100);
		if($nb_entries <= 1000) $f = floor($nb_entries/10);
		if($nb_entries <= 100) $f = 1;
		$this->output->writeln($nb_entries.' entries : refreshing every '.$f.'- ');
		$progressBar->setRedrawFrequency($f);
		$progressBar->start();

		//set off SQLlogger for performance
		$this->em->getConnection()->getConfiguration()->setSQLLogger(null);

		
		//loop for each entry
		foreach ($old_entries as $k => $entry) {
			
			try
			{
				//map the new entity with the data of the old entry
				$entity = $this->mapEntity($config,$entry);	
				//set IdGeneratorType to null in order to keep id from previous database
				$metadata = $this->em->getClassMetaData(get_class($entity));
				$metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

				//presist new entity				
				$this->em->persist($entity);
				//flush
				$this->em->flush();	
				//trigger callback
				if($this->callback) $this->triggerCallback($entity,$entry);
				//advance progressBar
				$progressBar->advance();
				//stock success
				$success[] = get_class($entity);
				//clear memory once in a time			
				if($k % 20 === 0) {
					$this->em->clear();//clear memory every 10 entity
				}				
							
			}
			catch(\Exception $e)
			{
				

				$errorMsg = $e->getMessage();				
				$errorMsg .= ' - file: '.$e->getFile();
				$errorMsg .= ' - line: '.$e->getLine();
				
				//$this->output->writeln($errorMsg);

				//because the EntityManager close when there is a Exception, we need to reopen it
				// reset the EM and all aias
				$this->container->set('doctrine.orm.entity_manager', null);
				$this->container->set('doctrine.orm.default_entity_manager', null);
				// get a fresh EM
				$this->em = $this->container->get('doctrine.orm.entity_manager');


				//If the error is about a SQL error
				if (strpos($errorMsg,'SQLSTATE[23000]') !== false) {
					$errorMsg = 'SQLERROR: '.$errorMsg;
				}

				//advance progressBar
				$progressBar->advance();

				//stock the error msg
				$errors[] = $errorMsg;	
		
			}
		}

		$progressBar->finish();

		return array('success'=>$success,'errors'=>$errors);			
		
	}

	private function triggerCallback($entity,$entry)
	{		
		$class = $this->callback['class'];
		$method = $this->callback['method'];
		$parameters = $this->callback['parameters'];
		
		$caller = new $class($this->container,$entry,$entity);
		//call the method 
		call_user_func_array(array($caller,$method), $parameters);

		return $this->callback = null;
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

					throw new \Exception('The type need to be define for the "'.ucfirst($property).'" property of '.$class);
				}
				//field is mapped by a caller
				elseif($field['type'] == 'call'){

					$class = $field['class'];
					$caller = new $class($this->container,$entry,$entity);
					$method = $field['method'];
					$parameters = (isset($field['parameters']))? $field['parameters'] : array();

					//call the method 
					$value = call_user_func_array(array($caller,$method), $parameters);

					if('_skip_'===$value) {

						throw new \Exception('A record have been avoided : "'.get_class($entity).'" with ID='.$entity->getId());						
					}
				}
				//stock callback
				elseif ($field['type'] == 'callback') {
					$this->callback = array(
						'class' => $field['class'],
						'method' => $field['method'],
						'parameters' => (isset($field['parameters']))? $field['parameters'] : array()
						);
					$value = '_noset_';
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

			//dont set the field if we decided to
			if($value==='_noset_') continue;

			//set the field fo the entity
			$setter = $this->formatSetter($property);
			$entity->$setter($value);
		}

		return $entity;
	}


	private function mapField($entry,$config)
	{
		$type = $config['type'];

		if($type === 'datetime' || $type === 'date'){
			
			$format = $config['format'] ;
			$date = $entry[$config['field']] ;
			
			return \DateTime::createFromFormat($format,$date);
		}

		if($type === 'integer'){
			if(is_integer($config['value'])) return $config['value'];
			throw new \Exception('a "value" parameter is needed for "integer" type and must be an integer');		
		}

		if($type === 'string'){
			if(is_string($config['value'])) return $config['value'];
			throw new \Exception('a "value" parameter is needed for "string" type');
			
		}

		if($type === 'boolean'){
			if(is_string($config['value']) && $config['value'] === 'true') return true;
			if(is_string($config['value']) && $config['value'] === 'false') return false;
			throw new \Exception('a "value" parameter" is needed for "boolean" type, and must be "true" or "false"');
		}

		if($type === 'value' && isset($config['value'])){
			return $config['value'];
		}

		return null;
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