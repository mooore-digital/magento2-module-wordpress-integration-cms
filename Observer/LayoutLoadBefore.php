<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Observer;

use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageRepository;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Layout;
use Magento\Framework\Event\Observer;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;

class LayoutLoadBefore implements ObserverInterface
{
    // TODO: check if contact_index_index is used with CMS pages, if so, add the following code
    const DISALLOWED_PAGES = [
        'cms_page_view',
        'cms_index_index',
    ];

    const ALLOWED_PAGES = [
        'cms_index_noroute',
        'cms_index_nocookies',
    ];
    /**
     * @var Http
     */
    private $request;
    /**
     * @var PageRepository
     */
    private $pageRepository;
    /**
     * @var Page
     */
    private $page;

    private RemotePageRepository $remotePageRepository;

    public function __construct(
        Http $request,
        PageRepository $pageRepository,
        RemotePageRepository $remotePageRepository,
        Page $page
    ) {
        $this->request = $request;
        $this->pageRepository = $pageRepository;
        $this->page = $page;
        $this->remotePageRepository = $remotePageRepository;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $action = $observer->getData('full_action_name');

        if (!in_array($action, self::DISALLOWED_PAGES) && in_array($action, self::ALLOWED_PAGES)) {
            return;
        }

        $pageId = $this->page->getId();

        if ($pageId === null) {
            return;
        }

        try {
            $page = $this->pageRepository->getById($pageId);
        } catch (NoSuchEntityException $e) {
            return;
        }

        if (empty($page->getData('wordpress_page_id'))) {
            return;
        }

        /** @var Layout $layout */
        $layout = $observer->getData('layout');

        $layout->getUpdate()->addHandle('cms_wp_content');
        $layout->getUpdate()->addHandle('wpci_page_view');

        $this->addWordpressLayoutHandles($page->getData('wordpress_page_id'), $layout);
    }

    public function addWordpressLayoutHandles($wordpressPageId, $layout) {
        [$siteId, $pageId] = explode('_', $wordpressPageId);
        $siteId = (int)$siteId;
        $pageId = (int)$pageId;

        $wpPage = $this->remotePageRepository->get($siteId, $pageId);

        if (!array_key_exists('page_layout', $wpPage) || is_null($wpPage['page_layout'])) {
            return;
        }

        $layout->getUpdate()->addHandle(sprintf('wpci_page_view_%s', $wpPage['page_layout']));
    }
}
