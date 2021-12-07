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
     * @var array
     */
    private $remotePageContentCache = [];
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
        
        $html = $this->remotePageResolver->resolve($remotePageId);

        if ($html === null) {
            return $proceed();
        }

        return $html;
    }
}
