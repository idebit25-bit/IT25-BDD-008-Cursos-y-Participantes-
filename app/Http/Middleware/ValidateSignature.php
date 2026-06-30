<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ValidateSignature as BaseMiddleware;
use Closure;

class ValidateSignature extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$args
     * @return mixed
     */
    public function handle($request, Closure $next, ...$args)
    {
        // Usa el método de la clase base para validar la firma
        return parent::handle($request, $next, ...$args);
    }
}
