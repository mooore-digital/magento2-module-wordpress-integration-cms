<?php

namespace Mooore\WordpressIntegrationCms\Model\Data;

use Magento\Framework\DataObject;
use Mooore\WordpressIntegrationCms\Api\Data\WordpressPostInterface;

class WordpressPostData extends DataObject implements WordpressPostInterface
{
    /**
     * Getter for WordpressPostId.
     *
     * @return int|null
     */
    public function getWordpressPostId(): ?int
    {
        return $this->getData(self::WORDPRESS_POST_ID) === null ? null
            : (int)$this->getData(self::WORDPRESS_POST_ID);
    }

    /**
     * Setter for WordpressPostId.
     *
     * @param int|null $wordpressPostId
     *
     * @return void
     */
    public function setWordpressPostId(?int $wordpressPostId): void
    {
        $this->setData(self::WORDPRESS_POST_ID, $wordpressPostId);
    }

    /**
     * Get site_id
     *
     * @return int|null
     */
    public function getSiteId(): ?int
    {
        return $this->getData(self::SITE_ID) === null ? null
            : (int)$this->getData(self::SITE_ID);
    }

    /**
     * Set site_id
     *
     * @param int|null $siteId
     *
     * @return void
     */
    public function setSiteId(?int $siteId): void
    {
        $this->setData(self::SITE_ID, $siteId);
    }

    /**
     * Get post_id
     *
     * @return int|null
     */
    public function getPostId(): ?int
    {
        return $this->getData(self::POST_ID) === null ? null
            : (int)$this->getData(self::POST_ID);
    }

    /**
     * Set post_id
     *
     * @param int|null $postId
     *
     * @return void
     */
    public function setPostId(?int $postId): void
    {
        $this->setData(self::POST_ID, $postId);
    }

    /**
     * Get slug
     *
     * @return string|null
     */
    public function getSlug(): string
    {
        return $this->getData(self::SLUG) === null ? null
            : $this->getData(self::SLUG);
    }

    /**
     * Set slug
     *
     * @param slug|null $slug
     *
     * @return void
     */
    public function setSlug(string $slug): void
    {
        $this->setData(self::SLUG, $slug);
    }
}
