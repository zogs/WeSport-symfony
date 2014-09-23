<?php

namespace Ws\ConvertSQLBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
   


       $converter = $this->get('ws_table_converter');

       $converter->importYml(__DIR__.'/../Resources/config/mapping/tables.yml');


       $converter->convert();

    }
}
