<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Cron;

use Mooore\WordpressIntegrationCms\Model\RemotePostRepository;
use Mooore\WordpressIntegration\Api\SiteRepositoryInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Mooore\WordpressIntegrationCms\Model\WordpressPostModel;
use Mooore\WordpressIntegrationCms\Model\Data\WordpressPostDataFactory;
use Mooore\WordpressIntegrationCms\Model\Data\WordpressPostData;
use Mooore\WordpressIntegrationCms\Model\WordpressPostRepository;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class IndexBlogUrls
{
    const URLREWRITE_ENTITY_TYPE = 'wpci-blog-post';

    /**
     * @var RemotePostRepository
     */
    protected $remotePostRepository;

    /**
     * @var SiteRepositoryInterface
     */
    protected $siteRepository;

    /**
     * @var UrlRewriteFactory
     */
    protected $urlRewriteFactory;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersistInterface;

    /**
     * @var WordpressPostDataFactory
     */
    protected $wordpressPostDataFactory;

    /**
     * @var WordpressPostRepository
     */
    protected $wordpressPostRepository;

    public function __construct(
        RemotePostRepository $remotePostRepository,
        SiteRepositoryInterface $siteRepository,
        UrlRewriteFactory $urlRewriteFactory,
        UrlPersistInterface $urlPersistInterface,
        WordpressPostDataFactory $wordpressPostDataFactory,
        WordpressPostRepository $wordpressPostRepository
    ) {
        $this->remotePostRepository = $remotePostRepository;
        $this->siteRepository = $siteRepository;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlPersistInterface = $urlPersistInterface;
        $this->wordpressPostDataFactory = $wordpressPostDataFactory;
        $this->wordpressPostRepository = $wordpressPostRepository;
    }

    public function execute(): void
    {
        $sites = $this->remotePostRepository->getList();
        $dbPosts = $this->wordpressPostRepository->getItems();

        /* @var string[] $urls */
        $urls = [];
        /* @var WordpressPostData[] $toBeDeleted */
        $toBeDeleted = [];
        /* @var array $toBeAdded */
        $toBeAdded = [];

        foreach ($sites as $siteId => $site) {
            $siteOptions = $this->siteRepository->get((string)$siteId);

            if (!$siteOptions->getEnableBlog()) {
                // * Site is disabled in M2
                $toBeDeleted = array_merge($toBeDeleted, array_filter($dbPosts, function ($row) use ($siteId) {
                    return (int)$row->getSiteId() === $siteId;
                }));
                continue;
            }

            foreach ($site['data'] as $post) {
                $dbPost = array_filter($dbPosts, function ($row) use ($post, $siteId) {
                    return (int)$row->getPostId() === $post['id'] &&
                        (int)$row->getSiteId() === $siteId;
                });

                if (count($dbPost) === 0) {
                    // * Page is added in WP
                    $toBeAdded[] = array_merge($post, ['site_id' => $siteId]);
                    continue;
                }

                $dbPost = reset($dbPost);
                if ($dbPost->getSlug() !== $post['slug']) {
                    $urls = array_merge($urls, $this->updatePageSlug($dbPost, $post['slug']));
                }
            }
        }

        foreach ($dbPosts as $post) {
            // * Page has been deleted from WP
            $site = $sites[$post->getSiteId()];

            $isDeleted = array_filter($site['data'], function ($row) use ($post) {
                return $row['id'] === (int)$post->getPostId();
            });

            if (count($isDeleted) !== 0) {
                continue;
            }

            $toBeDeleted[] = $post;
        }

        foreach ($toBeAdded as $row) {
            $urls = array_merge($urls, $this->registerPage($row));
        }

        foreach ($toBeDeleted as $row) {
            $this->unregisterPage($row);
        }

        $this->urlPersistInterface->replace($urls);
    }

    public function unregisterPage(WordpressPostData $page)
    {
        // * To be deleted
        $this->urlPersistInterface->deleteByData([
            UrlRewrite::ENTITY_TYPE => self::URLREWRITE_ENTITY_TYPE,
            UrlRewrite::ENTITY_ID => $page->getWordpressPostId()
        ]);

        $this->wordpressPostRepository->delete($page->getWordpressPostId());
    }

    public function registerPage(array $page)
    {
        // * To be added
        [
            'site_id' => $siteId,
            'id' => $postId,
            'slug' => $slug
        ] = $page;

        // * Create DB row
        $model = $this->wordpressPostDataFactory->create();
        $model->setSiteId($siteId);
        $model->setPostId($postId);
        $model->setSlug($slug);
        $modelId = $this->wordpressPostRepository->save($model);

        return $this->createRewriteRules($siteId, $postId, $modelId, $slug);
    }

    public function updatePageSlug(WordpressPostData $page, string $newSlug)
    {
        $page->setSlug($newSlug);
        $this->wordpressPostRepository->save($page);
        return $this->createRewriteRules($page->getSiteId(), $page->getPostId(), $page->getWordpressPostId(), $newSlug);
    }

    public function createRewriteRules($siteId, $postId, $entityId, $slug)
    {
        $siteOptions = $this->siteRepository->get($siteId);

        $prefix = $siteOptions->getBlogPrefix();
        $blogStores = explode(',', $siteOptions->getBlogStores());

        $url = $this->createUrl($prefix, $slug);

        $rewrites = [];
        foreach ($blogStores as $blogStoreId) {
            $rewrites[] = $this->urlRewriteFactory->create()
                ->setStoreId($blogStoreId)
                ->setEntityType(self::URLREWRITE_ENTITY_TYPE)
                ->setEntityId($entityId)
                ->setRequestPath($url)
                ->setTargetPath("wpci/blog/view/site_id/$siteId/page_id/$postId")
                ->setIsAutogenerated(1)
                ->setDescription("Generated by WPCI Blogs. Edit this in Content > Sites")
                ->setRedirectType(0);
        }

        return $rewrites;
    }

    public function createUrl($prefix = '', $slug)
    {
        $prefix = ltrim($prefix, '/'); // Remove trailing slash

        return "$prefix/$slug";
    }
}
