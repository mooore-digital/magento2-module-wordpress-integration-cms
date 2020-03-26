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
     * @var ClientFactory
     */
    private $clientFactory;
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
        ClientFactory $clientFactory,
        SiteRepositoryInterface $siteRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StateInterface $cacheState,
        CacheInterface $cache,
        LoggerInterface $logger
    ) {
        $this->clientFactory = $clientFactory;
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

        foreach ($this->getSites() as $site) {
            $client = $this->clientFactory->get($site->getBaseurl());

            try {
                $response = $client->request('GET', '/wp-json/wp/v2/pages');
                $pages[$site->getSiteId()]['name'] = $site->getName();
                $pages[$site->getSiteId()]['id'] = $site->getSiteId();
                $pages[$site->getSiteId()]['data'] = json_decode($response->getContent(), true);
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
        $client = $this->clientFactory->get($site->getBaseurl());

        try {
            $response = $client->request('GET', '/wp-json/wp/v2/pages/' . $pageId);
            $result = json_decode($response->getContent(), true);
        } catch (ExceptionInterface $exception) {
            $this->logger->error($exception->getMessage());

            throw new LocalizedException(
                __('Something went wrong with requesting a Wordpress resource. See logs for more information.'),
                $exception,
                $exception->getCode()
            );
        }

        if ($cacheEnabled) {
            $this->cache->save(json_encode($result), $cacheKey, [CacheType::TYPE_IDENTIFIER]);
        }

        return $result;
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
