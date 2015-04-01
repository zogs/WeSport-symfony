<?php

namespace Ws\ConvertSQLBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PurgeCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('database:purge')
            ->setDescription('Purge une base de donnée')
            ->addArgument('database', InputArgument::OPTIONAL, 'Which database ?')
            //->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $database = 'default';

        if($input->hasArgument('database')){
            $database = $input->getArgument('database');            
        }
        
        $manager = $this->getContainer()->get('doctrine')->getManager($database);

        $purger = $this->getContainer()->get('ws_table_purger');
        $purger->setManager($manager);

        $purger->purge();

        $output->writeln('The database '.$purger->getDatabase().' has been purge !');

    }

}