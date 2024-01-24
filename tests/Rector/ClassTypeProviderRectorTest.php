<?php

declare(strict_types=1);

namespace zonuexe\TypeProvider\Rector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

#[CoversClass(ClassTypeProviderRector::class)]
final class ClassTypeProviderRectorTest extends AbstractRectorTestCase
{
    #[DataProvider('provideData')]
    public function test(string $fileInfo): void
    {
        $this->doTestFile($fileInfo);
    }

    public static function provideData(): iterable
    {
        return self::yieldFilesFromDirectory(__DIR__ . '/fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/rector.php';
    }
}
