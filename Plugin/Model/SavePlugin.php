<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Plugin\Model;

use Magento\Cms\Model\PageRepository;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Mooore\WordpressIntegrationCms\Model\HttpClient\Page as PageClient;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;
use Magento\Cms\Model\Page;
use Magento\Cms\Helper\Page as PageHelper;
use Magento\Cms\Api\Data\PageInterface;
use Mooore\WordpressIntegrationCms\Model\Config;
use Magento\Framework\Message\ManagerInterface as MessageManager;

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
     * @var PageHelper
     */
    private $pageHelper;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var MessageManager
     */
    private $messageManager;

    public function __construct(
        PageClient $pageClient,
        RemotePageRepository $remotePageRepository,
        PageHelper $pageHelper,
        Config $config,
        MessageManager $messageManager
    ) {
        $this->pageClient = $pageClient;
        $this->remotePageRepository = $remotePageRepository;
        $this->pageHelper = $pageHelper;
        $this->config = $config;
        $this->messageManager = $messageManager;
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
                $this->pageHelper->getPageUrl($result->getData('page_id'))
            );
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            return $result;
        }

        return $result;
    }
}
