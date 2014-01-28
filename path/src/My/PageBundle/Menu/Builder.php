<?php

namespace My\PageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Doctrine\ORM\Entitymanager;


class Builder
{

    private $factory;
    private $em;

    public function __construct(FactoryInterface $factory, Entitymanager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    public function createMainMenu()
    {
        $menu = $this->factory->createItem('root', array('attributes' => array('class' => 'nav')));        

        $menu->addChild(
            'Home', array(
                'label' => 'Home',
                'uri' => '/',
                'attributes' => array('class'=>'brand'),                
                ) 
        );

        $pages = $this->em->getRepository('MyPageBundle:Page')->findAll();        
        foreach($pages as $page)
        {
            $menu->addChild(
                'page_'.$page->getId(),
                array(
                    'label'=> $page->getTitle(),
                    'route'=> 'page_show',
                    'routeParameters'=> array('id'=>$page->getId()),
                )
            );
        }

        return $menu;
    }
}