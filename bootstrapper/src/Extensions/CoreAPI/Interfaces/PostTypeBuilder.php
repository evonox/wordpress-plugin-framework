<?php

namespace __PLUGIN__\Extensions\CoreAPI\Interfaces;

/**
 * 1. LABELS
 * 2. UI CONFIG
 * 3. REST ACCESS
 * 4. MENU POSITION
 * 5. CONFIG
 * 6. REWRITE
 */
interface PostTypeBuilder
{
    public function register(): void;
    public function label(string $label): self;
    public function description(string $description): self;
    public function isPublic(bool $flag = true): self;
    public function isHierarchical(bool $flag = true): self;
    public function excludeFromSearch(bool $flag = true): self;
    public function isQueryable(bool $flag = true): self;
    /**
     * @param array<string, mixed> $args
     */
    public function additionalArgs(array $args): self;
}
