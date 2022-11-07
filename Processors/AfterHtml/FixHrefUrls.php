<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Processors\AfterHtml;

use Mooore\WordpressIntegration\Api\SiteRepositoryInterface;

class FixHrefUrls
{
    /**
     * @var SiteRepositoryInterface
     */
    private $siteRepository;

    public function __construct(
        SiteRepositoryInterface $siteRepository
    ) {
        $this->siteRepository = $siteRepository;
    }

    public function process(string $html, $siteId): string
    {
        $site = $this->siteRepository->get($siteId);

        if ($site->getEnableBlog()) {
            $base_url = $site->getBaseurl();
            $prefix = $site->getBlogPrefix();
            $prefix = ltrim($prefix, '/');

            $html = preg_replace_callback(
                '<a.*?href="('.str_replace('/', '\\/', preg_quote($base_url)).'.*?)".*?>',
                function ($matches) use ($base_url, $prefix) {
                    return str_replace($base_url, '/' . $prefix . '/', $matches[0]);
                },
                $html);
        }

        return $html;
    }
}
