<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Http\ApiRoutesEnum;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TinyUrlControllerTest extends TestCase
{
    /**
     * Create a simple url redirection.
     */
    #[Test]
    public function valid_request(): void
    {
        $data = ['url' => $this->faker->url()];
        $response = $this->send_request($data);

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Create an invalid request, which fails Controller validation.
     */
    #[Test]
    public function fails_validation(): void
    {
        $data = ['ul' => $this->faker->url()];
        $response = $this->send_request($data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Create an invalid request body, which fails Controller validation.
     */
    #[Test]
    public function invalid_body(): void
    {
        $data = ['url' => ''];
        $response = $this->send_request($data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Invalidate integration token, which should return an unauthorized response code.
     */
    #[Test]
    public function invalid_integration_token(): void
    {
        config()->set('integrations.tinyurl.token', '');
        $data = ['url' => $this->faker->url()];
        $response = $this->send_request($data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    private function send_request(array $data): TestResponse
    {
        $version = 'v'.config('versioning.major');
        $resource = ApiRoutesEnum::short_urls->value;
        $url = "/api/$version/$resource";
        $response = $this->post($url, $data, [
            'Authorization' => 'Bearer ()',
            'Accept' => 'application/json',
        ]);

        return $response;

    }
}
