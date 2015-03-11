<?php


namespace My\UserBundle\Service;
 
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;

use My\FlashBundle\Controller\FlashController as Flashbag;
 
class OAuthSuccessHandler implements AuthenticationSuccessHandlerInterface
{
	protected $router;
	protected $security;
	protected $flashbag;

	public function __construct(Router $router, SecurityContext $security, Flashbag $flashbag)
	{
		$this->router = $router;
		$this->security = $security;
		$this->flashbag = $flashbag;
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token)
	{
		$user = $token->getUser();

		if(null != $user->getFacebookId()){
			$this->flashbag->add('Vous êtes maintenant connecté grace à Facebook !');
		}

		if ($this->security->isGranted('ROLE_USER')){

			if($referer_url = $request->request->get('_target_path')){
				
				$response = new RedirectResponse($referer_url);
			}
			else {
				$response = new RedirectResponse($this->router->generate('ws_calendar'));
			}			
		}

		return $response;
	}
}