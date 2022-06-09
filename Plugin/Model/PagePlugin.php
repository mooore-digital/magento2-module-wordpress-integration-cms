<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Plugin\Model;

use Magento\Cms\Model\Page;
use Magento\Store\Model\StoreManagerInterface;
use Mooore\WordpressIntegration\Api\SiteRepositoryInterface;
use Mooore\WordpressIntegrationCms\Processors\AfterHtmlProcessor;
use Mooore\WordpressIntegrationCms\Resolver\RemotePageResolver;

class PagePlugin
{
    /**
     * @var RemotePageResolver
     */
    private $remotePageResolver;

    private $afterHtmlProcessor;

    public function __construct(
        RemotePageResolver $remotePageResolver,
        AfterHtmlProcessor $afterHtmlProcessor
    ) {
        $this->remotePageResolver = $remotePageResolver;
        $this->afterHtmlProcessor = $afterHtmlProcessor;
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

        $html = $this->afterHtmlProcessor->process($html, $subject);

        return $html;
    }
}
