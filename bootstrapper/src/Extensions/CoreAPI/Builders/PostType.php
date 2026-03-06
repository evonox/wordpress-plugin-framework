<?php

namespace __PLUGIN__\Extensions\CoreAPI\Builders;

use __PLUGIN__\Extensions\CoreAPI\Exceptions\RuntimeApiException;
use __PLUGIN__\Extensions\CoreAPI\Interfaces\PostTypeBuilder;

class PostType implements PostTypeBuilder
{
    private string $post_type;

    /**
     * @var array<string, mixed>
     */
    private array $args = [];

    public function __construct(string $post_type)
    {
        $this->post_type = sanitize_key($post_type);
    }

    public static function name(string $post_type): PostTypeBuilder
    {
        return new PostType($post_type);
    }

    public function register(): void
    {
        $result = register_post_type($this->post_type, $this->args);
        if (is_wp_error($result)) {
            throw new RuntimeApiException("Failed to register custom post type: " . $result->get_error_message());
        }
    }

    /**
     * @inheritDoc
     */
    public function label(string $label): self
    {
        $this->args["label"] = $label;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function additionalArgs(array $args): self
    {
        $this->args = array_merge($this->args, $args);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function description(string $description): self
    {
        $this->args["description"] = $description;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function excludeFromSearch(bool $flag = true): self
    {
        $this->args["exclude_from_search"] = $flag;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isHierarchical(bool $flag = true): self
    {
        $this->args["hierarchical"] = $flag;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isPublic(bool $flag = true): self
    {
        $this->args["public"] = $flag;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isQueryable(bool $flag = true): self
    {
        $this->args["publicly_queryable"] = $flag;
        return $this;
    }
}
