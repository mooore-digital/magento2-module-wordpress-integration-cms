<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Plugin\Model;

use Magento\Cms\Model\PageRepository;
use Magento\Framework\Controller\ResultInterface;
use Mooore\WordpressIntegrationCms\Model\HttpClient\Page as PageClient;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;
use Magento\Cms\Model\Page;
use Magento\Cms\Helper\Page as PageHelper;
use Magento\Cms\Api\Data\PageInterface;

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

    public function __construct(
        PageClient $pageClient,
        RemotePageRepository $remotePageRepository,
        PageHelper $pageHelper
    ) {
        $this->pageClient = $pageClient;
        $this->remotePageRepository = $remotePageRepository;
        $this->pageHelper = $pageHelper;
    }

    public function afterSave(PageRepository $subject, Page $result)
    {
        $wordpressSiteAndPageId = $result->getData('wordpress_page_id');
        if (!$wordpressSiteAndPageId) {
            return $result;
        }

        $explodedWordpressSiteAndPageId = explode("_", $wordpressSiteAndPageId);
        $this->remotePageRepository->postMetaData(
            (int) $explodedWordpressSiteAndPageId[0],
            (int) $explodedWordpressSiteAndPageId[1],
            'mooore_magento_cms_url',
            $this->pageHelper->getPageUrl($result->getData('page_id'))
        );

        return $result;
    }
}
