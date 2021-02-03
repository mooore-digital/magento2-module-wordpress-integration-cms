<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Plugin\Controller\Adminhtml\Page;

use Magento\Cms\Controller\Adminhtml\Page\Delete as DeleteController;
use Magento\Cms\Model\PageRepository;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;

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

    public function __construct(PageRepository $pageRepository, RemotePageRepository $remotePageRepository)
    {
        $this->pageRepository = $pageRepository;
        $this->remotePageRepository = $remotePageRepository;
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

        if (!$wordpressSiteAndPageId) {
            return;
        }

        $explodedWordpressSiteAndPageId = explode("_", $wordpressSiteAndPageId);
        $this->remotePageRepository->postMetaData(
            (int) $explodedWordpressSiteAndPageId[0],
            (int) $explodedWordpressSiteAndPageId[1],
            'mooore_magento_cms_url',
            ''
        );
    }
}
