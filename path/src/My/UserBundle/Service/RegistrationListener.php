<?php

namespace My\UserBundle\Service;

use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManager;

class RegistrationListener implements EventSubscriberInterface
{

  private $em;
  private $router;
  private $flashbag;

  public function __construct(EntityManager $em, UrlGeneratorInterface $router, $flashbag)
  {
    $this->em = $em;
    $this->router = $router;
    $this->flashbag = $flashbag;
  }

  public static function getSubscribedEvents()
  {
    return array(
      FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
      FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted',
      FOSUserEvents::REGISTRATION_CONFIRM => 'onRegistrationConfirm',
      FOSUserEvents::REGISTRATION_CONFIRMED => 'onRegistrationConfirmed'
      );
  }

  public function onRegistrationSuccess( FormEvent $event )
  {

    $url = $this->router->generate('ws_events_calendar');

    $event->setResponse(new RedirectResponse($url));
  }

  public function onRegistrationCompleted( FOS\UserBundle\Event\FilterUserResponseEvent $event )
  {

    $this->flashbag->clear()->add('info','Un email a été envoyé blabla...');
  }

  public function onRegistrationConfirm( FOS\UserBundle\Event\FilterUserResponseEvent $event )
  {

    $url = $this->router->generate('ws_events_calendar');

    $event->setResponse(new RedirectResponse($url));
  }

  public function onRegistrationConfirmed( FOS\UserBundle\Event\FilterUserResponseEvent $event )
  {

  }

}