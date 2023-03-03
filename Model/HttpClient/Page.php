<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model\HttpClient;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Page extends Wordpress
{
    public ?string $type = 'pages';

    public function postMetaDataToPage(int $pageId, string $key, string $value, string $authentication)
    {
        try {
            return $this->client->request(
                'POST',
                self::WP_JSON_URL_PREFIX . 'pages/' . $pageId . '?'. $key .'=' . $value,

                [
                    'auth_basic' => $authentication
                ]
            );
        } catch (TransportExceptionInterface $tce) {
            $this->logger->error('Error: Meta data could not be posted. Reason: ' . $tce->getMessage());
        }
    }
}
