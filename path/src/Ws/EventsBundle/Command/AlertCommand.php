<?php

namespace Ws\EventsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

use My\UserBundle\Entity\User;

class AlertCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('alerts:send')
            ->setDescription('Send alerts')
            ->addArgument('param', InputArgument::REQUIRED, 'Periodicity or username/id')
            ->addOption('user', 'u', InputOption::VALUE_NONE, 'If defined, only the alerts of the param user will be sended')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //get the input param
        $param = $input->getArgument('param');
        
        //the console command have not Request
        //we must defined it manually
        $container = $this->getContainer();
        $container->enterScope('request');
        $container->set('request', new Request(), 'request');
        
        //if we want to send to a unique user
        if(true == $input->getOption('user')) {

            $output->writeln('Start sending alerts for user: '.$param.'...');
            $res = $this->send2User($param);
        }
        //if we want to send periodicity alerts
        else {

            $output->writeln('Start sending '.$param.' alerts...');
            $res = $this->sendPeriodic($param);
        }

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

        //write the output results
        $this->formatResults($output,$res);

        //end line
        $output->writeln('End sending.');
        

    }

    private function formatResults(OutputInterface $output, $res) {

        $output->writeln(count($res['alerts']). ' alerts finded');

        $output->writeln(count($res['matched']). ' alerts matched');

        if( 0 === count($res['matched'])) return;

        $output->writeln(count($res['sended']).' alerts sended');
        foreach ($res['sended'] as $sended) {
            $output->writeln($sended['alert'].'=>'.$sended['nbevents'].' matched events');
        }

        if( 0 < count($res['expired'])) $output->writeln(count($res['expired']).' alerts expired...'); 
    }

    private function sendPeriodic($period) {

        $alerter = $this->getContainer()->get('ws_events.alerter');
        
        return $alerter->send($period);

    }

    private function send2User($user) {

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        
        if(is_numeric($user)) $user = $em->getRepository('MyUserBundle:User')->findOneById($user);
        else $user = $em->getRepository('MyUserBundle:User')->findOneByUsername($user);
       
        if( !$user instanceof User) throw new \Exception("User cant be found...");

        $alerts = $em->getRepository('WsEventsBundle:Alert')->findByUser($user);
        
        $alerter = $this->getContainer()->get('ws_events.alerter');
        return $alerter->sendAlerts($alerts);

    }

}