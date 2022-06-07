<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Plugin\Model;

use Magento\Cms\Model\Page;
use Magento\Store\Model\StoreManagerInterface;
use Mooore\WordpressIntegration\Api\SiteRepositoryInterface;
use Mooore\WordpressIntegrationCms\Resolver\RemotePageResolver;

class PagePlugin
{
    /**
     * @var RemotePageResolver
     */
    private $remotePageResolver;

    /**
     * @var SiteRepositoryInterface
     */
    private $siteRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        RemotePageResolver $remotePageResolver,
        SiteRepositoryInterface $siteRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->remotePageResolver = $remotePageResolver;
        $this->siteRepository = $siteRepository;
        $this->storeManager = $storeManager;
    }

    public function aroundGetContent(Page $subject, callable $proceed)
    {
        $remotePageId = $subject->getData('wordpress_page_id');

        if (empty($remotePageId)) {
            return $proceed();
        }

        [$siteId, $pageId] = explode('_', $remotePageId);

        $html = $this->remotePageResolver->resolve((int)$siteId, (int)$pageId);

        if ($html === null) {
            return $proceed();
        }

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
