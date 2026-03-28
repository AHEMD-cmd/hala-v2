<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasHeader('X-Livewire')) {
            return $next($request);
        }

        if ($request->has('locale')) {
            $locale = $request->get('locale');
            if (in_array($locale, ['en', 'ar', 'nl'])) {
                Session::put('locale', $locale);
                App::setLocale($locale);
            }
        } elseif (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }

        return $next($request);
    }
}
