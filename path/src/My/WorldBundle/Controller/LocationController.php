<?php

namespace My\WorldBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use My\WorldBundle\Entity\City;
use My\WorldBundle\Entity\Location;

class LocationController extends Controller
{
    public function viewAction(Location $location)
    {
        return $this->render('MyWorldBundle:Location:view.html.twig',array(
            'location' => $location
            ));
    }

    public function formSelectLocationAction(Request $request)
    {
    	$location = null;

    	$form = $this->createForm('location_select');
    	$form->handleRequest($request);

    	if($form->isValid()){

		$location = $form->getData();    			
    	}

    	return $this->render('MyWorldBundle:Form:test_location_select.html.twig',array(
    		'form' => $form->createView(),
    		'location' => $location,
    		));
    }


    public function nextGeoLevelAction(Request $request)
    {     
        //entity manager 
        $em = $this->getDoctrine()->getManager();

        //find current state
        $parent = $em->getRepository('MyWorldBundle:Location')->findStateById($request->query->get('level'),$request->query->get('value'));

        //find children of the current state
        $children = $em->getRepository('MyWorldBundle:Location')->findChildrenStatesByParent($parent);

        //create html options
        $level = '';
        $options = '';
        if(!empty($children)){
            $options .= '<option value="">'.$this->getSelectBoxHelper($children[0]->getLevel()).'</options>';
            foreach ($children as $child) {
                $options .= '<option value="'.$child->getId().'">'.$child->getName().'</option>';
            }
            $level = $child->getLevel();

        }               

        return new JsonResponse(array(
            'level'=>$level,
            //'location'=>$actual_location->getId(),
            'options'=>$options,
            ));
        
    }

    private function getSelectBoxHelper($level)
    {
        $helpers = array(
            'country'=>"Sélectionnez un pays",
            'region'=>"Séléectionnez une région",
            'departement'=>"Sélectionnez un département",
            'district'=>"Select a district",
            'division'=>"Select a division",
            'city'=>"Sélectionnez une ville"
            );

        return $helpers[$level];
    }

    public function nearestLatLonAction($lat,$lon,$country = null)
    {

        if(!is_numeric($lat) || !is_numeric($lon)) throw new \Exception('lat and lon must be numeric');

        $location = $this->get('world.location_manager')->getLocationFromNearestCityLatLon($lat,$lon,$country);

        dump($location);
        exit();
    }

}
