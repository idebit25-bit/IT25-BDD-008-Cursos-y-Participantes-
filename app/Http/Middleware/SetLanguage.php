<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class SetLanguage
{
    public function handle($request, Closure $next)
    {
        // Obtener idioma de la sesión o usar predeterminado
        $language = session('language', Config::get('app.locale'));

        // Establecer idioma
        App::setLocale($language);

        return $next($request);
    }
}
