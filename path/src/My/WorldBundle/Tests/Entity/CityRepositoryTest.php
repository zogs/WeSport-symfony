<?php

namespace My\WorldBundle\Tests\Repository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use My\WorldBundle\Entity\City;

class CityRespositoryTest extends WebTestCase {

    private $repo;

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->repo = $kernel
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('MyWorldBundle:City')
        ;
    }

    public function testFindCityByUNI()
    {
        $lyon = $this->repo->findCityByUNI('-2074875');
        $this->assertEquals('Lyon',$lyon->getName());
    }

    public function testFindCityByName()
    {
        $beaune = $this->repo->findCityByName('Beaune','FR','A1','21');
        $this->assertEquals(1,count($beaune));
        $this->assertEquals('Beaune',$beaune->getName());
        $this->assertEquals('A1',$beaune->getADM1());
    }

    public function testFindCitiesSuggestions()
    {
        $Beau = $this->repo->findCitiesSuggestions('15','Beau','FR','A1');        
        $this->assertInstanceOf('My\WorldBundle\Entity\City',$Beau[0]);
        $this->assertCount(15,$Beau);
        $this->assertStringStartsWith('Beau',$Beau[0]->getName());

    }

    public function testFindCitiesArround()
    {
        $beaune = $this->repo->findCityByName('Beaune','FR','A1','21');
        $cities = $this->repo->findCitiesArround(10,$beaune->getLat(),$beaune->getLon(),'FR');
        $count = count($cities);
        $this->assertGreaterThan(10,$count);
        $this->assertInstanceOf('My\WorldBundle\Entity\City',$cities[0]);

    }

}
?>