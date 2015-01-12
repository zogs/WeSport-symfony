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
		if (!$this->get('form.csrf_provider')->isCsrfTokenValid('participation_add', $token)) {
			throw new AccessDeniedHttpException('Invalid CSRF token.');
		}

		if($this->get('ws_events.manager')->isNotParticipating($event,$this->getUser())){

			$this->get('ws_events.manager')->saveParticipation($event,$this->getUser(),true);
			$this->get('flashbag')->add("C'est parti! Amusez-vous bien.");	

			//confirm event if nb min is reached
			if($event->countParticipation() == $event->getNbMin()){
				$this->get('ws_events.manager')->confirmEvent($event);
			}	

			//throw event
			$this->get('event_dispatcher')->dispatch(WsEvents::PARTICIPANT_ADD, new AddParticipant($event,$this->getUser()));

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
		if (!$this->get('form.csrf_provider')->isCsrfTokenValid('participation_cancel', $token)) {
			throw new AccessDeniedHttpException('Invalid CSRF token.');
		}

		if($this->get('ws_events.manager')->isParticipating($event,$this->getUser())){

			$this->get('ws_events.manager')->deleteParticipation($event,$this->getUser(),true);
			$this->get('flashbag')->add("Ok... une prochaine fois peut être !",'info');

			//confirm event if nb min is reached
			if($event->countParticipation() < $event->getNbMin()){
				$this->get('ws_events.manager')->unconfirmEvent($event);
			}

			//throw event
			$this->get('event_dispatcher')->dispatch(WsEvents::PARTICIPANT_CANCEL, new CancelParticipant($event,$this->getUser()));  
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
