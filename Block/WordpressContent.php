<?php

namespace Mooore\WordpressIntegrationCms\Block;

use Magento\Framework\View\Element\Template;
use Mooore\WordpressIntegrationCms\Model\RemotePostRepository;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;
use Magento\Cms\Model\Template\FilterProvider;

class WordpressContent extends Template
{
    /**
     * @var RemotePostRepository
     */
    protected $remotePostRepository;

    /**
     * @var RemotePageRepository
     */
    protected $remotePageRepository;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    public function __construct(
        Template\Context $context,
        RemotePostRepository $remotePostRepository,
        RemotePageRepository $remotePageRepository,
        FilterProvider $filterProvider,

        array $data = []
    ) {
        $this->remotePostRepository = $remotePostRepository;
        $this->remotePageRepository = $remotePageRepository;
        $this->filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }

    public function toHtml()
    {
        $contentType = $this->getContentType() ?? 'page';
        $siteId = $this->getSiteId();
        $pageId = $this->getPageId();

        if (!$siteId || !$pageId) {
            throw new Exception(
                "Both Site ID and Page ID are required fields"
            );
        }

        $page = null;
        if ($contentType === 'page') {
            $page = $this->remotePageRepository->get($siteId, $pageId);
        }
        else if ($contentType === 'post') {
            $page = $this->remotePostRepository->get($siteId, $pageId);
        }

        $page['content']['rendered'] = preg_replace_callback("{{{(.*)}}}", function ($matches) {
            $match = $matches[0];

            // Solve wierd encoding from Wordpress API regarding double-dashes
            $match = preg_replace('/([^\s])&#8211;([^\s])/m', '$1--$2', $match);

            $match = html_entity_decode($match);
            $match = str_replace('”', '"', $match); // Opening quote
            $match = str_replace('″', '"', $match); // Ending quote
            return str_replace('“', '``', $match);  // Double quotes
        }, $page['content']['rendered']);

        $this->setData('page', $page);

        $html = $this->filterProvider->getPageFilter()->filter($page['content']['rendered']);

        return $html;
    }
}
