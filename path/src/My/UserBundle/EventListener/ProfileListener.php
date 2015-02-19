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

class ProfileListener implements EventSubscriberInterface
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
      FOSUserEvents::PROFILE_EDIT_SUCCESS => 'onEditSuccess',
      );
  }

  public function onEditSuccess( FormEvent $event )
  {
    $action = ($event->getForm()->get('action')->getData() != null)? '?action='.$event->getForm()->get('action')->getData() : '';
    $url = $this->router->generate('fos_user_profile_edit').$action;
    $event->setResponse(new RedirectResponse($url));
  }

}