<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    private const CONFIG_PATH_MAGENTO_URL_PUSHBACK = 'cms/wordpress_integration/magento_url_push_back';

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
            self::CONFIG_PATH_MAGENTO_URL_PUSHBACK,
            ScopeInterface::SCOPE_STORE
        );
    }
}
