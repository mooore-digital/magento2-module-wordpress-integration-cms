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

class RemotePostRepository
{
    /**
     * @var HttpClient\Post
     */
    private $postClient;
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
        HttpClient\Post $postClient,
        SiteRepositoryInterface $siteRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StateInterface $cacheState,
        CacheInterface $cache,
        LoggerInterface $logger
    ) {
        $this->postClient = $postClient;
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
        $posts = [];

        $pageSize = 10; // @Hardcoded value: create a configuration for this value.

        foreach ($this->getSites() as $site) {
            $this->postClient->setBaseUrl($site->getBaseurl());

            try {
                $posts[$site->getSiteId()]['name'] = $site->getName();
                $posts[$site->getSiteId()]['id'] = $site->getSiteId();

                foreach ($this->postClient->all($pageSize) as $post) {
                    $posts[$site->getSiteId()]['data'][] = $post;
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

        return $posts;
    }

    /**
     * @param int $siteId
     * @param int $postId
     * @return array|null
     * @throws LocalizedException
     */
    public function get(int $siteId, int $postId): ?array
    {
        $cacheKey = sprintf('wordpress_post_%s_%s', $siteId, $postId);
        $cacheEnabled = $this->cacheState->isEnabled(CacheType::TYPE_IDENTIFIER);

        if ($cacheEnabled && $cacheEntry = $this->cache->load($cacheKey)) {
            return json_decode($cacheEntry, true);
        }

        $site = $this->siteRepository->get($siteId);
        $this->postClient->setBaseUrl($site->getBaseurl());

        try {
            $post = $this->postClient->get($postId);
        } catch (ExceptionInterface $exception) {
            $this->logger->error($exception->getMessage());

            throw new LocalizedException(
                __('Something went wrong with requesting a Wordpress resource. See logs for more information.'),
                $exception,
                $exception->getCode()
            );
        }

        if ($cacheEnabled) {
            $this->cache->save(json_encode($post), $cacheKey, [CacheType::TYPE_IDENTIFIER]);
        }

        return $post;
    }

    public function postMetaData(int $siteId, int $postId, string $key, string $value)
    {
        $site = $this->siteRepository->get($siteId);
        $this->postClient->setBaseUrl($site->getBaseurl());

        if (empty(($username = $site->getApiUsername())) || empty(($password = $site->getApiPassword()))) {
            throw new LocalizedException(
                __('API credentials have not been found, meta data has not been written to post. Please check API credentials.')
            );
        }

        try {
            $this->postClient->postMetaDataToPost($postId, $key, $value, $username.':'.$password);
        } catch (ExceptionInterface $exception) {
            $this->logger->error($exception->getMessage());

            throw new LocalizedException(
                __('Something went wrong when posting metadata to the Wordpress resource. See logs for more information.'),
                $exception,
                $exception->getCode()
            );
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
