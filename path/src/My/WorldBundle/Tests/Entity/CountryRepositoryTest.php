<?php

namespace My\WorldBundle\Tests\Repository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use My\WorldBundle\Entity\Country;

class CountryRespositoryTest extends WebTestCase {

    private $repo;

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->repo = $kernel
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('MyWorldBundle:Country')
        ;
    }

    public function testFindAllCountry()
    {
        $allcountry = $this->repo->findAllCountry();
        $this->assertEquals(265,count($allcountry));
        $this->assertInstanceOf('My\WorldBundle\Entity\Country',$allcountry[0]);
         $this->assertInstanceOf('My\WorldBundle\Entity\Country',$allcountry[14]);

    }

    public function testFindCountryByCode()
    {

        $france = $this->repo->findCountryByCode('FR');
        $this->assertEquals('France',$france->getName());
        $this->assertEquals('fra',$france->getLang());

        $argentine = $this->repo->findCountryByCode('AR');
        $this->assertEquals('Argentina',$argentine->getName());
        $this->assertEquals('spa',$argentine->getLang());

        $allemagne = $this->repo->findCountryByCode('GM');
        $this->assertEquals('Germany',$allemagne->getName());
        $this->assertEquals('deu',$allemagne->getLang());

    }

    public function testFindCountryById()
    {

        $france = $this->repo->findCountryById(82);
        $this->assertEquals('France',$france->getName());
        $this->assertEquals('fra',$france->getLang());

        $argentine = $this->repo->findCountryById(11);
        $this->assertEquals('Argentina',$argentine->getName());
        $this->assertEquals('spa',$argentine->getLang());

        $allemagne = $this->repo->findCountryById(90);
        $this->assertEquals('Germany',$allemagne->getName());
        $this->assertEquals('deu',$allemagne->getLang());

    }
}
?>