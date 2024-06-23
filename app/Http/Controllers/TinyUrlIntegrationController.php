<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DataTransferObjects\Integrations\TinyUrl\Create\TinyUrlCreateRequestDTO;
use App\Http\IntegrationFacades\TinyUrlIntegrationFacade;
use App\Http\Requests\TinyUrlCreateRequest;
use Illuminate\Http\JsonResponse;

class TinyUrlIntegrationController extends Controller
{
    /**
     * Create a new shortened URL.
     */
    public function create(
        TinyUrlCreateRequest $request
    ): JsonResponse {
        $integrationRequestData = new TinyUrlCreateRequestDTO(
            $request->validated('url')
        );

        return TinyUrlIntegrationFacade::create(
            $integrationRequestData
        );
    }
}
