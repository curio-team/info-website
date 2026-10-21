<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateTest
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->hasValidCredentials($request)) {
            return response('Unauthorized', Response::HTTP_UNAUTHORIZED, [
                'WWW-Authenticate' => 'Basic realm="Access denied"',
                'Cache-Control' => 'no-cache, must-revalidate, max-age=0',
            ]);
        }

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-cache, must-revalidate, max-age=0');

        return $response;
    }

    /**
     * Fails closed: when no user or password is configured, nobody is let in.
     */
    private function hasValidCredentials(Request $request): bool
    {
        $authUser = (string) config('app.site_testing.access_user');
        $authPassword = (string) config('app.site_testing.access_password');

        if ($authUser === '' || $authPassword === '') {
            return false;
        }

        $suppliedUser = (string) $request->getUser();
        $suppliedPassword = (string) $request->getPassword();

        $userMatches = hash_equals($authUser, $suppliedUser);
        $passwordMatches = hash_equals($authPassword, $suppliedPassword);

        return $userMatches && $passwordMatches;
    }
}
