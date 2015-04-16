<?php

namespace Ws\EventsBundle\Service;

use Doctrine\ORM\EntityManager;
use Ws\EventsBundle\Manager\AlertManager;
use Ws\MailerBundle\Mailer\Mailer;

class Alerter {
	
	protected $manager;
	protected $mailer;
	protected $em;
	protected $results;

	public function __construct(AlertManager $manager, Mailer $mailer, EntityManager $em)
	{
		$this->manager = $manager;
		$this->mailer = $mailer;
		$this->em = $em;
		$this->results = array();
		$this->results['alerts'] = array();
		$this->results['sended'] = array();
		$this->results['matched'] = array();
		$this->results['expired'] = array();
	}

	public function send($type = 'daily')
	{
		if($type == 'daily') return $this->sendDaily();
		if($type == 'weekly') return $this->sendWeekly();

		throw new \Exception('Type of alert is not defined');
	}

	private function sendDaily()
	{
		$alerts = $this->findAlerts('daily');

		return $this->sendAlerts($alerts);
	}

	private function sendWeekly()
	{
		$alerts = $this->findAlerts('weekly');

		return $this->sendAlerts($alerts);
	}

	private function findAlerts($type)
	{
		if($type == 'daily') return $alerts = $this->em->getRepository('WsEventsBundle:Alert')->findDailyAlerts();
		if($type == 'weekly') return $alerts = $this->em->getRepository('WsEventsBundle:Alert')->findWeeklyAlerts();

		throw new \Exception('Type of alert is not defined');
	}

	
	public function sendAlerts($alerts)
	{
		//Increment alerts
		$this->results['alerts'] = $this->setResultAlerts($alerts);

		//Constants
		$repo = $this->em->getRepository('WsEventsBundle:Event');
		$now = new \DateTime('now');

		//for each alert
		foreach ($alerts as $k => $alert) {
			
			//find all events that match the alert
			$events = $repo->findEvents($alert->getSearch());

			//Skip if no events match the alert
			if(empty($events)) continue;

			//Increment matched alert
			$this->results['matched'][] = $alert->getId();

			//Mail the alert
			$this->mailer->sendAlertMessage($alert,$events);

			//Save which events have been alerted
			$this->manager->saveAlerted($alert,$events);

			//Set results
			$this->results['sended'][] = array('alert'=>$alert->getId().':'.$alert->getEmail(),'nbevents'=>count($events));

			//Disactive outdated alerts
			if( $alert->getDateStop()->format('U') >= $now->format('U')) {
				$this->manager->disableAlert($alert);
				//Inform the user that his alert is outdated
				$this->mailer->sendExpiredAlertMessage($alert);
				//Increment results
				$this->results['expired'][] = $alert->getId();
			}
		}
		
		$this->manager->flush();
		$this->manager->clear();

		return $this->results;
	}

	private function setResultAlerts($alerts = array()) {

		$r = array();
		foreach ($alerts as $alert) {
			$r[] = $alert->getId();
		}
		return $r;
	}

}