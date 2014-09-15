<?php

namespace My\WorldBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Container;

use My\WorldBundle\Manager\LocationManager;

class LocationFromIpController
{

    public $container;

    /**
     * Set directly the whole Service Container as this is a service Controller
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

    }

    /**
     * Find the nearest city from one IP and return the Location object
     * Using the database ../GeoIpDatabase/GeoLiteCity.dat
     * @param string IPv4
     * @return Location object
     */

    public function getLocationFromIp($ip = '193.52.250.230')
    {
    
        include(getcwd()."/../src/My/WorldBundle/GeoIpDatabase/API/php-1.11/geoipcity.inc");
        include(getcwd()."/../src/My/WorldBundle/GeoIpDatabase/API/php-1.11/geoipregionvars.php");

        $gi = geoip_open(getcwd()."/../src/My/WorldBundle/GeoIpDatabase/Database/GeoLiteCity.dat",GEOIP_STANDARD);

        $record = geoip_record_by_addr($gi,$ip);

        //echo $record->country_name . "\n";
        //echo $GEOIP_REGION_NAME[$record->country_code][$record->region] . "\n";
        //echo $record->city . "\n";
        //echo $record->postal_code . "\n";
        //echo $record->latitude . "\n";
        //echo $record->longitude . "\n";
        if(!isset($record)) return false;
        
        $location = $this->container->get('world.location_manager')->getLocationFromNearestCityLatLon($record->latitude,$record->longitude,$record->country_name);

        geoip_close($gi);

        return $location;

    }

}
