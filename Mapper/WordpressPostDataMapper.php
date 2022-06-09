<?php

namespace Mooore\WordpressIntegrationCms\Mapper;

use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mooore\WordpressIntegrationCms\Api\Data\WordpressPostInterface;
use Mooore\WordpressIntegrationCms\Api\Data\WordpressPostInterfaceFactory;
use Mooore\WordpressIntegrationCms\Model\WordpressPostModel;

/**
 * Converts a collection of WordpressPost entities to an array of data transfer objects.
 */
class WordpressPostDataMapper
{
    /**
     * @var WordpressPostInterfaceFactory
     */
    private $entityDtoFactory;

    /**
     * @param WordpressPostInterfaceFactory $entityDtoFactory
     */
    public function __construct(
        WordpressPostInterfaceFactory $entityDtoFactory
    )
    {
        $this->entityDtoFactory = $entityDtoFactory;
    }

    /**
     * Map magento models to DTO array.
     *
     * @param AbstractCollection $collection
     *
     * @return array|WordpressPostInterface[]
     */
    public function map(AbstractCollection $collection): array
    {
        $results = [];
        /** @var WordpressPostModel $item */
        foreach ($collection->getItems() as $item) {
            /** @var WordpressPostInterface|DataObject $entityDto */
            $entityDto = $this->entityDtoFactory->create();
            $entityDto->addData($item->getData());

            $results[] = $entityDto;
        }

        return $results;
    }
}
