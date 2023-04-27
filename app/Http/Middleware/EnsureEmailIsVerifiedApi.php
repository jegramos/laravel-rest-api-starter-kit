<?php

namespace App\Http\Middleware;

use App\Enums\ApiErrorCode;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedApi
{
    /**
     * Modified \Illuminate\Auth\Middleware\EnsureEmailIsVerified
     * to have a bit more control on the response
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $noUserFound = ! $request->user();
        $emailNotVerified = $request->user() instanceof MustVerifyEmail && ! $request->user()->hasVerifiedEmail();

        if ($noUserFound || $emailNotVerified) {
            return response()->json([
                'success' => false,
                'message' => 'Email address is not verified',
                'error_code' => ApiErrorCode::EMAIL_NOT_VERIFIED,
                'email' => ! $noUserFound ? $request->user()->email : null,
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
