<?php

namespace My\WorldBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use My\WorldBundle\Entity\City;
use My\WorldBundle\Entity\Location;

class CityController extends Controller
{
    public function autoCompleteAction($country,$prefix)
    {
        $em = $this->getDoctrine()->getManager();

        $cities = $em->getRepository('MyWorldBundle:City')->findCitiesSuggestions(10,$prefix,$country);

        foreach($cities as $k => $city){

            $city->upperstate = $em->getRepository('MyWorldBundle:State')->findStateByCodes($city->getCC1(),$city->getADM1(),$city->getADM2(),$city->getADM3(),$city->getADM4());

            $cities[$k] = array();
            $cities[$k]['name'] = $city->getName();
            $cities[$k]['state'] = $city->upperstate->getName();
            $cities[$k]['token'] = preg_split('/[ -]/',$city->getName().' '.$city->upperstate->getName());
            $cities[$k]['id'] = $city->getId();
            $cities[$k]['value'] = $city->getId();

        }

        return new JsonResponse($cities);
    }

    public function searchAction(Request $request)
    {        
        $location = new Location();
        $form = $this->createForm('city_to_location_type',$location);

        $form->handleRequest($request);

        if($form->isValid()){

            $location = $form->getData();
            return $this->redirect($this->generateUrl('my_world_city_view',array('city'=>$location->getCity()->getId())));        
        }
        
        return $this->render('MyWorldBundle:City:form.html.twig',array(
            'form' => $form->createView()
            ));
    }

    public function viewAction(City $city)
    {        
        return $this->render('MyWorldBundle:City:view.html.twig',array(
            'city'=>$city
            ));
    }

}
