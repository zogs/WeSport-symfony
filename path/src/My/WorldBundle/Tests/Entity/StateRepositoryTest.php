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

    public function testFindStateByCodes()
    {
        $bourgogne = $this->repo->findStateByCodes('FR','A1','ADM1');
        $this->assertEquals('Bourgogne',$bourgogne->getName());

        $cantal = $this->repo->findStateByCodes('FR','15','ADM2');
        $this->assertEquals('Departement du Cantal',$cantal->getName());    

        $ecosse = $this->repo->findStateByCodes('UK','SCT','ADM1');
        $this->assertEquals('Scotland',$ecosse->getName());
    }
}
?>