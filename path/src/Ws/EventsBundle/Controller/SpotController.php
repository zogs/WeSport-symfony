<?php

namespace Ws\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ws\EventsBundle\Entity\Spot;

class SpotController extends Controller
{
		public function indexAction()
		{

			$spots = $this->getDoctrine()->getEntityManager()->getRepository('WsEventsBundle:Spot')->findAll();

			return $this->render('WsEventsBundle:Spot:index.html.twig',array(
				'spots' => $spots
				));
		}

		public function createAction(Request $request)
		{
			$spot = new Spot();

			$form = $this->createForm('spot_type',$spot);

			$form->handleRequest($this->getRequest());

			if($form->isValid()){

				$spot = $form->getData();

				if($this->get('ws_events.spot.manager')->saveSpot($spot)){

					$this->get('flashbag')->add('Bravo, le spot est sauvegardé !','success');

					return $this->redirect($this->generateUrl('ws_spot_index'));
				} 
				else {
					$this->get('flashbag')->add('Erreur !!!','error');
				}
			}			

			return $this->render('WsEventsBundle:Spot:new.html.twig', array(
				'form' => $form->createView()
				));
		}

		public function autoCompleteAction($country,$prefix)
		{
			$em = $this->getDoctrine()->getManager();

			$spots = $em->getRepository('WsEventsBundle:Spot')->findSuggestions(20,$prefix,$country);

			
			foreach($spots as $k => $spot){

            $spots[$k] = array();
            $spots[$k]['name'] = $spot->getName();
            $spots[$k]['city'] = $spot->getLocation()->getCity()->getName();
            $spots[$k]['address'] = $spot->getAddress();
            $spots[$k]['country'] = $spot->getCountryCode();
            $spots[$k]['id'] = $spot->getId();
            $spots[$k]['token'] = $spot->getSlug();

	        }

	        return new JsonResponse($spots);
	        
		}
}