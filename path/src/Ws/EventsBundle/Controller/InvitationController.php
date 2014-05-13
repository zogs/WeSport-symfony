<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

use Ws\EventsBundle\Entity\Event;
use Ws\EventsBundle\Entity\Serie;
use Ws\EventsBundle\Form\Type\InvitationType;


class InvitationController extends Controller
{
	/**
	 * Show invitation form and perform invitation
	 *
	 * @param  request
	 * @return  view
	 * 
	 */
	public function newAction(Request $request)
	{
		$form = $this->createForm(new InvitationType());
		$form->handleRequest($request);

		if($form->isValid()){

			$data = $form->getData();
			print_r($data);
			exit();
		}

		return $this->render('WsEventsBundle:Invitation:new.html.twig',array(
			'form' => $form->createView(),
			));

	}
}