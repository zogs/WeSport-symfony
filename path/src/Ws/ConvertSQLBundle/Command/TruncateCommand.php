<?php

namespace Ws\ConvertSQLBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TruncateCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('database:truncate')
            ->setDescription('Purge une table')
            ->addArgument('table', InputArgument::REQUIRED, 'Which table ?')
            ->addArgument('database', InputArgument::OPTIONAL, 'Which database ?')
            ->addOption('no-reset', null, InputOption::VALUE_NONE, 'If defined, auto_increment will not be reinitializate')
            ->addOption('entity', null, InputOption::VALUE_NONE, 'If defined, table name will be found by its entity name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $database = 'default';        

        //ask for confirmation
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog->askConfirmation($output, '<question>Do you really want to lost all these data ? (no)</question>', false)) {
            return;
        }

        if ($input->hasArgument('database')) {
            $database = $input->getArgument('database');            
        }

        $table = $input->getArgument('table');

        $em = $this->getContainer()->get('doctrine')->getManager($database);
        $connection = $em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();

        if (true == $input->getOption('entity')) {
            $cmd = $em->getClassMetadata($table);
            $table = $cmd->getTableName();
        }
        
        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $q = $dbPlatform->getTruncateTableSql($table);
        $connection->executeUpdate($q);
        $connection->query('SET FOREIGN_KEY_CHECKS=1');

        $output->write('The table '.$table.' has been truncated !');

        if (false == $input->getOption('no-reset')) {
            $connection->exec('ALTER TABLE ' . $table . ' AUTO_INCREMENT = 1;');            
            $output->write(' AUTO_INCREMENT have been resetted !');            
        }
        


    }

}