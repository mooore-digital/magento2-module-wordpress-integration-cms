<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model;

use Exception;
use Mooore\WordpressIntegrationCms\Api\Data\WordpressPostInterface;
use Mooore\WordpressIntegrationCms\Mapper\WordpressPostDataMapper;
use Mooore\WordpressIntegrationCms\Model\Data\WordpressPostData;
use Mooore\WordpressIntegrationCms\Model\ResourceModel\WordpressPostResource;
use Mooore\WordpressIntegrationCms\Model\ResourceModel\WordpressPostModel\WordpressPostCollectionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Mooore\WordpressIntegrationCms\Model\Data\WordpressPostDataFactory as WordpressPostFactory;

/**
 * @suppressWarnings(PHPMD.LongVariable)
 * @suppressWarnings(PHPMD.ShortVariable)
 */
class WordpressPostRepository
{
    private LoggerInterface $logger;
    private WordpressPostModelFactory $modelFactory;
    private WordpressPostResource $resource;
    private WordpressPostCollectionFactory $collectionFactory;
    private WordpressPostFactory $wordpressPostFactory;
    private WordpressPostDataMapper $wordpressPostDataMapper;

    /**
     * @param LoggerInterface $logger
     * @param WordpressPostModelFactory $modelFactory
     * @param WordpressPostResource $resource
     */
    public function __construct(
        LoggerInterface $logger,
        WordpressPostModelFactory $modelFactory,
        WordpressPostResource $resource,
        WordpressPostCollectionFactory $collectionFactory,
        WordpressPostFactory $wordpressPostFactory,
        WordpressPostDataMapper $wordpressPostDataMapper
    ) {
        $this->logger = $logger;
        $this->modelFactory = $modelFactory;
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->wordpressPostFactory = $wordpressPostFactory;
        $this->wordpressPostDataMapper = $wordpressPostDataMapper;
    }

    /**
     * Save WordpressPost.
     *
     * @param WordpressPostData $wordpressPost
     *
     * @return int
     * @throws CouldNotSaveException
     */
    public function save(WordpressPostInterface $wordpressPost): int
    {
        try {
            /** @var WordpressPostModel $model */
            $model = $this->modelFactory->create();
            /**
             * @psalm-suppress MixedArgument
             */
            $model->addData($wordpressPost->getData());

            if (!$model->getData(WordpressPostInterface::WORDPRESS_POST_ID)) {
                $model->isObjectNew(true);
            }
            $this->resource->save($model);
        } catch (Exception $exception) {
            $this->logger->error(
                __('Could not save WordpressPost. Original message: {message}'),
                [
                    'message' => $exception->getMessage(),
                    'exception' => $exception
                ]
            );
            throw new CouldNotSaveException(__('Could not save WordpressPost.'));
        }

        return (int)$model->getData(WordpressPostInterface::WORDPRESS_POST_ID);
    }

    public function get(int $id): ?WordpressPostInterface
    {
        $collection = $this->collectionFactory->create([]);

        /**
         * @psalm-suppress InvalidScalarArgument
         */
        $collection->addFieldToFilter(WordpressPostInterface::WORDPRESS_POST_ID, $id);

        /** @var WordpressPostModel|false $wordpressPost */
        $wordpressPost = $collection->fetchItem();

        if ($wordpressPost === false) {
            return null;
        }

        $model = $this->wordpressPostFactory->create();
        /**
         * @psalm-suppress MixedArgument
         */
        $model->setData($wordpressPost->getData());

        return $model;
    }

    /**
     * @return \Mooore\WordpressIntegrationCms\Api\Data\WordpressPostInterface[]
     */
    public function getItems()
    {
        $collection = $this->collectionFactory->create([]);

        return $this->wordpressPostDataMapper->map($collection);
    }

    /**
     * Delete WordpressPost.
     *
     * @param int $entityId
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete(int $entityId): void
    {
        try {
            /** @var WordpressPostModel $model */
            $model = $this->modelFactory->create();
            $this->resource->load($model, $entityId, WordpressPostInterface::WORDPRESS_POST_ID);

            if (!$model->getData(WordpressPostInterface::WORDPRESS_POST_ID)) {
                throw new NoSuchEntityException(
                    __(
                        'Could not find WordpressPost with id: `%id`',
                        [
                            'id' => $entityId
                        ]
                    )
                );
            }

            $this->resource->delete($model);
        } catch (Exception $exception) {
            $this->logger->error(
                __('Could not delete WordpressPost. Original message: {message}'),
                [
                    'message' => $exception->getMessage(),
                    'exception' => $exception
                ]
            );
            throw new CouldNotDeleteException(__('Could not delete WordpressPost.'));
        }
    }
}
