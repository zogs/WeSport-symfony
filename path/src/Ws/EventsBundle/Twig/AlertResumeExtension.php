<?php

// src/Acme/DemoBundle/Twig/AcmeExtension.php
namespace Ws\EventsBundle\Twig;

use Ws\EventsBundle\Entity\Alert;
use Ws\EventsBundle\Entity\Event;


class AlertResumeExtension extends \Twig_Extension
{
    private $translator;

    public function __construct($translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return array(
            'alertResumizer' => new \Twig_Filter_Method($this, 'generateResume'),
            'alertContext' => new \Twig_Filter_Method($this, 'generateContext'),
            'alertFrequency' => new \Twig_Filter_Method($this, 'generateFrequency')
        );
    }

    public function generateResume(Alert $alert)
    {
        $tx = $this->generateContext($alert);
        $tx .= $this->generateFrequency($alert);

        return $tx;
    }

    public function generateContext(Alert $alert)
    {
        $search = $alert->getSearch();
        
        $tx = '';
        // Les annonces de 
        $tx .= '<span>Les annonces de</span>';
        
        // Football Volley Kayak 
        foreach ($search->getSports() as $key => $sport) {
            $tx .= '<strong> '.$sport->getName().' </strong>';
        }

        // à Montpellier 
        $tx .= '<span>à <strong>'.$search->getLocation()->getCity()->getName().'</strong> </span>';
        
        // étendu de 50km 
        if($search->hasArea()) $tx .= '<span> étendu de <strong>'.$search->getArea().'km</strong> </span>';        

        // de niveau débutant 
        if($search->hasLevel()){
            $tx .= '<span> de niveaux <strong>';            
            foreach ($search->getLevelNames() as $key => $level) {
                $tx .= $this->translator->trans('event.level.'.$level).' ';
            }
            $tx .= '</strong></span>';
        }        

        // organisé par 
        if($search->hasType()){
            $tx .= '<span> organisé par des<strong>';
            foreach ($search->getTypeNames() as $key => $type) {
                $tx .= $this->translator->trans('user.types.'.$type,array(),'MyUserBundle').' ';
            }
            $tx .= '</strong></span>';
        }        

        // d'un prix inférieur à 
        if($search->hasPrice()) $tx .= '<span> d\'un prix inférieur à <strong>'.$search->getPrice().'€ </strong></span>';        

        // les lundi mardi jeudi 
        if($search->hasDayOfWeek()){
            $tx .= '<span>qui ont lieu les <strong>';
            foreach ($search->getDayOfWeek() as $key => $day) {
                $tx .= $day.' ';
            }
            $tx .= '</strong></span>';
        }
        
        // entre 10h et 14h 
        if($search->hasTime())  $tx .= '<span> entre <strong>'.$search->getTime('start').'</strong> et <strong>'.$search->getTime('end').'</strong></span>';        
        
        
        
        return $tx;
    }

    public function generateFrequency(Alert $alert)
    {
        $tx = 'Envoi';
        // tout les jours | une fois par semaine 
        if($alert->getFrequency() == 'daily')
            $tx .= '<i> chaque jours </i>';
        elseif($alert->getFrequency() == 'weekly')
            $tx .= '<i> une fois par semaine</i>';        

        // jusqu'au 14 septembre 
        $tx .= '<span> jusqu\'au <i>'.$alert->getDateStop()->format('d/m/Y').'</i></span>';        

        // à l'adresse email 
        $tx .= '<span> à l\'adresse <strong><i>'.$alert->getEmail().'</i></strong></span>';

        return $tx;
    }

    public function getName()
    {
        return 'alert_resume';
    }
}