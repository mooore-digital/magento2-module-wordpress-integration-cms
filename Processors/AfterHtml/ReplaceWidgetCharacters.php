<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Processors\AfterHtml;

class ReplaceWidgetCharacters
{
    public function process(string $html): string
    {
        $html = preg_replace_callback("{{{(.*)}}}", function ($matches) {
            $match = $matches[0];

            // Solve wierd encoding from Wordpress API regarding double-dashes
            $match = preg_replace('/([^\s])&#8211;([^\s])/m', '$1--$2', $match);

            $match = html_entity_decode($match);
            $match = str_replace('”', '"', $match); // Opening quote
            $match = str_replace('″', '"', $match); // Ending quote
            return str_replace('“', '``', $match);  // Double quotes
        }, $html);

        return $html;
    }
}
