<?php

namespace My\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManager;

class ResettingListener implements EventSubscriberInterface
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
      FOSUserEvents::RESETTING_RESET_INITIALIZE => 'onResettingInitialize',
      FOSUserEvents::RESETTING_RESET_SUCCESS => 'onResettingSuccess',
      FOSUserEvents::RESETTING_RESET_COMPLETED => 'onResettingCompleted',
      );
  }

  public function onResettingInitialize( FormEvent $event )
  {

  }

  public function onResettingSuccess( FilterUserResponseEvent $event )
  {
    
  }

  public function onResettingCompleted( GetResponseUserEvent  $event )
  {
    $this->flashbag->add("Et hop, mot de passe changé! C'est reparti! ");

    $url = $this->router->generate('user_profil_view');

    $event->setResponse(new RedirectResponse($url));
  }

  public function onRegistrationConfirmed( FilterUserResponseEvent $event )
  {

  }

}