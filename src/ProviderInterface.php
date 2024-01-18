<?php

declare(strict_types=1);

namespace zonuexe\TypeProvider;

/**
 * @phpstan-type types array<string, array{0: string, 1?: string}>
 */
interface ProviderInterface
{
    /** @phpstan-return types */
    public function toTypes(): array;
}
