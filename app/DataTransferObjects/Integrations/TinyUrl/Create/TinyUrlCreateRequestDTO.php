<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Integrations\TinyUrl\Create;

class TinyUrlCreateRequestDTO
{
    public function __construct(
        public string $url,
        public $domain = 'tinyurl.com',
        public $description = ''
    ) {
        //
    }
}
