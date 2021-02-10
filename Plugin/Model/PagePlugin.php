<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Plugin\Model;

use Magento\Cms\Model\Page;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Exception\LocalizedException;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;

class PagePlugin
{
    /**
     * @var RemotePageRepository
     */
    private $pageRepository;

    /**
     * @var FilterProvider
     */
    private $filterProvider;
    /**
     * @var array
     */
    private $remotePageContentCache = [];


    public function __construct(RemotePageRepository $pageRepository, FilterProvider $filterProvider)
    {
        $this->pageRepository = $pageRepository;
        $this->filterProvider = $filterProvider;
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

        $html = preg_replace_callback("{{(.*)}}", function ($matches) {
            $match = $matches[0];

            $match = html_entity_decode($match);
            $match = str_replace("”", "\"", $match); // Opening quote
            $match = str_replace("″", "\"", $match); // Ending quote
            $match = str_replace("“", "``", $match); // Double quotes
            return $match;
        }, $html);

        $html = $this->filterProvider->getPageFilter()->filter($html);

        $this->remotePageContentCache[$remotePageId] = $html;

        return $html;
    }
}
