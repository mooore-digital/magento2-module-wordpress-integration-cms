<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;

class WPCIData implements ArgumentInterface
{
    /**
     * @var RemotePageRepository
     */
    private $remotePageRepository;

    public function __construct(
        RemotePageRepository $remotePageRepository
    ) {
        $this->remotePageRepository = $remotePageRepository;
    }

    /**
     * Get the WordPress Base Url
     *
     * @return string|null
     */
    public function getWordPressUrl(): string
    {
        foreach ($this->remotePageRepository->getSites() as $site) {
            $url = $site->getBaseurl();
        }

        return $url;
    }
}
