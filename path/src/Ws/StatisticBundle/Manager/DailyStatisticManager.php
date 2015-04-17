<?php

namespace Ws\StatisticBundle\Manager;

use My\ManagerBundle\Manager\AbstractManager;
use Symfony\Component\Yaml\Parser;

use My\UserBundle\Entity\User;
use My\UtilsBundle\Utils\String;

class DailyStatisticManager extends AbstractManager
{

	private $stats;

	public function getDailyStats() {

		if(!empty($this->stats)) return $this->stats;

		$res = array();
		$res['registration'] = '';
		$res['events_planned'] = '';
		$res['events_confirmed'] =  '';
		$res['events_deposed'] = '';

		$events_planned = $this->em->getRepository('WsEventsBundle:Event')->findEventsInFuturDays(0);
		$res['events_planned'] = count($events_planned);
		$i = 0;
		foreach ($events_planned as $event) {
			if($event->getConfirmed() == true) $i++;
		}
		$res['events_confirmed'] = $i;

		$events_deposed = $this->em->getRepository('WsEventsBundle:Event')->findEventsDeposedToday();
		$res['events_deposed'] = count($events_deposed);

		$registration = $this->em->getRepository('MyUserBundle:User')->findRegistrationLastFewDays(0);
		$res['registration'] = count($registration);

		return $this->stats = $res;
		
		
	}
	public function sendEmailAdmins() {

		$emails = $this->container->getParameter('mailer.emails.statistic');

		$stats = $this->getDailyStats();
		
		foreach ($emails as $email) {
			
			$this->sendEmail($stats,$email);
		}

		return $emails;
	}


	private function sendEmail($stats,$email) {

		$this->container->get('statistic.mailer')->sendDailyStats($stats,$email);
	}

}
?>