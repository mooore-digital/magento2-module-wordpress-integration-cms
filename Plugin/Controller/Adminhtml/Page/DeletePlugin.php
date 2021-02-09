<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Plugin\Controller\Adminhtml\Page;

use Magento\Cms\Controller\Adminhtml\Page\Delete as DeleteController;
use Magento\Cms\Model\PageRepository;
use Magento\Framework\Exception\LocalizedException;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;
use Mooore\WordpressIntegrationCms\Model\Config;
use Magento\Framework\Message\ManagerInterface as MessageManager;

class DeletePlugin
{
    /**
     * @var PageRepository
     */
    private $pageRepository;

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
        PageRepository $pageRepository,
        RemotePageRepository $remotePageRepository,
        Config $config,
        MessageManager $messageManager
    ) {
        $this->pageRepository = $pageRepository;
        $this->remotePageRepository = $remotePageRepository;
        $this->config = $config;
        $this->messageManager = $messageManager;
    }

    /**
     * This function calls Wordpress to clear the Magento URL entry on page deletion.
     *
     * @param DeleteController $subject
     * @return void
     */
    public function beforeExecute(DeleteController $subject)
    {
        $wordpressSiteAndPageId = $this->pageRepository->getById(
            $subject->getRequest()->getParam('page_id')
        )->getData('wordpress_page_id');

        if (!$this->config->magentoUrlPushBackEnabled() || !$wordpressSiteAndPageId) {
            return;
        }

        $explodedWordpressSiteAndPageId = explode('_', $wordpressSiteAndPageId);
        try {
            $this->remotePageRepository->postMetaData(
                (int) $explodedWordpressSiteAndPageId[0],
                (int) $explodedWordpressSiteAndPageId[1],
                'mooore_magento_cms_url',
                ''
            );
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            return;
        }
    }
}
