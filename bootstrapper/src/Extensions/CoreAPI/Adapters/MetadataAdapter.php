<?php

namespace __PLUGIN__\Extensions\CoreAPI\Adapters;

use __PLUGIN__\Extensions\CoreAPI\Interfaces\MetadataAPI;

class MetadataAdapter extends AdapterBase implements MetadataAPI
{
    /**
     * @inheritDoc
     */
    public function addMetadata(
        string $metaType,
        int $objectId,
        string $metaKey,
        mixed $metaValue,
        bool $unique = false
    ): void {
        $metaKey = $this->prefixMetaKey($metaKey);
        $result = add_metadata($metaType, $objectId, $metaKey, $metaValue, $unique);
        self::verify($result, "Failed to add metadata: type={$metaType}, objectId={$objectId}, metaKey={$metaKey}");
    }

    /**
     * @inheritDoc
     */
    public function deleteAllMetadata(string $metaType, string $metaKey): void
    {
        $metaKey = $this->prefixMetaKey($metaKey);
        $result = delete_metadata($metaType, 0, $metaKey, '', true);
        self::verify($result, "Failed to delete all metadata: type={$metaType}, metaKey={$metaKey}");
    }

    /**
     * @inheritDoc
     */
    public function deleteMetadata(string $metaType, int $objectId, string $metaKey, mixed $metaValue = null): void
    {
        $metaKey = $this->prefixMetaKey($metaKey);
        $result = delete_metadata($metaType, $objectId, $metaKey, $metaValue);
        self::verify($result, "Failed to delete metadata: type={$metaType}, objectId={$objectId}, metaKey={$metaKey}");
    }

    /**
     * @inheritDoc
     */
    public function getAllMetadata(string $metaType, int $objectId, string $metaKey): array
    {
        $metaKey = $this->prefixMetaKey($metaKey);
        $result = get_metadata($metaType, $objectId, $metaKey, false);
        self::verify($result, "Failed to get all metadata: type={$metaType}, objectId={$objectId}, metaKey={$metaKey}");
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata(string $metaType, int $objectId, string $metaKey): mixed
    {
        $metaKey = $this->prefixMetaKey($metaKey);
        $result = get_metadata($metaType, $objectId, $metaKey, true);
        self::verify(
            $result,
            "Failed to get metadata: type={$metaType}, objectId={$objectId}, metaKey={$metaKey}"
        );
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function hasMetadata(string $metaType, int $objectId, string $metaKey): bool
    {
        $metaKey = $this->prefixMetaKey($metaKey);
        return metadata_exists($metaType, $objectId, $metaKey);
    }

    /**
     * @inheritDoc
     */
    public function prefixMetaKey(string $metaKey): string
    {
        return $this->prefixName($metaKey);
    }

    /**
     * @inheritDoc
     */
    public function updateMetadata(string $metaType, int $objectId, string $metaKey, mixed $metaValue): void
    {
        $metaKey = $this->prefixMetaKey($metaKey);
        $result = update_metadata($metaType, $objectId, $metaKey, $metaValue);
        self::verify($result, "Failed to update metadata: type={$metaType}, objectId={$objectId}, metaKey={$metaKey}");
    }
}
