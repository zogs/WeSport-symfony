<?php

namespace My\WorldBundle\Export;

use Symfony\Component\HttpFoundation\Request;

class SQLExporter 
{
	private $file_path;
	private $file_name;
	private $file_ext = 'sql';
	private $mysql_bin;
	private $request;
	private $dbname;
	private $dbuser;
	private $dbpassword;

	public function __construct(Request $request,$dbname,$dbuser,$dbpassword)
	{
		$this->request = $request;
		$this->dbname = $dbname;
		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;
	}

	public function setConfig($config)
	{
		$this->file_path =  $config['file_path'];
		$this->file_name = $config['file_name'];
		$this->mysql_bin = $config['mysql_bin_path'];
	}
	public function exportCountries($countries)
	{
		$cc = '';
	            foreach ($countries as $country) {
	                $cc .= "'".$country->getCode()."',";
	            }
	            $cc = rtrim($cc,",");

	            //prepare password option
	            $password = ($this->dbpassword !== null)? ' -p'.$this->dbpassword.' ' : '';

	            //prepare options
	            $options = ' --skip-add-drop-table ';

	            //dump countries
	            $command = $this->mysql_bin.DIRECTORY_SEPARATOR.'mysqldump -u'.$this->dbuser.' '.$password.' --databases '.$this->dbname.' '.$options.' --tables world_country --where="CC1 IN ('.$cc.')" > '.$this->getFilePath().' ';
	            exec($command);

	            //dump states
	            $command = $this->mysql_bin.DIRECTORY_SEPARATOR.'mysqldump -u'.$this->dbuser.' '.$password.' --databases '.$this->dbname.' '.$options.' --tables world_states --where="CC1 IN ('.$cc.')"  >>  '.$this->getFilePath().' ';             
	            exec($command);

	            //dump cities
	            $command = $this->mysql_bin.DIRECTORY_SEPARATOR.'mysqldump -u'.$this->dbuser.' '.$password.' --databases '.$this->dbname.' '.$options.' --tables world_cities --where="CC1 IN ('.$cc.')"  >>  '.$this->getFilePath().' ';              
	            exec($command);

	            return $this->getFileUrl();
	}

	private function getFilePath()
	{
		return  $this->file_path.DIRECTORY_SEPARATOR.$this->file_name.'.'.$this->file_ext;
	}

	private function getFileUrl()
	{
		return $this->request->getScheme() . '://' . $this->request->getHttpHost() . $this->request->getBasePath().'/'.$this->file_name.'.'.$this->file_ext;
	}
}