<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Plugin\Model;

use Magento\Cms\Model\Page;
use Magento\Framework\Exception\LocalizedException;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;
use Mooore\WordpressIntegrationCms\Resolver\RemotePageResolver;

class PagePlugin
{
    /**
     * @var RemotePageResolver
     */
    private $remotePageResolver;

    public function __construct(
        RemotePageResolver $remotePageResolver
    ) {
        $this->remotePageResolver = $remotePageResolver;
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
