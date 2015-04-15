<?php

namespace Ws\EventsBundle\Admin\Blocks;

use Symfony\Component\HttpFoundation\Response;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

class ComingParticipationsBlockService extends BaseBlockService
{
    private $em;

    private $nb_displayed = 5;

    public function getName()
    {
        return 'Coming participations';
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

        $participants = $this->em->getRepository('WsEventsBundle:Participation')->findComingSoon($this->nb_displayed);

        return $this->renderResponse('MyUtilsBundle:Administration:block_coming_participations.html.twig', array(
            'block'     => $blockContext->getBlock(),
            'block_context' => $blockContext,
            'settings'  => $settings,
            'participants' => $participants,
            ), $response);
    }
}