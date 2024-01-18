<?php

declare(strict_types=1);

namespace zonuexe\TypeProvider\Utils;

use Closure;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(JsonUtil::class)]
class JsonUtilTest extends TestCase
{
    /**
     * @param array{0: string, 1?: string} $expected
     */
    #[DataProvider('jsonProvider')]
    public function test(string $json, array $expected): void
    {
        $subject = new class () {
            use JsonUtil {
                JsonUtil::parseJson as _parseJson;
            }

            /**
             * @return array<string, array{0: string, 1?: string}>
             */
            public function parseJson(string $json): array
            {
                return $this->_parseJson($json);
            }
        };

        $this->assertEquals($expected, $subject->parseJson($json));
    }

    /**
     * @return iterable<array{string, array<string, array{0: string, 1?: string}>}>
     */
    public static function jsonProvider(): iterable
    {
        yield [
            '{"a": 1}',
            [
                'a' => ['int'],
            ],
        ];

        yield [
            '{"a": 1, "b": false}',
            [
                'a' => ['int'],
                'b' => ['bool'],
            ],
        ];

        yield [
            '{"a": 1, "b": false, "c": [1, 2, 3]}',
            [
                'a' => ['int'],
                'b' => ['bool'],
                'c' => ['array', 'list<int>'],
            ],
        ];

        yield [
            '{"a": 1, "b": false, "c": [1, false, "foo"]}',
            [
                'a' => ['int'],
                'b' => ['bool'],
                'c' => ['array', 'array{0: int, 1: bool, 2: string}'],
            ],
        ];
    }
}
