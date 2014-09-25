<?php

namespace Ws\ConvertSQLBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
	public function indexAction()
	{
		return $this->render('WsConvertSQLBundle:Default:index.html.twig');
	}

	public function ConvertOneAction()
	{
		$doctrineEntityName = $this->getRequest()->query->get('table');

		$converter = $this->get('ws_table_converter');

		$converter->importYml(__DIR__.'/../Resources/config/mapping/tables.yml');

		$results = $converter->convertOne($doctrineEntityName);

              if(!empty($results['success'])) $this->get('flashbag')->add("Bravo, ".count($results['success'])." entités créés! ",'success');              
              if(!empty($results['errors'])) $this->get('flashbag')->add("Il y a eu quelques erreurs...",'warning');

		return $this->render('WsConvertSQLBundle:Default:results.html.twig', array('success' => $results['success'],'errors'=> $results['errors']));
	}

    public function ConvertAllAction()
    {
   
       $converter = $this->get('ws_table_converter');

       $converter->importYml(__DIR__.'/../Resources/config/mapping/tables.yml');

       $results = $converter->purge()->convertAll();

        if(!empty($results['success'])) $this->get('flashbag')->add("Bravo, ".count($results['success'])." entités créés! ",'success');              
        if(!empty($results['errors'])) $this->get('flashbag')->add("Il y a eu quelques erreurs...",'warning');

       return $this->render('WsConvertSQLBundle:Default:results.html.twig', array('success' => $results['success'],'errors'=> $results['errors']));

    }

    public function PurgeAction()
    {
    	$converter = $this->get('ws_table_converter');

    	$converter->purge();

      $database = $converter->getPurger()->getObjectManager()->getConnection()->getDatabase();

      $this->get('flashbag')->add('La base de donnée <strong><i>'.$database.'</i></strong> à été purgé','info');

    	return $this->redirect($this->generateUrl('ws_convert_sql_index'));
    }
}
