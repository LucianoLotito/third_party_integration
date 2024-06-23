<?php

namespace App\Http\Middleware;

use Arr;
use Closure;
use Illuminate\Http\Request;
use Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * A valid token is any token which has n number of bracket pairs opened and closed respectively.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authorizationHeader = $request->header('Authorization');

        // Validate that we actually have an Auhtorization Bearer token structure.
        $validateBearer = Str::match('/Bearer [\(\)\{\}\[\]]*/', $authorizationHeader);

        if (Str::length($validateBearer) !== Str::length($authorizationHeader)) {
            return response()->json(
                literal(
                    message: Arr::get(
                        Response::$statusTexts,
                        Response::HTTP_UNAUTHORIZED
                    )
                ),
                Response::HTTP_UNAUTHORIZED
            );
        }

        $token = Str::remove('Bearer ', $authorizationHeader);

        $previousToken = '';

        // Keep removing valid matches until both previous and current token are equal, which means no more pair matches.
        while ($token != $previousToken) {
            $previousToken = $token;
            $token = Str::replaceMatches('/\[\]|\(\)|{}/', '', $token);

        }

        // If the final token has any lenght, means that there was some mismatched bracket.
        if (Str::length($token) > 0) {
            return response()->json(
                literal(
                    message: Arr::get(
                        Response::$statusTexts,
                        Response::HTTP_UNAUTHORIZED
                    )
                ),
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $next($request);
    }
}
