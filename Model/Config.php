<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    private const MAGENTO_URL_PUSHBACK = 'cms/wordpress_integration/magentoUrlPushBack';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function magentoUrlPushBackEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::MAGENTO_URL_PUSHBACK,
            ScopeInterface::SCOPE_STORE
        );
    }
}
