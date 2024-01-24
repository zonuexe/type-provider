<?php

declare(strict_types=1);

namespace zonuexe\TypeProvider\Rector;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Class_;
use Rector\Rector\AbstractRector;
use ReflectionClass;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use zonuexe\TypeProvider\ProviderInterface;
use function array_map;
use function implode;
use function is_a;

/**
 * @phpstan-import-type types from ProviderInterface
 */
class ClassTypeProviderRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }

        $typeProvider = null;
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                $name = implode('\\', $attr->name->getParts());
                if (is_a($name, ProviderInterface::class, true)) {
                    $typeProvider = $this->hydrate($name, $attr->args);
                    break 2;
                }
            }
        }

        if ($typeProvider === null) {
            return null;
        }

        $modified = false;
        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $stmt->name->name === '__construct') {
                $stmt->params = $this->buildParams($typeProvider->toTypes());
                $modified = true;
            }
        }

        return $modified ? $node : null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Inject construct promotions that typed from TypeProvider', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use zonuexe\TypeProvider\Json;

#[Json('{"name": "Perfect PHP", "publisher": "Gijutsu-Hyoron Co., Ltd."}')]
final readonly class Book
{
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use zonuexe\TypeProvider\Json;

#[Json('{"name": "Perfect PHP", "publisher": "Gijutsu-Hyoron Co., Ltd."}')]
final readonly class Book
{
    public function __construct(
        public readonly string $name,
        public readonly string $publisher,
    ) {
    }
}
CODE_SAMPLE,
            ),
        ]);
    }

    /**
     * @param class-string<ProviderInterface> $name
     * @param array<Arg> $args
     */
    private function hydrate(string $name, array $args): ProviderInterface
    {
        $ref = new ReflectionClass($name);
        $paramPositions = array_map(
            fn ($param) => $param->getName(),
            $ref->getConstructor()?->getParameters() ?? []
        );

        $attrArgs = [];
        $i = 0;
        foreach ($args as $arg) {
            if (!$arg->value instanceof String_) {
                continue;
            }

            if ($arg->name === null) {
                $paramName = $paramPositions[$i++];
            } else {
                $paramName = $arg->name->name;
            }

            $attrArgs[$paramName] = $arg->value->value;
        }

        return new $name(...$attrArgs);
    }

    /**
     * @phpstan-param types $types
     * @return list<Param>
     */
    private function buildParams(array $types): array
    {
        $params = [];

        foreach ($types as $name => $type) {
            $nativeType = $type[0];
            $params[] = new Param(
                new Variable($name),
                type: new Identifier($nativeType),
                flags: Modifiers::PUBLIC,
            );
        }

        return $params;
    }
}
