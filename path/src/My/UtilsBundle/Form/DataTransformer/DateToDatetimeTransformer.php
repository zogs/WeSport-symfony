<?php 

// src/Acme/TaskBundle/Form/DataTransformer/IssueToNumberTransformer.php
namespace My\UtilsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;


class DateToDatetimeTransformer implements DataTransformerInterface
{
    /**
     * @var string $format
     */
    private $format;

    /**
     * @param string $format
     */
    public function __construct($format = 'd/m/Y')
    {
        $this->format = $format;
    }

    /**
     * Transforms an datetime object into a string date
     *
     * @param  object \Datetime
     * @return string 
     */
    public function transform($datetime)
    {
        if (null === $datetime) {
            return "";
        }

        return $datetime->format($this->format);
    }

    /**
     * Transforms a date string into a Datetime object
     *
     * @param  string $date
     * @return object \Datetime
     */
    public function reverseTransform($date)
    {
        if (!$date) {
            return null;
        }
        
        return \Datetime::createFromFormat($this->format,$date);

    }
}