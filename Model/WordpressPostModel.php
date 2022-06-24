<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model;

use Magento\Framework\Model\AbstractModel;
use Mooore\WordpressIntegrationCms\Model\ResourceModel\WordpressPostResource;

class WordpressPostModel extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'wordpress_post_model';

    /**
     * Initialize magento model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(WordpressPostResource::class);
    }
}
