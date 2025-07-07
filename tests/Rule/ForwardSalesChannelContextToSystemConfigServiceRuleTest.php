<?php

declare(strict_types=1);

namespace Shopware\PhpStan\Tests\Rule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Shopware\PhpStan\Rule\ForwardSalesChannelContextToSystemConfigServiceRule;

/**
 * @extends RuleTestCase<ForwardSalesChannelContextToSystemConfigServiceRule>
 */
class ForwardSalesChannelContextToSystemConfigServiceRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/fixtures/ForwardSalesChannelContextToSystemConfigServiceRule/correct-usage.php'], []);
        $this->analyse([__DIR__ . '/fixtures/ForwardSalesChannelContextToSystemConfigServiceRule/wrong-usage.php'], [
            [
                'SystemConfigService methods expects a salesChannelId as second parameter. When a method gets a SalesChannelContext passed and that parameter is not forwarded to SystemConfigService we should throw an phpstan error',
                21,
            ],
            [
                'SystemConfigService methods expects a salesChannelId as second parameter. When a method gets a SalesChannelContext passed and that parameter is not forwarded to SystemConfigService we should throw an phpstan error',
                22,
            ],
            [
                'SystemConfigService methods expects a salesChannelId as second parameter. When a method gets a SalesChannelContext passed and that parameter is not forwarded to SystemConfigService we should throw an phpstan error',
                23,
            ],
            [
                'SystemConfigService methods expects a salesChannelId as second parameter. When a method gets a SalesChannelContext passed and that parameter is not forwarded to SystemConfigService we should throw an phpstan error',
                24,
            ],
            [
                'SystemConfigService methods expects a salesChannelId as second parameter. When a method gets a SalesChannelContext passed and that parameter is not forwarded to SystemConfigService we should throw an phpstan error',
                25,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new ForwardSalesChannelContextToSystemConfigServiceRule($this->createReflectionProvider());
    }
}
