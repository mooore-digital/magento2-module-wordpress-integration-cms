<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Plugin\Controller\Adminhtml\Page;

use Magento\Cms\Controller\Adminhtml\Page\MassDelete as MassDeleteController;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Ui\Component\MassAction\Filter;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;
use Mooore\WordpressIntegrationCms\Model\Config;

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

    /**
     * @var Config
     */
    private $config;

    /**
     * @var MessageManager
     */
    private $messageManager;

    public function __construct(
        Filter $filter,
        CollectionFactory $collectionFactory,
        RemotePageRepository $remotePageRepository,
        Config $config,
        MessageManager $messageManager
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->remotePageRepository = $remotePageRepository;
        $this->config = $config;
        $this->messageManager = $messageManager;
    }

    /**
     * This function calls to Wordpress to delete the Magento URL entry in a MassDelete action.
     *
     * @param MassDeleteController $subject
     * @return void
     */
    public function beforeExecute(MassDeleteController $subject)
    {
        if (!$this->config->magentoUrlPushBackEnabled()) {
            return;
        }

        foreach ($this->filter->getCollection($this->collectionFactory->create())->getData() as $page) {
            if (!array_key_exists('wordpress_page_id', $page) || null === $page['wordpress_page_id']) {
                continue;
            }

            $wordpressSiteAndPageId = explode('_', $page['wordpress_page_id']);
            try {
                $this->remotePageRepository->postMetaData(
                    (int) $wordpressSiteAndPageId[0],
                    (int) $wordpressSiteAndPageId[1],
                    'mooore_magento_cms_url',
                    ''
                );
            } catch (LocalizedException $exception) {
                // Only add message to the MessageManager if no error messages are found.
                // Otherwise, you will get 'X' amount of error messages for 'X' amount of page-deletions.
                // This can add up to a large amount of unnecessary extra error messages.
                if (empty($this->messageManager->getMessages()->getItems())) {
                    $this->messageManager->addErrorMessage($exception->getMessage());
                }
                continue;
            }
        }
    }
}
