<?php

namespace __PLUGIN__\Extensions\CoreAPI\Adapters;

use __PLUGIN__\Extensions\CoreAPI\Interfaces\TransientsAPI;

class TransientsAdapter extends AdapterBase implements TransientsAPI
{
    /**
     * @inheritDoc
     */
    public function deleteSiteTransient(string $transientName): void
    {
        $transientName = $this->prefixTransientName($transientName);
        self::verify(
            delete_site_transient($transientName),
            "Failed to delete site transient: {$transientName}"
        );
    }

    /**
     * @inheritDoc
     */
    public function deleteTransient(string $transientName): void
    {
        $transientName = $this->prefixTransientName($transientName);
        self::verify(
            delete_transient($transientName),
            "Failed to delete transient: {$transientName}"
        );
    }

    /**
     * @inheritDoc
     */
    public function getSiteTransient(string $transientName): mixed
    {
        $transientName = $this->prefixTransientName($transientName);
        return get_site_transient($transientName);
    }

    /**
     * @inheritDoc
     */
    public function getTransient(string $transientName): mixed
    {
        $transientName = $this->prefixTransientName($transientName);
        return get_transient($transientName);
    }

    /**
     * @inheritDoc
     */
    public function setSiteTransient(string $transientName, mixed $value, int $expirationInSeconds = 0): void
    {
        $transientName = $this->prefixTransientName($transientName);
        self::verify(
            set_site_transient($transientName, $value, $expirationInSeconds),
            "Failed to set site transient: {$transientName}"
        );
    }

    /**
     * @inheritDoc
     */
    public function setTransient(string $transientName, mixed $value, int $expirationInSeconds = 0): void
    {
        $transientName = $this->prefixTransientName($transientName);
        self::verify(
            set_transient($transientName, $value, $expirationInSeconds),
            "Failed to set transient: {$transientName}"
        );
    }

    /**
     * @inheritDoc
     */
    public function prefixTransientName(string $transientName): string
    {
        if (str_starts_with($transientName, self::NO_PREFIXING_TOKEN)) {
            return substr($transientName, strlen(self::NO_PREFIXING_TOKEN));
        }
        return $this->pluginPrefix . "_" . $transientName;
    }
}
