<?php

namespace My\BlogBundle\Controller;

use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Export\CSVExport;
use APY\DataGridBundle\Grid\Export\ExcelExport;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Admin controller.
 *
 */
class AdminController extends Controller
{
    /**
     * Affiche la liste des Articles
     *
     */
    public function indexAction()
    {
        // Initialisation de la source de données
        $source = new Entity('MyBlogBundle:Article');

        // Récupération du service Grid
        $grid = $this->container->get('grid');

        // Affectation de la source
        $grid->setSource($source);
        
        //Changer la colonne URl en colonne Image (<img> not work)
        $source->manipulateRow(
            function ($row) {
                $row->setField('image', $row->getEntity()->getUrl());
                return $row;
            }
        );

        $rowAction = new RowAction("Voir", 'blog_voir');
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction("Editer", 'blog_editer');
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction("Supprimer", 'blog_supprimer', true, '_self');
        $rowAction->setConfirmMessage('Etes vous sûr de vouloir supprimer cet article ?');
        $grid->addRowAction($rowAction);

        $grid->addExport(new CSVExport('Exporter au format CSV'));
        $grid->addExport(new ExcelExport('Exporter au format Excel'));

        // Set the limits
		$grid->setLimits(array(1, 2, 5));

		// Set the default limit
		$grid->setDefaultLimit(2);

        // Renvoie une réponse
        return $grid->getGridResponse('MyBlogBundle:Admin:article_index.html.twig');
    }

}
?>