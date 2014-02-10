<?php

namespace My\WorldBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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

}
