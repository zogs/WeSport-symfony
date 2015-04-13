<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use My\UserBundle\Entity\User;
use Ws\EventsBundle\Entity\Alert;
use Ws\EventsBundle\Entity\Search;
use Ws\EventsBundle\Entity\Follow;

class FollowController extends Controller
{
		public function createAction(User $organizer)
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

				$this->get('ws_events.alert.manager')->saveAlert($alert);
				$this->get('ws_events.follow.manager')->saveFollowFromAlert($alert);

				$this->get('flashbag')->add('Bravo, vous suivez désormais '.ucfirst($organizer->getUsername()).' !','success');

				if($form->get('referer')->getData()) {
					return $this->redirect($form->get('referer')->getData());
				}
				return $this->redirect($this->generateUrl('ws_calendar'));
				
			}

			return $this->render('WsEventsBundle:Follow:create.html.twig',array(
				'organizer'=> $organizer,
				'form'=> $form->createView(),
				));
		}

		public function editAction(Follow $follow)
		{

			$this->get('session')->set('followControllerReferer',$this->getRequest()->headers->get('referer'));

			return $this->render('WsEventsBundle:Follow:edit.html.twig',array(
				'follow'=> $follow,
				));
		}

		public function deleteAction(Follow $follow)
		{
			$this->get('ws_events.follow.manager')->deleteFollow($follow);

			if($referer = $this->get('session')->get('followControllerReferer')){
				return $this->redirect($referer);
			}
			
			return $this->redirect($this->generateUrl('ws_calendar'));
		}

		
}