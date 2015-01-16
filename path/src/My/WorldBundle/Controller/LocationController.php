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

    public function nearestLatLonAction($lat,$lon,$country = null)
    {

        if(!is_numeric($lat) || !is_numeric($lon)) throw new \Exception('lat and lon must be numeric');

        $location = $this->get('world.location_manager')->getLocationFromNearestCityLatLon($lat,$lon,$country);

        dump($location);
        exit();
    }

}
