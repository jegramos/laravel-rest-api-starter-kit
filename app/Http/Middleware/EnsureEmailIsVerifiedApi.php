<?php

namespace App\Http\Middleware;

use App\Enums\ApiErrorCode;
use App\Exceptions\EmailNotVerifiedException;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedApi
{
    /**
     * Modified \Illuminate\Auth\Middleware\EnsureEmailIsVerified
     * to have a bit more control on the response
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws EmailNotVerifiedException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $noUserFound = !$request->user();
        $emailNotVerified = $request->user() instanceof MustVerifyEmail && !$request->user()->hasVerifiedEmail();

        if ($noUserFound || $emailNotVerified) {
            $exception = new EmailNotVerifiedException('Email address not verified');
            $exception->email = $request->user()->email;
            throw $exception;
        }

        return $next($request);
    }
}
