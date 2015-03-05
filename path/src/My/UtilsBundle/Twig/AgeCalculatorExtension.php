<?php

namespace My\UtilsBundle\Twig;


class AgeCalculatorExtension extends \Twig_Extension
{
    private $today;

    public function __construct()
    {        
        $this->today = new \Datetime('now');
    }

    public function getFilters()
    {
        return array(
            'calculAge' => new \Twig_Filter_Method($this, 'calculAge'),
        );
    }

    public function calculAge(\Datetime $datetime)
    {        
        return $this->today->diff($datetime)->format('%y');
    }

    public function getName()
    {
        return 'age_calculator';
    }
}