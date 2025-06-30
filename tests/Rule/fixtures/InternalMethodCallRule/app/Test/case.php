<?php

declare(strict_types=1);

namespace Test\App;

use Shopware\PhpStan\Tests\Rule\fixtures\InternalMethodCallRule\SomeClass;

function test()
{
    (new SomeClass())->internalMethod();
}
