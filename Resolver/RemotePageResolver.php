<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Resolver;

use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;

class RemotePageResolver
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

    public function resolve(int $siteId, int $pageId): ?string
    {
        $cacheKey = sprintf('page_%s_%s', $siteId, $pageId);

        if (isset($this->remotePageContentCache[$cacheKey])) {
            return $this->remotePageContentCache[$cacheKey];
        }

        try {
            $remotePage = $this->pageRepository->get($siteId, $pageId);
        } catch (LocalizedException $e) {
            return null;
        }

        if ($remotePage === null || empty($remotePage['content'])) {
            return null;
        }

        /** @var string $html */
        $html = $remotePage['content']['rendered'];

        $this->remotePageContentCache[$cacheKey] = $html;

        return $this->remotePageContentCache[$cacheKey];
    }
}
