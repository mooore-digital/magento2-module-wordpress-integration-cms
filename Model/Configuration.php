<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Configuration
{
    const CONFIG_PATH_BASEURLS = 'cms/wordpress_integration/baseurls';
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getBaseUrls(): array
    {
        return explode(
            "\n",
            $this->scopeConfig->getValue(self::CONFIG_PATH_BASEURLS, ScopeInterface::SCOPE_STORE)
        ) ?: [];
    }
}
