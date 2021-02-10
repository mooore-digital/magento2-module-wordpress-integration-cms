<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model\HttpClient;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Page
{
    const WP_JSON_URL_PREFIX = '/wp-json/wp/v2/';

    /**
     * @var \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    private $client;

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
     */
    public function all(int $pageSize = 10): \Generator
    {
        $peekHeaders = $this->peek($pageSize);

        if (empty($peekHeaders['x-wp-total'])) {
            return false;
        }

        $totalPages = (int) $peekHeaders['x-wp-total'][0] ?? 0;
        $pageNumber = 1;
        $pageCount = 0;

        while ($pageCount < $totalPages) {
            $response = $this->client->request(
                'GET',
                self::WP_JSON_URL_PREFIX . 'pages?' . http_build_query([
                    'page' => $pageNumber++,
                    'per_page' => $pageSize
                ])
            );

            foreach (json_decode($response->getContent(), true) as $page) {
                $page['modified_date_formatted'] = date('j M y', strtotime($page['modified']));
                yield $page;
                $pageCount++;
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
     */
    public function get(int $id): array
    {
        $response = $this->client->request('GET', self::WP_JSON_URL_PREFIX . 'pages/' . $id);

        return json_decode($response->getContent(), true);
    }

    public function postMetaDataToPage(int $pageId, string $key, string $value, string $authentication)
    {
        try {
            $response = $this->client->request(
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

    /**
     * Peek pages headers. Useful for listing.
     *
     * @param int $pageSize
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function peek(int $pageSize): array
    {
        $peekResponse = $this->client->request('HEAD', self::WP_JSON_URL_PREFIX . 'pages?per_page=' . $pageSize);

        return $peekResponse->getHeaders();
    }
}
