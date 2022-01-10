<?php

declare(strict_types=1);

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

        $html = $remotePage['content']['rendered'];

        $html = preg_replace_callback("{{{(.*)}}}", function ($matches) {
            $match = $matches[0];

            // Solve wierd encoding from Wordpress API regarding double-dashes
            $match = preg_replace('/([^\s])&#8211;([^\s])/m', '$1--$2', $match);

            $match = html_entity_decode($match);
            $match = str_replace('”', '"', $match); // Opening quote
            $match = str_replace('″', '"', $match); // Ending quote
            $match = str_replace('“', '``', $match); // Double quotes
            return $match;
        }, $html);

        $this->remotePageContentCache[$remotePageId] = $html;

        return $html;
    }
}
