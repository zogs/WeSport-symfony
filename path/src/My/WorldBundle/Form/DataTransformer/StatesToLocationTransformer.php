<?php

// src/Acme/TaskBundle/Form/DataTransformer/IssueToNumberTransformer.php
namespace My\WorldBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

use My\WorldBundle\Entity\Location;

class StatesToLocationTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms ( call on initialize the form )
     *
     * @param  
     * @return 
     */
    public function transform($entity)
    {        
        
        if($entity === null || $entity == ''){
            return null;
        }

        if(!is_object($entity)){
            throw new UnexpectedTypeException($entity, 'object');
        }

        return $entity->getId();
    }

    /**
     * Reverse Transforms  ( call on handle the form )
     *
     * @param  
     *
     * @return 
     *
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($location)
    {
        
        var_dump($location);
        exit();
        if($location->getId()==0){
            return null;
        }

        $location = $this->om->getRepository('MyWorldBundle:Location')->findOneById($location->getId());

        return $location;
        
    }
}