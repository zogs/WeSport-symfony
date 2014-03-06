<?php
 
namespace My\FlashBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;


class FlashbagExtension extends \Twig_Extension
{

	protected $container, $flashbag;

	public function __construct(ContainerInterface $container = null)
	{
		$this->container = $container;
		$this->flashbag = $container->get('flashbag');
	}
 

 	public function renderAll($container = false)
 	{
 		$notifications = $this->flashbag->all();

 		if( count($notifications)>0 ){
 			return $this->container->get('templating')->render(
 				"MyFlashBundle:Notifications:all.html.twig",
 				compact("notifications","container")
 				);
 		}
 		return null;
 	}


 	public function getFunctions()
    {
        return array(
            'flashbag_all' => new \Twig_Function_Method($this, 'renderAll', array('is_safe' => array('html')))            
        );
    }


    public function getName()
    {
        return 'my_flashbag_extension';
    }

 
}