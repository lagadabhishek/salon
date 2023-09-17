<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Services\Registration\UserRegistrationService;
use Illuminate\Support\Facades\Log;
use App\Http\Services\Utility\ResponseUtility;

class checkHairSalonAuth
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
       try {
            $sessionToken = $request->header('session-token');
            $userRegistrationService = new UserRegistrationService();

            //Session-token validation
            if (empty($sessionToken) || $sessionToken == '')
                return ResponseUtility::respondWithError(40002, null);

            //Dyanamic session-token validation
            $validSessionToken = $userRegistrationService->validateSessionToken($sessionToken);
            if (! $validSessionToken)
                return ResponseUtility::respondUnAuthorized(40001, null);
            
            return $next($request);
        } catch (Exception $e) {
            Log::info("Middleware: checkHairSalonAuth: Got exception in handle function " . PHP_EOL . $e->getMessage());
        }
    }
}
