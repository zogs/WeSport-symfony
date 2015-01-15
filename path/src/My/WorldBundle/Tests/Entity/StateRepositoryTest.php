<?php

namespace My\WorldBundle\Tests\Repository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use My\WorldBundle\Entity\State;

class StateRespositoryTest extends WebTestCase {

    private $repo;

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->repo = $kernel
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('MyWorldBundle:State')
        ;
    }

    public function testFindStateByCode()
    {
        $bourgogne = $this->repo->findStateByCode('FR','A1','ADM1');
        $this->assertEquals('Bourgogne',$bourgogne->getName());

        $cantal = $this->repo->findStateByCode('FR','15','ADM2');
        $this->assertEquals('Departement du Cantal',$cantal->getName());    

        $ecosse = $this->repo->findStateByCode('UK','SCT','ADM1');
        $this->assertEquals('Scotland',$ecosse->getName());
    }

    public function testFindStatesByParent()
    {
        $departement_bourguigon = $this->repo->findStatesByParent('ADM2','FR','A1');

        $this->assertEquals(4,count($departement_bourguigon));
    }
}
?>