<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Plugin\Model;

use Magento\Cms\Model\PageRepository;
use Magento\Cms\Model\Page;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Mooore\WordpressIntegrationCms\Model\HttpClient\Page as PageClient;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;
use Mooore\WordpressIntegrationCms\Model\Config;

class SavePlugin
{
    /**
     * @var PageClient
     */
    private $pageClient;

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

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        PageClient $pageClient,
        RemotePageRepository $remotePageRepository,
        Config $config,
        MessageManager $messageManager,
        StoreManagerInterface $storeManager
    ) {
        $this->pageClient = $pageClient;
        $this->remotePageRepository = $remotePageRepository;
        $this->config = $config;
        $this->messageManager = $messageManager;
        $this->storeManager = $storeManager;
    }

    public function afterSave(PageRepository $subject, Page $result)
    {
        $wordpressSiteAndPageId = $result->getData('wordpress_page_id');

        if (!$wordpressSiteAndPageId || !$this->config->magentoUrlPushBackEnabled()) {
            return $result;
        }

        $explodedWordpressSiteAndPageId = explode('_', $wordpressSiteAndPageId);
        try {
            $this->remotePageRepository->postMetaData(
                (int) $explodedWordpressSiteAndPageId[0],
                (int) $explodedWordpressSiteAndPageId[1],
                'mooore_magento_cms_url',
                $this->buildUrlFromPage($result)
            );
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            return $result;
        }

        return $result;
    }

    private function buildUrlFromPage(Page $page): string
    {
        try {
            $storeIds = $page->getStoreId();
            if (!count($storeIds)) {
                return '';
            }

            try {
                $firstStoreId = $storeIds[0];
                $storeFromPage = $this->storeManager->getStore($firstStoreId);

                return $storeFromPage->getBaseUrl() . $page->getIdentifier();
            } catch (NoSuchEntityException $e) {
                return '';
            }
        } catch (NoSuchEntityException $e) {
            return '';
        }

        return $storeFromPage->getBaseUrl() . $page->getIdentifier();
    }
}
