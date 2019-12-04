<?php

namespace Mooore\WordpressIntegrationCms\Observer;

use Magento\Cms\Model\PageRepository;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Layout;

class LayoutLoadBefore implements ObserverInterface
{
    /**
     * @var Http
     */
    private $request;
    /**
     * @var PageRepository
     */
    private $pageRepository;

    public function __construct(Http $request, PageRepository $pageRepository)
    {
        $this->request = $request;
        $this->pageRepository = $pageRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $action = $observer->getData('full_action_name');

        if ($action !== 'cms_page_view') {
            return;
        }

        $pageId = $this->request->getParam('page_id');

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

        $layout->getUpdate()->addHandle('cms_wp-content');
    }
}
