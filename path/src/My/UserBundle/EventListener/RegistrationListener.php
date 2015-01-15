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

use My\FlashBundle\Controller\FlashController;

class RegistrationListener implements EventSubscriberInterface
{

  private $em;
  private $router;
  private $flashbag;

  public function __construct(EntityManager $em, UrlGeneratorInterface $router, FlashController $flashbag)
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

    $url = $this->router->generate('ws_calendar');

    $event->setResponse(new RedirectResponse($url));
  }

  public function onRegistrationCompleted( FilterUserResponseEvent $event )
  {

    $this->flashbag->add('Pour terminer votre inscription, cliquez sur le lien dans le mail que nous vous avons envoyé !');
    $this->flashbag->add("Si vous ne le trouvez pas, pensez à vérifier le dossier \"Indésirables\" de votre boîte mail",'warning');
  }

  public function onRegistrationConfirm( GetResponseUserEvent  $event )
  {

    $this->flashbag->add("Félicitations, vous êtes maintenant connecté !");

    $url = $this->router->generate('ws_calendar');

    $event->setResponse(new RedirectResponse($url));
  }

  public function onRegistrationConfirmed( FilterUserResponseEvent $event )
  {

  }

}