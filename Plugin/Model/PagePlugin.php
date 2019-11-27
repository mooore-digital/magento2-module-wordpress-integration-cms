<?php

namespace Mooore\WordpressIntegrationCms\Plugin\Model;

use Magento\Cms\Model\Page;
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

        $remotePage = $this->pageRepository->get((int) $siteId, (int) $pageId);

        if ($remotePage === null || empty($remotePage['content'])) {
            return  $proceed();
        }

        $this->remotePageContentCache[$remotePageId] = $remotePage['content']['rendered'];

        return $remotePage['content']['rendered'];
    }
}
