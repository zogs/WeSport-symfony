<?php

// src/AppBundle/Controller/RedirectingController.php
namespace My\UtilsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use My\UserBundle\Entity\User;
use Ws\EventsBundle\Entity\Event;

class TestObjectController extends Controller
{
	
    public function testObjectChangeAction(Request $request)
    {
       $homme = new \stdClass();
       $homme->type = 'homme';

       $garou = clone $homme;
       $garou->type = 'loup-garou';

       $changes = \My\UtilsBundle\Utils\Object::getChanges($homme,$garou);

       dump($homme);
       dump($garou);
       dump($changes);
       exit();

    }
}