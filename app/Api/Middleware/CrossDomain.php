<?php

declare(strict_types=1);

namespace App\Api\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class CrossDomain
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (! $response instanceof Response) {
            return $response;
        }

        $response->headers->set('Access-Control-Allow-Credentials', $this->getCredentials());
        $response->headers->set('Access-Control-Allow-Origin', $this->getOrigin($request));
        $response->headers->set('Access-Control-Allow-Methods', '*');
        $response->headers->set('Access-Control-Allow-Headers', '*');

        return $response;
    }

    /**
     * Get credentials.
     *
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function getCredentials(): string
    {
        $credentials = $this->config('http.cros.credentials', false);

        return $credentials ? 'true' : 'false';
    }

    /**
     * Get origin.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function getOrigin($request): string
    {
        $origin = $this->config('http.cros.origin', '*');
        if ($origin === '*') {
            return '*';
        }

        $requestOrigin = $request->headers->get('origin');
        if (in_array($requestOrigin, (array) $origin)) {
            return $requestOrigin;
        }

        return '';
    }

    /**
     * Get config function alise.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function config(string $key, $default)
    {
        return config($key, $default);
    }
}
