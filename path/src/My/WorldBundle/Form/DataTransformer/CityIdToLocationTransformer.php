<?php

namespace My\WorldBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

use My\WorldBundle\Entity\Location;

class CityIdToLocationTransformer implements DataTransformerInterface
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

        return $location->getCity()->getId();
    }

    /**
     * Transforms a string (number) to an object (location).
     *
     * @param  integer $id
     * @return Location|null
     * @throws TransformationFailedException if object (location) is not found.
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $location = $this->om->getRepository('MyWorldBundle:Location')->findLocationByCityId($id);            

        if (null === $location) {
            throw new TransformationFailedException(sprintf(
                'La ville avec le numéro "%s" ne peut pas être trouvé!',
                $id
            ));
        }

        return $location;
    }
}