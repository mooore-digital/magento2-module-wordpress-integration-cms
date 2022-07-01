<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Controller\Blog;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\Context;
use Mooore\WordpressIntegrationCms\Model\RemotePostRepository;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultFactory;

class View extends Action implements HttpGetActionInterface
{
    /**
     * @var RemotePostRepository
     */
    protected $remotePostRepository;

    /**
     * @var ResultPage
     */
    protected $resultPage;

    /**
     * @var ForwardFactory
     */
    protected $forwardFactory;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    public function __construct(
        Context $context,
        RemotePostRepository $remotePostRepository,
        ResultPage $resultPage,
        ForwardFactory $forwardFactory,
        ResultFactory $resultFactory
    ) {
        $this->remotePostRepository = $remotePostRepository;
        $this->resultPage = $resultPage;
        $this->forwardFactory = $forwardFactory;
        $this->resultFactory = $resultFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $siteId = (int)$this->getRequest()->getParam('site_id', false);
        $pageId = (int)$this->getRequest()->getParam('page_id', false);

        $page = null;
        try {
            $page = $this->remotePostRepository->get($siteId, $pageId);
        }
        catch (\Exception $e) {
            $page = null;
        }

        if (!$page) {
            // 404
            $resultForward = $this->forwardFactory->create();
            $resultForward->setController('index');
            $resultForward->forward('defaultNoRoute');
            return $resultForward;
        }

        $this->resultPage->getConfig()->getTitle()->set($page['title']['rendered']);
        
        $descriptionRaw = $page['excerpt']['rendered'];
        if($descriptionRaw) {
            $description = trim(strip_tags($descriptionRaw));
            $this->resultPage->getConfig()->setDescription($description);
        }

        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $layout = $page->getLayout();

        $block = $layout->getBlock('blog.content');
        if ($block) {
            $block->setData('content_type', 'post');
            $block->setData('site_id', $siteId);
            $block->setData('page_id', $pageId);
        }

        return $page;
    }
}
