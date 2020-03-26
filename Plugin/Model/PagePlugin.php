<?php

namespace Mooore\WordpressIntegrationCms\Plugin\Model;

use Magento\Cms\Model\Page;
use Magento\Framework\Exception\LocalizedException;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;

class PagePlugin
{
    /**
     * @var RemotePageRepository
     */
    private $pageRepository;
    /**
     * @var array
     */
    private $remotePageContentCache = [];

    public function __construct(RemotePageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function aroundGetContent(Page $subject, callable $proceed)
    {
        $remotePageId = $subject->getData('wordpress_page_id');

        if (empty($remotePageId)) {
            return $proceed();
        }

        if (isset($this->remotePageContentCache[$remotePageId])) {
            return $this->remotePageContentCache[$remotePageId];
        }

        [$siteId, $pageId] = explode('_', $remotePageId);

        try {
            $remotePage = $this->pageRepository->get((int) $siteId, (int) $pageId);
        } catch (LocalizedException $e) {
            return $proceed();
        }

        if ($remotePage === null || empty($remotePage['content'])) {
            return $proceed();
        }

        $this->remotePageContentCache[$remotePageId] = $remotePage['content']['rendered'];

        return $remotePage['content']['rendered'];
    }
}
