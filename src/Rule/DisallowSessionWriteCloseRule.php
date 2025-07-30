<?php

declare(strict_types=1);

namespace Shopware\PhpStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<FuncCall>
 */
class DisallowSessionWriteCloseRule implements Rule
{
    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * @return array<array-key, RuleError|string>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Node\Name) {
            return [];
        }

        $name = $node->name->toString();

        if ($name === 'session_write_close') {
            return [
                RuleErrorBuilder::message('Do not use session_write_close() function in code. Use $request->getSession()->save() instead.')
                    ->line($node->getLine())
                    ->identifier('shopware.disallowSessionWriteClose')
                    ->build(),
            ];
        }

        return [];
    }
}
