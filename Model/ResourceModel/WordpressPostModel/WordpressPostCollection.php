<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model\ResourceModel\WordpressPostModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mooore\WordpressIntegrationCms\Model\ResourceModel\WordpressPostResource;
use Mooore\WordpressIntegrationCms\Model\WordpressPostModel;

class WordpressPostCollection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'wordpress_post_collection';

    /**
     * Initialize collection model.
     */
    protected function _construct()
    {
        $this->_init(WordpressPostModel::class, WordpressPostResource::class);
    }
}
