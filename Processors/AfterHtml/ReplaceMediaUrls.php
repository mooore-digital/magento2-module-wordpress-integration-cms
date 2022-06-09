<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Processors\AfterHtml;

use Magento\Cms\Model\Page;
use Magento\Store\Model\StoreManagerInterface;
use Mooore\WordpressIntegration\Api\SiteRepositoryInterface;

class ReplaceMediaUrls
{

    /**
     * @var SiteRepositoryInterface
     */
    private $siteRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        SiteRepositoryInterface $siteRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->siteRepository = $siteRepository;
        $this->storeManager = $storeManager;
    }

    public function process(string $html, Page $page): string {
        $remotePageId = $page->getData('wordpress_page_id');
        [$siteId] = explode('_', $remotePageId);

        $site = $this->siteRepository->get($siteId);
        if ($site->getReplaceMediaUrls()) {
            /* Remap cms.store.com/wp-content to store.com/media/wp-content */
            $html = str_replace(
                $site->getBaseurl() . 'wp-content/uploads/',
                $this->storeManager->getStore()->getBaseUrl() . 'media/wp-content/uploads/',
                $html
            );
        }

        return $html;
    }
}
