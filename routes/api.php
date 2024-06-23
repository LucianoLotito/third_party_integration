<?php

declare(strict_types=1);

use App\Enums\Http\ApiRoutesEnum;
use App\Http\Controllers\TinyUrlIntegrationController;
use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'v'.config('versioning.major'),
    'middleware' => EnsureTokenIsValid::class,
], function () {
    Route::post(ApiRoutesEnum::short_urls->value, [TinyUrlIntegrationController::class, 'create']);
});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
