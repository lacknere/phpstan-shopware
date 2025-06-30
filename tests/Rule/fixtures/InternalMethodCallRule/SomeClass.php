<?php

declare(strict_types=1);

namespace Shopware\PhpStan\Tests\Rule\fixtures\InternalMethodCallRule;

class SomeClass
{
    /**
     * @internal
     */
    public function internalMethod(): void {}
}
