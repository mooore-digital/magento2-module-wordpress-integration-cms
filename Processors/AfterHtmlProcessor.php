<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Processors;

use Magento\Cms\Model\Page;

class AfterHtmlProcessor
{
    /**
     * @var DataSetterInterface[]
     */
    private array $processors;

    /**
     * ProductDataProcessor constructor.
     * @param DataSetterInterface[] $processors
     */
    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    public function process($html, $siteId): string
    {
        foreach ($this->processors as $processor) {
            $html = $processor->process($html, $siteId);
        }

        return $html;
    }
}
