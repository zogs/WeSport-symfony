<?php

namespace Ws\EventsBundle\Admin\Blocks;

use Symfony\Component\HttpFoundation\Response;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

class RecentEventsBlockService extends BaseBlockService
{
    private $em;

    private $nb_displayed = 6;

    public function getName()
    {
        return 'Recent events';
    }

    public function getDefaultSettings()
    {
        return array();
    }

    public function setEntityManager($em)
    {
        $this->em = $em;
    }

    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
    }

    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // merge settings
        $settings = array_merge($this->getDefaultSettings(), $blockContext->getSettings());

        $events = $this->em->getRepository('WsEventsBundle:Event')->findRecentlyPosted($this->nb_displayed);

        return $this->renderResponse('MyUtilsBundle:Administration:block_recent_events.html.twig', array(
            'block'     => $blockContext->getBlock(),
            'block_context' => $blockContext,
            'settings'  => $settings,
            'events' => $events,
            ), $response);
    }
}