<?php

declare(strict_types=1);

namespace Shopware\PhpStan\Tests\Rule\fixtures\ForwardSalesChannelContextToSystemConfigServiceRule;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class CorrectUsage
{
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function correct(SalesChannelContext $context): void
    {
        $this->systemConfigService->get('foo.bar', $context->getSalesChannelId());
        $this->systemConfigService->getString('foo.bar', $context->getSalesChannel()->getId());
        $this->systemConfigService->getInt('foo.bar', $context->getSalesChannelId());
        $this->systemConfigService->getFloat('foo.bar', $context->getSalesChannelId());
        $this->systemConfigService->getBool('foo.bar', $context->getSalesChannelId());
    }

    public function correctWithoutContext(): void
    {
        $this->systemConfigService->get('foo.bar');
    }
}
