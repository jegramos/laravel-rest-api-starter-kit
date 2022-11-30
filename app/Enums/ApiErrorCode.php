<?php

namespace App\Enums;

/*
|--------------------------------------------------------------------------
| ApiErrorCode
|--------------------------------------------------------------------------
|
| Provides a list of error codes we can send back to the client
|
*/

enum ApiErrorCode: string
{
    case VALIDATION = 'VALIDATION_ERROR';
    case RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND_ERROR';
    case INVALID_CREDENTIALS = 'INVALID_CREDENTIALS_ERROR';
    case SMTP_ERROR = 'SMTP_ERROR';
    case UNAUTHORIZED = 'UNAUTHORIZED_ERROR';
    case FORBIDDEN = 'FORBIDDEN_ERROR';
    case UNKNOWN_ROUTE = 'UNKNOWN_ROUTE_ERROR';
    case RATE_LIMIT = 'TOO_MANY_REQUESTS_ERROR';
    case DEPENDENCY_ERROR = 'DEPENDENCY_ERROR';
    case SERVER = 'SERVER_ERROR';
    case INCORRECT_OLD_PASSWORD = 'INCORRECT_OLD_PASSWORD_ERROR';
}
