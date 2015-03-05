<?php

namespace Ws\EventsBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Ws\EventsBundle\Entity\Event;

class EventLinkExtension extends \Twig_Extension
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function getFilters()
    {
        return array(
            'link2event' => new \Twig_Filter_Method($this, 'eventLink'),
        );
    }

    public function eventLink(Event $event)
    {

        return $this->router->generate('ws_event_view',array('event'=>$event->getId(),'slug'=>$event->getSlug()));
    }

    public function getName()
    {
        return 'event_link';
    }
}