<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model;

use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Mooore\WordpressIntegration\Api\Data\SiteInterface;
use Mooore\WordpressIntegration\Api\SiteRepositoryInterface;

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

    public function __construct(
        ClientFactory $clientFactory,
        Configuration $config,
        SiteRepositoryInterface $siteRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->client = $clientFactory->create();
        $this->config = $config;
        $this->siteRepository = $siteRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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
        try {
            $site = $this->siteRepository->get($siteId);
            $baseUrl = trim($site->getBaseurl());
            $response = $this->client->get($baseUrl . '/wp-json/wp/v2/pages/' . $pageId);

            return json_decode($response->getBody()->getContents(), true);
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
