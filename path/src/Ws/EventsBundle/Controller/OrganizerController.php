<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use My\UserBundle\Entity\User;
use Ws\EventsBundle\Entity\Alert;
use Ws\EventsBundle\Entity\Search;

class OrganizerController extends Controller
{
		public function followAction(User $organizer)
		{

			$search = new Search();
			$search->setUser($this->getUser());
			$search->setOrganizer($organizer);
			
			$alert = new Alert();
			$alert->setUser($this->getUser());
			$alert->setSearch($search);

			$form = $this->createForm('alert',$alert,array(
				'action' => $this->getRequest()->getUri(),
				'attr' => array('novalidate'=>'novalidate')
				));

			$form->add('referer','hidden',array(
				'data'=>$this->getRequest()->headers->get('referer'),
				'mapped'=>false
				));			
			
			$form->handleRequest($this->getRequest());

			if( $form->isValid()) {

				$alert = $form->getData();

				if( $this->get('ws_events.alert.manager')->saveAlert($alert)) {

					$this->get('flashbag')->add('Bravo, vous suivez désormais '.ucfirst($organizer->getUsername()).' !','success');

					if($form->get('referer')->getData()) {
						return $this->redirect($form->get('referer')->getData());
					}
					return $this->redirect('ws_calendar');
				}
			}

			return $this->render('WsEventsBundle:Organizer:follow.html.twig',array(
				'organizer'=> $organizer,
				'form'=> $form->createView(),
				));
		}

		
}