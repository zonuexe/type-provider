<?php

declare(strict_types=1);

namespace zonuexe\TypeProvider\Utils;

use DomainException;
use function array_flip;
use function array_is_list;
use function array_key_first;
use function array_keys;
use function count;
use function implode;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function json_decode;
use const JSON_THROW_ON_ERROR;

trait JsonUtil
{
    /**
     * @return array<string, array{0: string, 1?: string}>
     */
    private function parseJson(string $json): array
    {
        $data = json_decode($json, true, JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new DomainException('Type-provider currently only supports objects.');
        }

        if (array_is_list($data)) {
            throw new DomainException('Type-provider does not currently support lists');
        }

        $types = [];

        foreach ($data as $name => $element) {
            $types[$name] = match (true) {
                is_string($element) => ['string'],
                is_float($element) => ['float'],
                is_int($element) => ['int'],
                is_bool($element) => ['bool'],
                is_array($element) => ['array', $this->parseElements($element)],
                default => throw new DomainException('Unexpected type'),
            };
        }

        return $types;
    }

    /**
     * @param array<mixed> $elements
     */
    private function parseElements(array $elements): string
    {
        $types = [];
        foreach ($elements as $name => $element) {
            $types[$name] = match (true) {
                is_string($element) => 'string',
                is_float($element) => 'float',
                is_int($element) => 'int',
                is_bool($element) => 'bool',
                is_array($element) => $this->parseElements($element),
                default => throw new DomainException('Unexpected type'),
            };
        }

        if (array_is_list($types) && count(array_flip($types)) === 1) {
            return "list<{$types[array_key_first($types)]}>";
        }

        return 'array{' . implode(
            ', ',
            array_map(fn ($name, $type) => "{$name}: {$type}", array_keys($types), $types),
        ) . '}';
    }
}
