<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model;

use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\LocalizedException;
use Mooore\WordpressIntegration\Api\Data\SiteInterface;
use Mooore\WordpressIntegration\Api\SiteRepositoryInterface;
use Mooore\WordpressIntegration\Model\Cache\Type as CacheType;

class RemotePageRepository
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var Configuration
     */
    private $config;
    /**
     * @var SiteRepositoryInterface
     */
    private $siteRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var StateInterface
     */
    private $cacheState;
    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(
        ClientFactory $clientFactory,
        Configuration $config,
        SiteRepositoryInterface $siteRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StateInterface $cacheState,
        CacheInterface $cache
    ) {
        $this->client = $clientFactory->create();
        $this->config = $config;
        $this->siteRepository = $siteRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cacheState = $cacheState;
        $this->cache = $cache;
    }

    public function getList(): array
    {
        $pages = [];

        foreach ($this->getSites() as $site) {
            $baseUrl = trim($site->getBaseurl());
            $response = $this->client->get($baseUrl . '/wp-json/wp/v2/pages');
            $pages[$site->getSiteId()]['name'] = $site->getName();
            $pages[$site->getSiteId()]['id'] = $site->getSiteId();
            $pages[$site->getSiteId()]['data'] = json_decode($response->getBody()->getContents(), true);
        }

        return $pages;
    }

    public function get(int $siteId, int $pageId): ?array
    {
        $cacheKey = sprintf('wordpress_page_%s_%s', $siteId, $pageId);
        $cacheEnabled = $this->cacheState->isEnabled(CacheType::TYPE_IDENTIFIER);

        if ($cacheEnabled && $cacheEntry = $this->cache->load($cacheKey)) {
            return json_decode($cacheEntry, true);
        }

        try {
            $site = $this->siteRepository->get($siteId);
            $baseUrl = trim($site->getBaseurl());
            $response = $this->client->get($baseUrl . '/wp-json/wp/v2/pages/' . $pageId);

            $result = json_decode($response->getBody()->getContents(), true);

            if ($cacheEnabled) {
                $this->cache->save(json_encode($result), $cacheKey, [CacheType::TYPE_IDENTIFIER]);
            }

            return $result;
        } catch (LocalizedException $e) {
            return null;
        }
    }

    /**
     * @return SiteInterface[]
     */
    private function getSites(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('enabled', 1)->create();

        try {
            $result = $this->siteRepository->getList($searchCriteria);
        } catch (LocalizedException $e) {
            return [];
        }

        return $result->getItems();
    }
}
