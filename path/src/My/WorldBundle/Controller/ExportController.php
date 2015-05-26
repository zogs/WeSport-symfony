<?php

namespace My\WorldBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use My\WorldBundle\Entity\City;
use My\WorldBundle\Entity\Location;

class ExportController extends Controller
{
 
    public function indexAction(Request $request)
    {
        $file_url = false;

        $form = $this->createFormBuilder()
            ->add('countries','entity',array(
                    'multiple'=> true,
                    'expanded'=> true,
                    'class' => 'MyWorldBundle:Country',
                    'property' => 'name',
                    'mapped'=> true,
                    'label' => "Select countries you want to export:",
                ))
            ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {

            $countries = $form->get('countries')->getData();

            $file_url = $this->container->get('world.exporter.sql')->exportCountries($countries);

        }

        return $this->render('MyWorldBundle:Export:index.html.twig',array(
            'form'=>$form->createView(),
            'file_url' => $file_url,

            ));
    }

}
