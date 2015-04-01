<?php

namespace Ws\ConvertSQLBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ConvertController extends Controller
{
	public function indexAction()
	{
		return $this->render('WsConvertSQLBundle:Default:index.html.twig');
	}

	public function ConvertOneAction()
	{
		$doctrineEntityName = $this->getRequest()->query->get('table');

		$converter = $this->get('ws_table_converter');

		$converter->importYml(__DIR__.'/../Resources/config/tables.yml');

		$results = $converter->convertOne($doctrineEntityName);

              if(!empty($results['success'])) $this->get('flashbag')->add("Bravo, ".count($results['success'])." entités créés! ",'success');              
              if(!empty($results['errors'])) $this->get('flashbag')->add("Il y a eu ".count($results['errors'])." erreurs...",'warning');

		return $this->render('WsConvertSQLBundle:Default:results.html.twig', array('success' => $results['success'],'errors'=> $results['errors']));
	}

    public function ConvertAllAction()
    {
   
       $converter = $this->get('ws_table_converter');
       $purger = $this->get('ws_table_purger');

       $purger->purge();

       $converter->importYml(__DIR__.'/../Resources/config/tables.yml');
       $results = $converter->convertAll();

        if(!empty($results['success'])) $this->get('flashbag')->add("Bravo, ".count($results['success'])." entités créés! ",'success');              
        if(!empty($results['errors'])) $this->get('flashbag')->add("Il y a eu ".count($results['errors'])." erreurs...",'warning');

       return $this->render('WsConvertSQLBundle:Default:results.html.twig', array('success' => $results['success'],'errors'=> $results['errors']));

    }

    public function PurgeAction()
    {
      $purger = $this->get('ws_table_purger');
      $purger->purge();

      $this->get('flashbag')->add('La base de donnée <strong><i>'.$purger->getDatabase().'</i></strong> à été purgé','success');

    	return $this->redirect($this->generateUrl('ws_convert_sql_index'));
    }


}
