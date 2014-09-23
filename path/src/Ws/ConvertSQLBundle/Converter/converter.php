<?php

namespace Ws\ConvertSQLBundle\Converter;

use Symfony\Component\Yaml\Parser;

class Converter
{
	private $db;
	private $doctrine;
	private $config;
	private $yaml;

	public function __construct($db,$doctrine)
	{
		$this->db = $db;
		$this->doctrine = $doctrine;
		$this->yaml = new Parser();
	}

	public function importYml($path)
	{
		

		$config = $this->yaml->parse(file_get_contents($path));

		foreach ($config['tables'] as $dbname => $doctrinename) {
			
			$config['params'][$doctrinename] = $this->yaml->parse(file_get_contents(__DIR__.'/../Resources/config/mapping/'.$doctrinename.'.yml'));
		}

		$this->config = $config;
	}

	public function convert()
	{
		

		foreach ($this->config['tables'] as $doctrinename => $dbname) {
			
			//get all result from dbname
			$stmt = $this->db->prepare("SELECT * FROM ".$dbname);
			$stmt->execute();
			$old_entries = $stmt->fetchAll();

			//$em = $this->doctrine->getRepository($this->config['params'][$doctrinename]['repository']);

			foreach ($old_entries as $key => $entry) {
				
				$new = new $this->config['params'][$doctrinename]['class'];

				$relations = $this->config['params'][$doctrinename]['relations'];
				foreach ($relations as $key => $value) {
						
					if(is_array($value)) $value = $this->formatValue($entry,$value);
					else $value = $entry[$value];

					$setter = $this->formatSetter($key);
					$new->$setter($value);

					$this->doctrine->persist($new);
				}
			}

			$this->doctrine->flush();			

		}

						
		exit('saved');
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