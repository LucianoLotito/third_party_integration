<?php

declare(strict_types=1);

namespace App\Enums\Http;

enum HttpMethodsEnum
{
    case GET;
    case POST;
    case PUT;
    case DELETE;
    case PATCH;
    case OPTIONS;
    case HEAD;
}
