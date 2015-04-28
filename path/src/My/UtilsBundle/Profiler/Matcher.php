<?php

namespace My\UtilsBundle\Profiler;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

/**
 * This class is used by Symfony configuration for displaying Profiler bar in production environnement
 * The matches method must return true for displaying the bar
 */
class Matcher implements RequestMatcherInterface
{
	protected $securityContext;


	public function __construct(SecurityContext $securityContext)
	{
		$this->securityContext = $securityContext;
	}

	public function matches(Request $request)
	{				
		return $this->securityContext->isGranted('ROLE_ADMIN');
	}
}