<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\LocalizedException;
use Mooore\WordpressIntegration\Api\Data\SiteInterface;
use Mooore\WordpressIntegration\Api\SiteRepositoryInterface;
use Mooore\WordpressIntegration\Model\Cache\Type as CacheType;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

class RemotePageRepository
{
    /**
     * @var HttpClient\Page
     */
    private $pageClient;
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
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        HttpClient\Page $pageClient,
        SiteRepositoryInterface $siteRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StateInterface $cacheState,
        CacheInterface $cache,
        LoggerInterface $logger
    ) {
        $this->pageClient = $pageClient;
        $this->siteRepository = $siteRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cacheState = $cacheState;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getList(): array
    {
        $pages = [];

        $pageSize = 10; // @Hardcoded value: create a configuration for this value.

        foreach ($this->getSites() as $site) {
            $this->pageClient->setBaseUrl($site->getBaseurl());

            try {
                $pages[$site->getSiteId()]['name'] = $site->getName();
                $pages[$site->getSiteId()]['id'] = $site->getSiteId();

                foreach ($this->pageClient->all($pageSize) as $page) {
                    $pages[$site->getSiteId()]['data'][] = $page;
                }
            } catch (ExceptionInterface $exception) {
                $this->logger->error($exception->getMessage());

                throw new LocalizedException(
                    __('Something went wrong with requesting a Wordpress resource. See logs for more information.'),
                    $exception,
                    $exception->getCode()
                );
            }
        }

        return $pages;
    }

    /**
     * @param int $siteId
     * @param int $pageId
     * @return array|null
     * @throws LocalizedException
     */
    public function get(int $siteId, int $pageId): ?array
    {
        $cacheKey = sprintf('wordpress_page_%s_%s', $siteId, $pageId);
        $cacheEnabled = $this->cacheState->isEnabled(CacheType::TYPE_IDENTIFIER);

        if ($cacheEnabled && $cacheEntry = $this->cache->load($cacheKey)) {
            return json_decode($cacheEntry, true);
        }

        $site = $this->siteRepository->get($siteId);
        $this->pageClient->setBaseUrl($site->getBaseurl());

        try {
            $page = $this->pageClient->get($pageId);
        } catch (ExceptionInterface $exception) {
            $this->logger->error($exception->getMessage());

            throw new LocalizedException(
                __('Something went wrong with requesting a Wordpress resource. See logs for more information.'),
                $exception,
                $exception->getCode()
            );
        }

        if ($cacheEnabled) {
            $this->cache->save(json_encode($page), $cacheKey, [CacheType::TYPE_IDENTIFIER]);
        }

        return $page;
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
