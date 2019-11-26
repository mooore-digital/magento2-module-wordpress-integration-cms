<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model\Page\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Mooore\WordpressIntegrationCms\Model\RemotePageRepository;

class WordpressPage implements OptionSourceInterface
{
    /**
     * @var RemotePageRepository
     */
    private $remotePageRepository;

    public function __construct(RemotePageRepository $remotePageRepository)
    {
        $this->remotePageRepository = $remotePageRepository;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->remotePageRepository->getList() as $site) {
            foreach ($site['data'] as $page) {
                $options[] = [
                    'label' => $page['title']['rendered'] . ' (' . $site['name'] . ')',
                    'value' => $site['id'] .'_' . $page['id']
                ];
            }
        }

        return $options;

        /*
         *
        $configOptions = $this->pageLayoutBuilder->getPageLayoutsConfig()->getOptions();
        $options = [];
        foreach ($configOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        $this->options = $options;

        return $options;
         */
    }
}
