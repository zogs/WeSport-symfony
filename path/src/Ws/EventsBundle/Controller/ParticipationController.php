<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Event\WsEvents;
use Ws\EventsBundle\Event\AddParticipant;
use Ws\EventsBundle\Event\CancelParticipant;
use Ws\StatisticBundle\Entity\UserStat;


class ParticipationController extends Controller
{
	/**
	 * Add a participant
	 *
	 * @param event
	 *
	 * @return View
	 */
	public function addAction(Event $event,$token)
	{
		if (!$this->get('form.csrf_provider')->isCsrfTokenValid('event_token', $token)) {
			throw new AccessDeniedHttpException('Invalid CSRF token.');
		}

		if($this->get('ws_events.manager')->isNotParticipating($event,$this->getUser())){

			$this->get('ws_events.manager')->saveParticipation($event,$this->getUser(),true);
			$this->get('flashbag')->add("C'est parti! Amusez-vous bien.");		

			//throw event
			$this->get('event_dispatcher')->dispatch(WsEvents::PARTICIPANT_ADD, new AddParticipant($event,$this->getUser()));

			//update stat 
			$this->get('statistic.manager')->setContext('user',$this->getUser())->get()->increment(UserStat::EVENT_PARTICIPATION_ADDED);
			$this->get('statistic.manager')->setContext('user',$event->getOrganizer())->get()->increment(UserStat::EVENT_TOTAL_PARTICIPANTS);

		} else {
			$this->get('flashbag')->add("Il semble que vous participiez déjà",'warning');
		}

		return $this->redirect($this->generateUrl(
			'ws_event_view',array(
				'sport'=>$event->getSport(),
				'slug'=>$event->getSlug(),
				'event'=>$event->getId()
				)
			)
		);
	}

	/**
	 * cancel a participant
	 *
	 * @param event
	 *
	 * @return View
	 */
	public function cancelAction(Event $event,$token)
	{
		if (!$this->get('form.csrf_provider')->isCsrfTokenValid('event_token', $token)) {
			throw new AccessDeniedHttpException('Invalid CSRF token.');
		}

		if($this->get('ws_events.manager')->isParticipating($event,$this->getUser())){

			$this->get('ws_events.manager')->deleteParticipation($event,$this->getUser(),true);
			$this->get('flashbag')->add("Ok... une prochaine fois peut être !",'info');

			//throw event
			$this->get('event_dispatcher')->dispatch(WsEvents::PARTICIPANT_CANCEL, new CancelParticipant($event,$this->getUser()));  

			$this->get('statistic.manager')->setContext('user',$this->getUser())->get()->increment(UserStat::EVENT_PARTICIPATION_CANCELED); 
			$this->get('statistic.manager')->setContext('user',$event->getOrganizer())->get()->decrement(UserStat::EVENT_TOTAL_PARTICIPANTS); 
		}

		return $this->redirect($this->generateUrl(
			'ws_event_view',array(
				'sport'=>$event->getSport(),
				'slug'=>$event->getSlug(),
				'event'=>$event->getId()
				)
			)
		);
	}
}
