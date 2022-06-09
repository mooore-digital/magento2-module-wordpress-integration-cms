<?php

declare(strict_types=1);

namespace Mooore\WordpressIntegrationCms\Api\Data;

interface WordpressPostInterface
{
    /**
     * String constants for property names
     */
    const WORDPRESS_POST_ID = "wordpress_post_id";
    const SITE_ID = "site_id";
    const POST_ID = "post_id";
    const SLUG = "slug";

    /**
     * Getter for WordpressPostId.
     *
     * @return int|null
     */
    public function getWordpressPostId(): ?int;

    /**
     * Setter for WordpressPostId.
     *
     * @param int|null $wordpressPostId
     *
     * @return void
     */
    public function setWordpressPostId(?int $wordpressPostId): void;


    /**
     * Get site_id
     *
     * @return int|null
     */
    public function getSiteId(): ?int;

    /**
     * Setter site_id
     *
     * @param int|null $siteId
     *
     * @return void
     */
    public function setSiteId(?int $siteId): void;

    /**
     * Getter for post_id.
     *
     * @return int|null
     */
    public function getPostId(): ?int;

    /**
     * Setter for post_id.
     *
     * @param int|null $postId
     *
     * @return void
     */
    public function setPostId(?int $postId): void;

    /**
     * Getter for slug.
     *
     * @return string|null
     */
    public function getSlug(): string;

    /**
     * Setter for slug.
     *
     * @param string|null $slug
     *
     * @return void
     */
    public function setSlug(string $slug): void;
}
