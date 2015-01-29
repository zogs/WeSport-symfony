<?php

namespace My\UserBundle\Service;
 
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
 
class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
	protected $router;

	public function __construct(Router $router)
	{
		$this->router = $router;
	}

	public function onLogoutSuccess(Request $request)
	{
		$response = new RedirectResponse($this->router->generate('ws_calendar'));	
		return $response;
	}
}