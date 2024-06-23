<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Middleware\EnsureTokenIsValid;
use Arr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EnsureTokenIsValidTest extends TestCase
{
    private array $bracketPairs;

    public function __construct(string $name)
    {
        $this->bracketPairs = ['()', '[]', '{}'];
        parent::__construct($name);
    }

    /**
     * Test that an empty string counts as a valid token.
     */
    public function empty_token(): void
    {
        $token = '';

        $response = $this->middleware_request($token);

        $responseStatus = $response->getStatusCode();

        $response->assertStatus(($responseStatus === Response::HTTP_OK));
    }

    /**
     * Test that a single bracket pair (()[]{}) allows request to continue.
     */
    #[Test]
    public function simple_bracket_pair(): void
    {
        $token = $this->faker->randomElement($this->bracketPairs);

        $response = $this->middleware_request($token);

        $responseStatus = $response->getStatusCode();

        $this->assertTrue(($responseStatus === Response::HTTP_OK));
    }

    /**
     * Test that all pairs concatenated ("()[]{}")allows request to continue.
     */
    #[Test]
    public function multi_bracket_pair(): void
    {
        $shuffledBrackets = $this->faker->shuffle($this->bracketPairs);

        $token = implode('', $shuffledBrackets);

        $response = $this->middleware_request($token);

        $responseStatus = $response->getStatusCode();

        $this->assertTrue(($responseStatus === Response::HTTP_OK));
    }

    /**
     * Check that two differing bracket pairs return Http 401.
     */
    #[Test]
    public function differing_pairs(): void
    {
        $randomizedPairs = $this->faker->randomElements($this->bracketPairs, 2);

        $token = '';

        Arr::map($randomizedPairs, function (string $bracketPair) use (&$token) {
            $token .= Str::substr($bracketPair, $this->faker->numberBetween(0, 1), 1);
        });

        $response = $this->middleware_request($token);

        $responseStatus = $response->getStatusCode();

        $this->assertTrue(($responseStatus === Response::HTTP_UNAUTHORIZED));
    }

    /**
     * Check that two bracket pairs in incorrect order return 401.
     */
    #[Test]
    public function differing_orders(): void
    {
        $randomizedPairs = $this->faker->randomElements($this->bracketPairs, 2);

        $token = $randomizedPairs[0][0].$randomizedPairs[1][0].$randomizedPairs[0][1].$randomizedPairs[1][1];

        $response = $this->middleware_request($token);

        $responseStatus = $response->getStatusCode();

        $this->assertTrue(($responseStatus === Response::HTTP_UNAUTHORIZED));
    }

    /**
     * Check that any bracket type incorrectly paired returns 401.
     */
    #[Test]
    public function non_closing_pairs(): void
    {
        $randomPair = $this->faker->randomElement($this->bracketPairs);

        $subElement = $randomPair[$this->faker->numberBetween(0, 1)];

        $token = $this->faker->regexify("/\\$subElement{1,9}/");

        $splitString = str_split($token);

        array_splice($splitString, $this->faker->numberBetween(0, count($splitString) - 1), 0, $randomPair);

        $token = implode('', $splitString);

        $response = $this->middleware_request($token);

        $responseStatus = $response->getStatusCode();

        $this->assertTrue(($responseStatus === Response::HTTP_UNAUTHORIZED));
    }

    /**
     * Check that any non valid character mixed with valid token returns 401.
     */
    #[Test]
    public function mixed_non_valid_chars(): void
    {
        $randomPair = $this->faker->randomElement($this->bracketPairs);

        $splitString = str_split($randomPair);

        array_splice($splitString, $this->faker->numberBetween(0, count($splitString) - 1), 0, $this->faker->regexify('/[^\(\)\{\}\[\]]{1,9}/'));

        $token = implode('', $splitString);

        $response = $this->middleware_request($token);

        $responseStatus = $response->getStatusCode();

        $this->assertTrue(($responseStatus === Response::HTTP_UNAUTHORIZED));
    }

    /**
     * Check that using any Authorization token prefix other than Bearer is invalid.
     */
    public function non_bearer_token_type(): void
    {
        $token = $this->faker->randomElement($this->bracketPairs);

        $prefix = $this->faker->word();

        $response = $this->middleware_request($token, $prefix);

        $responseStatus = $response->getStatusCode();

        $this->assertTrue(($responseStatus === Response::HTTP_UNAUTHORIZED));
    }

    private function middleware_request(?string $token, string $prefix = 'Bearer'): Response
    {
        $request = new Request();

        $request->headers->set('Authorization', "$prefix $token");
        $request->merge(['url' => 'http://example.com/'.$this->faker->regexify('/[A-Za-z0-9]{9}/')]);

        $middleware = new EnsureTokenIsValid();

        $response = $middleware->handle(
            $request,
            function ($request) {
                return new JsonResponse();

            }
        );

        return $response;
    }
}
