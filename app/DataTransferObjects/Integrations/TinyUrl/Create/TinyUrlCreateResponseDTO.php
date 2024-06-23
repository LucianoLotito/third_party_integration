<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Integrations\TinyUrl\Create;

use Arr;
use Carbon\Carbon;
use InvalidArgumentException;
use Log;
use Throwable;

class TinyUrlCreateResponseDTO
{
    /**
     * Base domain for the aliased url.
     */
    public string $domain;

    /**
     * Shorthand alias for this url.
     */
    public string $alias;

    /**
     * Is this url deleted.
     */
    public bool $deleted;

    /**
     * Is this url archived.
     */
    public bool $archived;

    /**
     * Tags for grouping multiple urls.
     *
     * @var string[]
     */
    public array $tags;

    /**
     * Analytics results for this specific url usage.
     */
    public array $analytics;

    /**
     * Aliased TinyUrl url.
     */
    public string $tinyUrl;

    /**
     * Date of creation.
     */
    public Carbon $createdAt;

    /**
     * Url to be redirected to.
     */
    public string $url;

    public function __construct(array $data)
    {
        try {
            $validateStructure = Arr::has(
                $data,
                [
                    'domain',
                    'alias',
                    'deleted',
                    'archived',
                    'tags',
                    'analytics',
                    'tiny_url',
                    'created_at',
                    'expires_at',
                    'url',
                ]
            );

            if (! $validateStructure) {
                throw new InvalidArgumentException('Missing arguments for class creation');
            }

            $this->domain = Arr::get($data, 'domain');
            $this->alias = Arr::get($data, 'alias');
            $this->deleted = Arr::get($data, 'deleted');
            $this->archived = Arr::get($data, 'archived');
            $this->tags = Arr::get($data, 'tags');
            $this->analytics = Arr::get($data, 'analytics');
            $this->tinyUrl = Arr::get($data, 'tiny_url');
            $this->createdAt = Carbon::createFromTimeString(Arr::get($data, 'created_at'));
            $this->url = Arr::get($data, 'url');
        } catch (Throwable $throwable) {
            Log::error($throwable->getMessage(), $throwable->getTrace());
            throw $throwable;
        }
    }
}
