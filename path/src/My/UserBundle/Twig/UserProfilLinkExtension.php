<?php

// src/Acme/DemoBundle/Twig/AcmeExtension.php
namespace My\UserBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use My\UserBundle\Entity\User;

class UserProfilLinkExtension extends \Twig_Extension
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function getFilters()
    {
        return array(
            'link2user' => new \Twig_Filter_Method($this, 'userLink'),
        );
    }

    public function userLink(User $user)
    {

        return $this->router->generate('user_profil_view',array('user'=>$user->getId(),'username'=>$user->getUsernameCanonical()));
    }

    public function getName()
    {
        return 'user_link';
    }
}