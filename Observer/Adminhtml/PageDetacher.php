<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Observer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Mooore\WordpressIntegrationCms\Model\Config;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;

/**
 * This Observer class sets the Wordpress page ID to NULL when the page has been detached and updates the record in the
 * Wordpress environment appropriately.
 *
 * @class PageDetacher
 */
class PageDetacher implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var RemotePageRepository
     */
    private $remotePageRepository;

    public function __construct(RemotePageRepository $remotePageRepository, Config $config)
    {
        $this->remotePageRepository = $remotePageRepository;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $page = $observer->getData('page');
        if (!array_key_exists('wordpress_page_id', $observer->getData('page')->getData())) {
            $page->setData('wordpress_page_id', null);
            $wordpressSiteAndPageId = $page->getOrigData('wordpress_page_id');
            if (!$this->config->magentoUrlPushBackEnabled() || !$wordpressSiteAndPageId) {
                return;
            }

            $explodedWordpressSiteAndPageId = explode('_', $page->getOrigData('wordpress_page_id'));
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
}
