<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\WhiteLabel;
use Illuminate\Support\Facades\Session;

class RestrictLoginByWhiteLabel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        
        $domain = $request->getHost();
        
        $whiteLabel = WhiteLabel::where('domain', $domain)->first();
        if ($whiteLabel && Auth::check() && Auth::user()->white_label_id == $whiteLabel->id) {
            return $next($request);
        }

        if (Auth::check() && Auth::user()->white_label_id === null) {
            return $next($request);
        }
        
        Session::flush();
        return redirect()->back()->withErrors(['email' => "Invalid white label."]);
    }
}
