<?php

namespace Mooore\WordpressIntegrationCms\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Mooore\WordpressIntegrationCms\Api\Data\WordpressPostInterface;

class WordpressPostResource extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'wordpress_post_resource_model';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('mooore_wordpressintegration_wordpress_post', WordpressPostInterface::WORDPRESS_POST_ID);
        $this->_useIsObjectNew = true;
    }
}
