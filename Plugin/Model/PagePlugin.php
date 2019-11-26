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

    public function __construct(RemotePageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function aroundGetContent(Page $subject, callable $proceed)
    {
        if (empty($subject->getData('wordpress_page_id'))) {
            return $proceed();
        }

        [$siteId, $pageId] = explode('_', $subject->getData('wordpress_page_id'));

        $remotePage = $this->pageRepository->get((int) $siteId, (int) $pageId);

        if ($remotePage === null || empty($remotePage['content'])) {
            return  $proceed();
        }

        return $remotePage['content']['rendered'];
    }
}
