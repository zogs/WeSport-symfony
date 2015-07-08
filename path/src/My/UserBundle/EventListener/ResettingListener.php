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
  private $user;

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

  public function onResettingInitialize( GetResponseUserEvent $event )
  {
    $this->user = $event->getUser();
  }

  public function onResettingSuccess( FormEvent $event )
  {
    $this->flashbag->add("Votre mot de passe a bien été changé et vous êtes maintenant connecté ! ");

    $url = $this->router->generate('fos_user_profile_edit');

    $event->setResponse(new RedirectResponse($url));
    
  }

  public function onResettingCompleted( FilterUserResponseEvent $event )
  {
  }

  public function onRegistrationConfirmed( FilterUserResponseEvent $event )
  {

  }

}