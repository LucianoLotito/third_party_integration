<?php

declare(strict_types=1);

namespace App\Http\IntegrationFacades;

use App\DataTransferObjects\Integrations\TinyUrl\Create\TinyUrlCreateRequestDTO;
use App\DataTransferObjects\Integrations\TinyUrl\Create\TinyUrlCreateResponseDTO;
use App\DataTransferObjects\Integrations\TinyUrl\TinyUrlBaseResponseDTO;
use App\Enums\Http\HttpMethodsEnum;
use App\Enums\Integrations\TinyUrl\TinyUrlResourceEnum;
use Arr;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TinyUrlIntegrationFacade
{
    /**
     * Create a new url redirection. Makes POST call to /create resource.
     *
     * @param  TinyUrlCreateRequestDTO  $data  [url => string]
     */
    public static function create(
        TinyUrlCreateRequestDTO $data
    ): JsonResponse {
        $request = self::request(HttpMethodsEnum::POST, TinyUrlResourceEnum::create, $data);

        return self::handle_response($request);
    }

    /**
     * Send a standardized request to the Tinyurl API by passing the resource and data.
     *
     * @param  array  $body
     */
    private static function request(
        HttpMethodsEnum $httpMethod,
        TinyUrlResourceEnum $resource,
        TinyUrlCreateRequestDTO $data
    ): ClientResponse {
        try {
            $url = config('integrations.tinyurl.url');
            $token = config('integrations.tinyurl.token');

            $headers = [
                'Authorization' => "Bearer $token",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];

            $url = "$url/$resource->name";

            $integrationResponse = Http::withHeaders($headers)->{$httpMethod->name}($url, $data);

            return $integrationResponse;
        } catch (Throwable $throwable) {
            Log::error($throwable->getMessage(), $throwable->getTrace());

            return response()->json(
                literal(
                    message: Arr::get(
                        Response::$statusTexts,
                        Response::HTTP_SERVICE_UNAVAILABLE
                    )
                ),
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

    }

    private static function handle_response(
        ClientResponse $response
    ): JsonResponse {
        try {
            $dto = new TinyUrlBaseResponseDTO(
                null,
                Arr::get($response, 'code'),
                Arr::get($response, 'errors')
            );
            switch ($response->status()) {
                case Response::HTTP_OK:
                    $dtoInnerData = new TinyUrlCreateResponseDTO(Arr::get($response, 'data'));
                    $dto->data = $dtoInnerData;

                    return response()->json(literal(url: "<{$dto->data->tinyUrl}>"), Response::HTTP_OK);
                default:
                    Log::error(implode('|', $dto->errors), ['TinyDto integration']);

                    return response()->json(literal(message: $dto->errors), $response->status());

            }
        } catch (Throwable $throwable) {
            Log::error($throwable->getMessage(), $throwable->getTrace());

            return response()->json(
                literal(
                    message: Arr::get(
                        Response::$statusTexts,
                        Response::HTTP_SERVICE_UNAVAILABLE
                    )
                ),
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }
    }
}
