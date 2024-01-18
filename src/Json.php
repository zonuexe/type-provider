<?php

declare(strict_types=1);

namespace zonuexe\TypeProvider;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Json implements ProviderInterface
{
    use Utils\JsonUtil;

    public function __construct(
        private readonly string $json,
    ) {
    }

    public function toTypes(): array
    {
        return $this->parseJson($this->json);
    }
}
