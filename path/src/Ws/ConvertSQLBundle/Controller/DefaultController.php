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

		return $this->render('WsConvertSQLBundle:Default:results.html.twig', array('success' => $results['success'],'errors'=> $results['errors']));
	}

    public function ConvertAllAction()
    {
   
       $converter = $this->get('ws_table_converter');

       $converter->importYml(__DIR__.'/../Resources/config/mapping/tables.yml');

       $results = $converter->purge()->convertAll();

       return $this->render('WsConvertSQLBundle:Default:results.html.twig', array('success' => $results['success'],'errors'=> $results['errors']));

    }

    public function PurgeAction()
    {
    	$converter = $this->get('ws_table_converter');

    	$converter->purge();

    	$this->redirect($this->generateUrl('ws_convert_sql_index'));
    }
}
