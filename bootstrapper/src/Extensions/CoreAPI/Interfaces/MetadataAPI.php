<?php

namespace __PLUGIN__\Extensions\CoreAPI\Interfaces;

interface MetadataAPI
{
    public function hasMetadata(string $metaType, int $objectId, string $metaKey): bool;
    public function getMetadata(string $metaType, int $objectId, string $metaKey): mixed;
    /**
     * @return array<mixed>
     */
    public function getAllMetadata(string $metaType, int $objectId, string $metaKey): array;
    public function addMetadata(
        string $metaType,
        int $objectId,
        string $metaKey,
        mixed $metaValue,
        bool $unique = false
    ): void;
    public function updateMetadata(string $metaType, int $objectId, string $metaKey, mixed $metaValue): void;
    public function deleteMetadata(string $metaType, int $objectId, string $metaKey, mixed $metaValue = null): void;
    public function deleteAllMetadata(string $metaType, string $metaKey): void;
    public function prefixMetaKey(string $metaKey): string;
}
