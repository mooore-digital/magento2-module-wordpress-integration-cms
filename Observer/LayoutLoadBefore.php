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

class LayoutLoadBefore implements ObserverInterface
{
    // TODO: check where contact_index_index is used for
    // And make sure this is needed in either array
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

    public function __construct(Http $request, PageRepository $pageRepository, Page $page)
    {
        $this->request = $request;
        $this->pageRepository = $pageRepository;
        $this->page = $page;
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
    }
}
