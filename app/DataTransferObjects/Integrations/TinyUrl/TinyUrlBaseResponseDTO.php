<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Integrations\TinyUrl;

use App\DataTransferObjects\Integrations\TinyUrl\Create\TinyUrlCreateResponseDTO;

class TinyUrlBaseResponseDTO
{
    public function __construct(
        public ?TinyUrlCreateResponseDTO $data,
        public int $code,
        public ?array $errors
    ) {
        //
    }
}
