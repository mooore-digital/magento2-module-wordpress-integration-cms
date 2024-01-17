<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model\HttpClient;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Mooore\WordpressIntegrationCms\Exception\HTTPClientTypeRequiredException;

abstract class Wordpress
{
    const WP_JSON_URL_PREFIX = '/wp-json/wp/v2/';

    public ?string $type = null;

    /**
     * @var \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    public $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Set base url of client. This has to be done before querying any data.
     *
     * @param string $baseUrl
     */
    public function setBaseUrl(string $baseUrl)
    {
        $baseUrl = trim($baseUrl, '/');

        $this->client = HttpClient::createForBaseUri($baseUrl, [
            'headers' => [
                'User-Agent' => 'Magento Wordpress Integration Client'
            ]
        ]);
    }

    /**
     * Get all pages.
     *
     * @param int $pageSize
     * @return \Generator
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws HTTPClientTypeRequiredException
     */
    public function all(int $pageSize = 10, array $filters = []): \Generator
    {
        if (is_null($this->type)) {
            throw new HTTPClientTypeRequiredException("Type is required for HTTPClient");
        }
        $peekHeaders = $this->peek($pageSize);

        if (empty($peekHeaders['x-wp-totalpages'])) {
            return false;
        }

        $totalPages = (int) $peekHeaders['x-wp-totalpages'][0] ?? 0;
        $pageNumber = 1;

        while ($pageNumber <= $totalPages) {
            var_dump($pageNumber. '/' .$totalPages);
            $response = $this->client->request(
                'GET',
                static::WP_JSON_URL_PREFIX .
                $this->type .
                '?' .
                http_build_query(
                    array_merge([
                        'page' => $pageNumber++,
                        'per_page' => $pageSize
                    ], $filters)
                )
            );

            foreach (json_decode($response->getContent(), true) as $page) {
                yield $page;
            }
        }
    }

    /**
     * Get page by ID.
     *
     * @param int $id
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws HTTPClientTypeRequiredException
     */
    public function get(int $id): array
    {
        if (is_null($this->type)) {
            throw new HTTPClientTypeRequiredException("Type is required for HTTPClient");
        }

        $response = $this->client->request('GET', static::WP_JSON_URL_PREFIX . $this->type . '/' . $id);

        return json_decode($response->getContent(), true);
    }

    /**
     * @throws HTTPClientTypeRequiredException
     */
    public function postMetaDataToPage(int $pageId, string $key, string $value, string $authentication)
    {
        if (is_null($this->type)) {
            throw new HTTPClientTypeRequiredException("Type is required for HTTPClient");
        }

        try {
            $response = $this->client->request(
                'POST',
                static::WP_JSON_URL_PREFIX . $this->type . '/' . $pageId . '?' . $key . '=' . $value,

                [
                    'auth_basic' => $authentication
                ]
            );
        } catch (TransportExceptionInterface $tce) {
            $this->logger->error('Error: Meta data could not be posted. Reason: ' . $tce->getMessage());
        }
    }

    /**
     * Peek pages headers. Useful for listing.
     *
     * @param int $pageSize
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws HTTPClientTypeRequiredException
     */
    public function peek(int $pageSize): array
    {
        if (is_null($this->type)) {
            throw new HTTPClientTypeRequiredException("Type is required for HTTPClient");
        }

        $peekResponse = $this->client->request('HEAD', static::WP_JSON_URL_PREFIX . $this->type . '?per_page=' . $pageSize);

        return $peekResponse->getHeaders();
    }
}
