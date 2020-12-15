<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Plugin\Controller\Adminhtml\Page;

use Magento\Cms\Controller\Adminhtml\Page\MassDelete as MassDeleteController;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;

class MassDeletePlugin
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var RemotePageRepository
     */
    private $remotePageRepository;

    public function __construct(
        Filter $filter,
        CollectionFactory $collectionFactory,
        RemotePageRepository $remotePageRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->remotePageRepository = $remotePageRepository;
    }

    /**
     * This function calls to Wordpress to delete the Magento URL entry in a MassDelete action.
     *
     * @param MassDeleteController $subject
     * @return void
     */
    public function beforeExecute(MassDeleteController $subject)
    {
        foreach ($this->filter->getCollection($this->collectionFactory->create())->getData() as $page) {
            if (!array_key_exists('wordpress_page_id', $page) || null === $page['wordpress_page_id']) {
                continue;
            }

            $explodedwordpressPageId = explode("_", $page['wordpress_page_id']);
            $this->remotePageRepository->postMagentoUrlToPage(
                (int) $explodedwordpressPageId[0],
                (int) $explodedwordpressPageId[1],
                ''
            );
        }
    }
}
