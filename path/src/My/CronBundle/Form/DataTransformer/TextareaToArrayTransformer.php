<?php 

// src/Acme/TaskBundle/Form/DataTransformer/IssueToNumberTransformer.php
namespace My\CronBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;


class TextareaToArrayTransformer implements DataTransformerInterface
{

    public function transform($array)
    {

        if (null === $array) {
            return "";
        }

        return implode("\n",$array);
    }

    public function reverseTransform($text)
    {
        if (!$text) {
            return null;
        }

        $array = explode(PHP_EOL, $text);
        
        return $array;

    }
}