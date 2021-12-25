<?php

namespace App\Http\Middleware;

use App\Contracts\LycheeException;
use App\Http\Middleware\Checks\IsInstalled;
use App\Redirections\ToHome;
use App\Redirections\ToInstall;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Class InstallationStatus.
 *
 * This middleware ensures that the installation has the required status.
 * If the installation has the required status, then the request passes
 * unchanged.
 * If the required status equals `:complete` but the installation is
 * incomplete, then the client is redirected to the installation pages.
 * If the required status equals `:incomplete` but the installation is
 * complete, then the client is redirected to the home page.
 * The latter mode is supposed to be used as a gatekeeper to the installation
 * pages and to prevent access if no installation is required.
 */
class InstallationStatus
{
	public const COMPLETE = 'complete';
	public const INCOMPLETE = 'incomplete';

	private IsInstalled $isInstalled;

	public function __construct(IsInstalled $isInstalled)
	{
		$this->isInstalled = $isInstalled;
	}

	/**
	 * Handles an incoming request.
	 *
	 * @param Request $request        the incoming request to serve
	 * @param Closure $next           the next operation to be applied to the
	 *                                request
	 * @param string  $requiredStatus the required installation status; either
	 *                                {@link self::COMPLETE} or
	 *                                {@link self::INCOMPLETE}
	 *
	 * @return mixed
	 *
	 * @throws RouteNotFoundException
	 * @throws LycheeException
	 */
	public function handle(Request $request, Closure $next, string $requiredStatus): mixed
	{
		if ($requiredStatus === self::COMPLETE && !$this->isInstalled->assert()) {
			return ToInstall::go();
		} elseif ($requiredStatus === self::INCOMPLETE && $this->isInstalled->assert()) {
			return ToHome::go();
		} else {
			return $next($request);
		}
	}
}