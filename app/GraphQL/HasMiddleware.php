<?php

namespace App\GraphQL;

use Illuminate\Http\JsonResponse;

trait HasMiddleware
{
    /**
     * [handleMiddleware description]
     * @param  [type] $middlewares [description]
     * @return [type]              [description]
     */
    public function handleMiddleware($middlewares = null)
    {
        if (env('SKIP_GRAPHQL_MIDDLEWARE', false)) {
            return;
        }
        $appMiddleware = app()->router->getMiddleware();
        if ($middlewares === null) {
            $middlewares = $this->middleware;
        }
        foreach ($middlewares as $middleware) {
            $middleware = explode(':', $middleware);
            $nameMiddleware = array_shift($middleware);
            $guard = array_shift($middleware);
            if (!empty($middleware)) {
                $guard = array_shift($middleware);
            }

            if (isset($appMiddleware[$nameMiddleware])) {
                $next = app($appMiddleware[$nameMiddleware])->handle(request(), function () {
                }, $guard);
                if ($next instanceof JsonResponse) {
                    abort($next->getData()->status . '', $next->getData()->error . ' - ' . $next->getData()->error_message);
                }
            }
        }
    }
}