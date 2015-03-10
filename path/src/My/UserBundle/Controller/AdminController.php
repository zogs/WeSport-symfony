<?php

namespace My\UserBundle\Controller;

use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Export\CSVExport;
use APY\DataGridBundle\Grid\Export\ExcelExport;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use My\UserBundle\Entity\User;

/**
 * Admin controller.
 *
 */
class AdminController extends Controller
{
    /**
     * Affiche la liste des Articles
     *
     
    public function indexAction()
    {
        // Initialisation de la source de données
        $source = new Entity('MyUserBundle:User');

        // Récupération du service Grid
        $grid = $this->container->get('grid');

        // Affectation de la source
        $grid->setSource($source);
        

        // Set the limits
		$grid->setLimits(array(1, 2, 5));

		// Set the default limit
		$grid->setDefaultLimit(2);

        // Renvoie une réponse
        return $grid->getGridResponse('MyUserBundle:Admin:user_index.html.twig');
    }
    */

    public function switchToUserAction(User $user)
    {
        if( ! $this->container->get('security.context')->isGranted('ROLE_ALLOWED_TO_SWITCH')) throw new AccessDeniedException();
        
        return $this->redirect($this->generateUrl('fos_user_profile_edit',array('_switch_user'=>$user->getUsername())));
    }

}
?>