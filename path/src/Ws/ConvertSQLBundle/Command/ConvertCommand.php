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
            ->addArgument('table', InputArgument::REQUIRED, 'Which table ?')
            //->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = $input->getArgument('table');
        
        if($table){
        	$this->executeTable($output,$table);
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
        
    protected function executeTable(OutputInterface $output, $table)
    {
    	if($table){

            $progress = $this->getHelperSet()->get('progress');


	        $converter = $this->getContainer()->get('ws_table_converter');
			$converter->importYml(__DIR__.'/../Resources/config/tables.yml');   
            $converter->setOutput($output);

			$results = $converter->convertOne($table);  

            if(!empty($results['errors'])) $this->errors = array_merge($this->errors,$results['errors']);	
			if(!empty($results['success'])) $this->success = array_merge($this->success,$results['success']);
        }
        
    }
}