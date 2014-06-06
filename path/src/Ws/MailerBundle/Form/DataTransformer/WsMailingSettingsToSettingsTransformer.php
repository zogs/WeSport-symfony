<?php

namespace Ws\MailerBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class WsMailingSettingsToSettingsTransformer implements DataTransformerInterface
{

    private $user;

    /**
     * @param User
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Transforms an entity (MyUserBundle:Settings) to an entity (WsMailerBundle:Settings)
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($settings)
    {
        if (null === $settings) {
            return "";
        }

        return $settings->getWsMailerSettings();
    }

    /**
     * Transforms an entity (WsMailerBundle:Settings) to an entity (MyUserBundle:Settings)
     *
     * @param  WsMailerBundle:Settings
     * @return MyUserBundle:Settings|null
     */
    public function reverseTransform($WsMailerSettings)
    {
        if(null === $WsMailerSettings){
            return null;
        }

        $this->user->getSettings()->setWsMailerSettings($WsMailerSettings);

        return $this->user->getSettings();
       
    }
}