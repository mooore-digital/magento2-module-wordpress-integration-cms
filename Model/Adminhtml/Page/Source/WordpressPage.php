<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model\Adminhtml\Page\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;

class WordpressPage implements OptionSourceInterface
{
    /**
     * @var RemotePageRepository
     */
    private $remotePageRepository;
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    public function __construct(RemotePageRepository $remotePageRepository, ManagerInterface $messageManager)
    {
        $this->remotePageRepository = $remotePageRepository;
        $this->messageManager = $messageManager;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = [];

        $options[] = [
            'label' => __('Select a page...'),
            'value' => ''
        ];

        try {
            foreach ($this->remotePageRepository->getList() as $site) {
                foreach ($site['data'] as $page) {
                    $options[] = [
                        'label' => $page['title']['rendered'] . ' (' . $site['name'] . ')',
                        'value' => $site['id'] . '_' . $page['id'],
                    ];
                }
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $options;
    }
}
