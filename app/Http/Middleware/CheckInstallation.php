<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SiteSetting;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if application is already installed
        $installed = SiteSetting::get('app_installed', false);

        if ($installed && !$request->is('update*')) {
            // If installed and not accessing update, redirect to home
            return redirect('/');
        }

        if (!$installed && $request->is('update*')) {
            // If not installed and trying to access update, redirect to install
            return redirect('/install');
        }

        return $next($request);
    }
}
