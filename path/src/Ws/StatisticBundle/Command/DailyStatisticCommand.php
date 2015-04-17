<?php

namespace Ws\StatisticBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

use My\UserBundle\Entity\User;

class DailyStatisticCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('daily:stat')
            ->setDescription('Send daily stat to admin')
            //->addArgument('param', InputArgument::REQUIRED, 'Periodicity or username/id')
            ->addOption('email', null, InputOption::VALUE_NONE, 'If defined, send email to admins')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {        
        //the console command have not Request
        //we must defined it manually
        $container = $this->getContainer();
        $container->enterScope('request');
        $container->set('request', new Request(), 'request');
                
        //get statistic
        $output->writeln('Getting statistic...');
        $res = $this->getContainer()->get('daily_statistic.manager')->getDailyStats();
        
        //write the output results
        $this->formatResults($output,$res);

        // (send only if "email" option is passed)
        if(true == $input->getOption('email')) {
            $output->writeln('Start sending emails...');

            //send emails
            $emails = $this->getContainer()->get('daily_statistic.manager')->sendEmailAdmins();

            //the transport mailer is not aware of the default parameters
            //we must configure it manually
            $transport = $container->get('swiftmailer.transport.real');
            $transport->setHost($container->getParameter('mailer_host'));
            $transport->setPort($container->getParameter('mailer_port'));
            $transport->setEncryption($container->getParameter('mailer_encryption'));
            $transport->setUsername($container->getParameter('mailer_user'));
            $transport->setPassword($container->getParameter('mailer_password'));

            //the mailer sent the mail at the end of a Response
            //as there is no Response in command line
            //we must flush manually the mailer spool
            $mailer = $container->get('mailer');
            $spool = $mailer->getTransport()->getSpool();
            $spool->flushQueue($transport);

            //write output
            foreach ($emails as $email) {
                $output->writeln('Sended to '.$email);
            }

            $output->writeln('End sending emails...');
        }


        //end line
        $output->writeln('End.');
        

    }

    private function formatResults(OutputInterface $output, $res) {

        $output->writeln($res['registration'].' registration');
        $output->writeln($res['events_planned'].' events_planned');
        $output->writeln($res['events_confirmed'].' events_confirmed');
        $output->writeln($res['events_deposed'].' events_deposed');

       }

}