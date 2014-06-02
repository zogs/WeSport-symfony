<?php

namespace My\WorldBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

use My\WorldBundle\Entity\Location;

class CityNameToLocationTransformer implements DataTransformerInterface
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
     * Transforms an object (location) to a integer (id).
     *
     * @param  Issue|null $location
     * @return string
     */
    public function transform($location)
    {     
        if (null === $location) {
            return "";
        }
        \My\UtilsBundle\Utils\Debug::debug($location);
        exit();
        return $location->getCity()->getName();
    }

    /**
     * Transforms a string (number) to an object (location).
     *
     * @param  integer $id
     * @return Location|null
     * @throws TransformationFailedException if object (location) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $location = $this->om->getRepository('MyWorldBundle:Location')->findLocationByCityName($name);            

        if (null === $location) {
            throw new TransformationFailedException(sprintf(
                'La ville avec le nom "%s" ne peut pas être trouvé!',
                $name
            ));
        }

        return $location;
    }
}