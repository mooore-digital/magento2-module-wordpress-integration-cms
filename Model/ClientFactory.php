<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ClientFactory
{
    /**
     * @var HttpClientInterface[]|array
     */
    private $instanceCache = [];

    public function get(string $baseUri): HttpClientInterface
    {
        $baseUri = trim($baseUri, '/');

        if (!array_key_exists($baseUri, $this->instanceCache)) {
            $this->instanceCache[$baseUri] = HttpClient::createForBaseUri($baseUri, [
                'headers' => [
                    'User-Agent' => 'Magento Wordpress Integration Client'
                ]
            ]);
        }

        return $this->instanceCache[$baseUri];
    }
}
