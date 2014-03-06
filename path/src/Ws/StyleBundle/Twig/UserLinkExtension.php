<?php

// src/Acme/DemoBundle/Twig/AcmeExtension.php
namespace Ws\StyleBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use My\UserBundle\Entity\User;

class UserLinkExtension extends \Twig_Extension
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function getFilters()
    {
        return array(
            'link2profil' => new \Twig_Filter_Method($this, 'linkProfil'),
        );
    }

    public function linkProfil(User $user)
    {

        return $this->router->generate('user_fiche_profil',array('user'=>$user->getId(),'username'=>$user->getUsername()));
    }

    public function getName()
    {
        return 'user_profil_link';
    }
}