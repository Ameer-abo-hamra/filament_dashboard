<?php

namespace App\Http\Middleware;

use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth ;
class IsActive
{
    use ResponseTrait ;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

      $sub =Auth::guard('sub')->user();

            if ($sub->is_active && $sub->is_verified) {

                return $next($request);
            }
            else {
                return $this->returnError("You must verify your account first ");
            }
    }
}
