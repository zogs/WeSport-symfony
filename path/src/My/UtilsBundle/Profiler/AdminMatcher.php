<?php

namespace My\UtilsBundle\Profiler;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

/**
 * This class is used by Symfony configuration for displaying Profiler bar in production environnement
 * The matches method must return true for displaying the bar
 */
class AdminMatcher implements RequestMatcherInterface
{
	protected $authorizationChecker;


	public function __construct(AuthorizationCheckerInterface $authorizationChecker)
	{
		$this->authorizationChecker = $authorizationChecker;
	}

	public function matches(Request $request)
	{				
		return $this->authorizationChecker->isGranted('ROLE_ADMIN');
	}
}