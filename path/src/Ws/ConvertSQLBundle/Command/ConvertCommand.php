<?php

namespace Ws\ConvertSQLBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertCommand extends ContainerAwareCommand
{

	private $errors = array();
	private $success = array();

    protected function configure()
    {
        $this
            ->setName('database:convert')
            ->setDescription('Converti une table SQL en table Doctrine')
            ->addArgument('table', InputArgument::OPTIONAL, 'Which table ?')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Convert all tables')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        if(true == $input->getOption('all')){

            $this->executeAllTables($output);

        }
        else {
            $table = $input->getArgument('table');
            if($table){
            	$this->executeOneTable($output,$table);
            }            
        }

        $this->displayErrors($output);
        $this->displaySuccess($output);

    }

    protected function displayErrors(OutputInterface $output)
    {
    	if(!empty($this->errors)){
        	foreach ($this->errors as $key => $error) {
        		$str = 'Error '.$key.': '.$error;        		
        		$output->writeln($str);
        	}
        	$output->writeln('');
        	$output->writeln(count($this->errors).' errors...');
        } 	
    }

    protected function displaySuccess(OutputInterface $output)
    {
		if(!empty($this->success)) {
            $output->writeln('');
			$output->writeln('Bravo, '.count($this->success).' entities created !');
		}    	
    }
        
    protected function executeOneTable(OutputInterface $output, $table)
    {
    	if($table){

	        $converter = $this->getContainer()->get('ws_table_converter');
			$converter->importConfig();   
            $converter->setOutput($output);

			$results = $converter->convertOne($table);  

            if(!empty($results['errors'])) $this->errors = array_merge($this->errors,$results['errors']);	
			if(!empty($results['success'])) $this->success = array_merge($this->success,$results['success']);
        }
        
    }

    protected function executeAllTables(OutputInterface $output)
    {
        $converter = $this->getContainer()->get('ws_table_converter');
        $converter->importConfig();   
        $converter->setOutput($output);

        $results = $converter->convertAll();  

        if(!empty($results['errors'])) $this->errors = array_merge($this->errors,$results['errors']);   
        if(!empty($results['success'])) $this->success = array_merge($this->success,$results['success']);
    }
}