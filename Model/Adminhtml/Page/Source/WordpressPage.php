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
        $options = [
            [
                'label' => __('Select a page...'),
                'value' => ''
            ]
        ];

        try {
            foreach ($this->remotePageRepository->getList() as $site) {
                if (!array_key_exists('data', $site)) {
                    $options[] = [
                        'label' => __('Could not fetch pages from %1', $site['name']),
                        'value' => $site['id']
                    ];
                    continue;
                }
                foreach ($site['data'] as $page) {
                    $options[] = [
                        'label' => $page['id'] . ' - ' . $page['modified_date_formatted'] . ' - ' . $page['title']['rendered'] . ' (' . $site['name'] . ')',
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
