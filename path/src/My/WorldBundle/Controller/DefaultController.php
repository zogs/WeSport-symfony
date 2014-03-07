<?php

namespace My\WorldBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        $obj = $em->getRepository('MyWorldBundle:Location')->findOneById(37);

        //$obj->locations = $em->getRepository('MyWorldBundle:Location')->findWorldLocationOf($obj);

    	//$obj = $em->getRepository('MyWorldBundle:City')->findCitiesSuggestions(10,'Beau','FR','A1','21');
        //$beaune = $em->getRepository('MyWorldBundle:City')->findCityByName('Beaune','FR','A1');
        //$obj = $em->getRepository('MyWorldBundle:City')->findCitiesArround(10,$beaune->getLat(),$beaune->getLon(),'FR');
        


    	return $this->render('MyWorldBundle:Default:test.html.twig', array('obj' => $obj));
    }

    public function nextGeoLevelAction(Request $request)
    {
      
        $em = $this->getDoctrine()->getManager();
        $parent = $em->getRepository('MyWorldBundle:Location')->findStateById(
                                                                                $request->query->get('level'),
                                                                                $request->query->get('value'));
        $children = $em->getRepository('MyWorldBundle:Location')->findChildrenStatesByParent($parent);

        $options = '';
        foreach ($children as $child) {
            $options .= '<option value="'.$child->getId().'">'.$child->getName().'</option>';
        }

        return new JsonResponse(array(
            'level'=>$child->getLevel(),
            'options'=>$options
            ));

        //return $this->render('MyWorldBundle:Form:geo_level_options.html.twig', array('obj' => $children));
    }


}
