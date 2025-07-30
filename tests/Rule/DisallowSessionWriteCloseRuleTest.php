<?php

declare(strict_types=1);

namespace Shopware\PhpStan\Tests\Rule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Shopware\PhpStan\Rule\DisallowSessionWriteCloseRule;

/**
 * @extends RuleTestCase<DisallowSessionWriteCloseRule>
 */
class DisallowSessionWriteCloseRuleTest extends RuleTestCase
{
    public function getRule(): Rule
    {
        return new DisallowSessionWriteCloseRule();
    }

    public function testRule(): void
    {
        $this->analyse([
            __DIR__ . '/fixtures/DisallowSessionWriteCloseRule/wrong-usage.php'
        ], [
            [
                'Do not use session_write_close() function in code. Use $request->getSession()->save() instead.',
                3
            ]
        ]);
    }
}
