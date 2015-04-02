<?php

namespace Ws\ConvertSQLBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ResetIdCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('database:reset:id')
            ->setDescription('Réinitialise l\'auto increment')
            ->addArgument('table', InputArgument::REQUIRED, 'Which table ?')
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

        $table = $input->getArgument('table');

        $em = $this->getContainer()->get('doctrine')->getManager($database);
        $connection = $em->getConnection();
        $connection->exec('ALTER TABLE ' . $table . ' AUTO_INCREMENT = 1;');

        $output->writeln('The AUTO_INCREMENT '.$table.' has been resetted !');

    }

}