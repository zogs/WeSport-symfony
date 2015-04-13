<?php
namespace My\ManagerBundle\Manager;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\Container;


abstract class AbstractManager
{
     /**
     * The Dependency injection Container
     *
     * @var \Symfony\Component\DependencyInjection\Containe  
     */
    protected $container;
    /**
     * The Entity Manager
     *
     * @var \Doctrine\ORM\EntityManager  The Doctrine Entity Manager
     */
    protected $em;

    /**
     * The Security context
     *
     * @var Symfony\Component\Security\Core\SecurityContext;
     */
    protected $context;

    /**
     * The Router
     *
     * @var use Symfony\Bundle\FrameworkBundle\Routing\Router;
     */
    protected $router;


    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->context = $container->get('security.context');
        $this->router = $container->get('router');
    }
    /**
     * {@inheritDoc}
     */
    public function save($object, $flush = true)
    {

        $this->em->persist($object);

        if($flush === true)
        {
            $this->flush();
        }

        return $object;
    }

    /**
     * {@inheritDoc}
     */
    public function delete($object, $flush = true)
    {

        $this->em->remove($object);

        if($flush === true)
        {
            $this->flush();
        }

        return true;
    }

    /**
     * Convenience method providing access to the entity manager flush method
     */
    public function flush()
    {
        $this->em->flush();
    }



    /**
     * Set entity manager. Setter for dependency injection
     *
     * @param \Doctrine\ORM\EntityManager $entity_manager
     */
    public function setEntityManager(EntityManager $entity_manager)
    {
        $this->em = $entity_manager;
    }

    /**
     * Get current user context
     */
    public function getUser()
    {
        return $this->context->getToken()->getUser();
    }

}
?>