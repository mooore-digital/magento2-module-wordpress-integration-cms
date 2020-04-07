<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Model\HttpClient;

use Symfony\Component\HttpClient\HttpClient;

class Page
{
    /**
     * @var \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    private $client;

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
        $totalPages = (int) $peekHeaders['x-wp-total'][0] ?? 0;
        $pageNumber = 1;
        $pageCount = 0;

        while ($pageCount < $totalPages) {
            $response = $this->client->request(
                'GET',
                '/wp-json/wp/v2/pages?' . http_build_query([
                    'page' => $pageNumber++,
                    'per_page' => $pageSize
                ])
            );

            foreach (json_decode($response->getContent(), true) as $page) {
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
        $response = $this->client->request('GET', '/wp-json/wp/v2/pages/' . $id);

        return json_decode($response->getContent(), true);
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
        $peekResponse = $this->client->request('HEAD', '/wp-json/wp/v2/pages?per_page=' . $pageSize);

        return $peekResponse->getHeaders();
    }
}
