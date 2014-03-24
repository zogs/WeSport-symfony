<?php

namespace My\UserBundle\Service;

use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManager;

class RegistrationListener implements EventSubscriberInterface
{

  private $em;

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

  public static function getSubscribedEvents()
  {
    return array(FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationInitialise');
  }

  public function onRegistrationInitialise( FormEvent $event )
  {

    var_dump($event);
    exit('before registration');

    // what do
  }
}