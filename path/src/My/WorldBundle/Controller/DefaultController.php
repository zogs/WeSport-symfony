<?php

namespace My\WorldBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MyWorldBundle:Default:index.html.twig', array('name' => $name));
    }

    public function testAction()
    {

    	$em = $this->getDoctrine()->getManager();

        $obj = new \stdClass();
        $obj->CC1 = 'FR';
        $obj->ADM1 = 'A1';
        $obj->ADM2 = '21';
        $obj->city = '-2041884';

        //$obj = $em->getRepository('WsEventsBundle:Event')->findOneById(25);

        //$obj = $obj->getLocation();

        //$obj = $em->getRepository('MyWorldBundle:Location')->findOneById(7);

        //$obj->locations = $em->getRepository('MyWorldBundle:Location')->findWorldLocationOf($obj);

    	//$obj = $em->getRepository('MyWorldBundle:City')->findCitiesSuggestions(10,'Beau','FR','A1','21');
        $beaune = $em->getRepository('MyWorldBundle:City')->findCityByName('Beaune','FR','A1');
        
        $obj = $em->getRepository('MyWorldBundle:City')->findCitiesArround(10,$beaune->getLat(),$beaune->getLon(),'FR');
       
    	return $this->render('MyWorldBundle:Default:test.html.twig', array('obj' => $obj));
    }

    private function geoLocateObject($object)
    {
     
        if($object->getCC1()!='')
        {            
            $object->location['country'] = $this->getDoctrine()
                                ->getManager()
                                ->getRepository('MyWorldBundle:Country')
                                ->findCountryName($object->getCC1());
        }

        if($object->getADM1()!='')
        {            
            $object->location['region'] = $this->getDoctrine()
                                ->getManager()
                                ->getRepository('MyWorldBundle:Region')
                                ->findRegionName($object->getADM1(),$object->getCC1());
        }

        return $object;
    }

    private function geoLocateObjects($objects)
    {
        foreach ($objects as $key => $object)
        {
            $objects[$key] = $this->geoLocateObject($object);
        }
        return $objects;
    }
}
