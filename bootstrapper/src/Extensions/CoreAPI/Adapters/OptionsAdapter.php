<?php

namespace __PLUGIN__\Extensions\CoreAPI\Adapters;

use __PLUGIN__\Extensions\CoreAPI\Interfaces\OptionsAPI;

class OptionsAdapter extends AdapterBase implements OptionsAPI
{
    /**
     * @inheritDoc
     */
    public function deleteOption(string $optionName): void
    {
        $optionName = $this->prefixOptionName($optionName);
        self::verify(delete_option($optionName), "Failed to delete option: $optionName");
    }

    /**
     * @inheritDoc
     */
    public function deleteSiteOption(string $optionName): void
    {
        $optionName = $this->prefixOptionName($optionName);
        self::verify(delete_site_option($optionName), "Failed to delete site option: $optionName");
    }

    /**
     * @inheritDoc
     */
    public function getOption(string $optionName, mixed $default = null): mixed
    {
        $optionName = $this->prefixOptionName($optionName);
        return get_option($optionName, $default);
    }

    /**
     * @inheritDoc
     */
    public function getSiteOption(string $optionName, mixed $default = null): mixed
    {
        $optionName = $this->prefixOptionName($optionName);
        return get_site_option($optionName, $default);
    }

    /**
     * @inheritDoc
     */
    public function hasOption(string $optionName): bool
    {
        $optionName = $this->prefixOptionName($optionName);
        return get_option($optionName) !== false;
    }

    /**
     * @inheritDoc
     */
    public function hasSiteOption(string $optionName): bool
    {
        $optionName = $this->prefixOptionName($optionName);
        return get_site_option($optionName) !== false;
    }

    /**
     * @inheritDoc
     */
    public function prefixOptionName(string $optionName): string
    {
        if (str_starts_with($optionName, self::NO_PREFIXING_TOKEN)) {
            return substr($optionName, strlen(self::NO_PREFIXING_TOKEN));
        }
        return $this->pluginPrefix . "_" . $optionName;
    }

    /**
     * @inheritDoc
     */
    public function setOption(string $optionName, mixed $value): void
    {
        $prefixedOptionName = $this->prefixOptionName($optionName);
        if ($this->hasOption($optionName)) {
            self::verify(update_option($prefixedOptionName, $value), "Failed to update option: $prefixedOptionName");
        } else {
            self::verify(add_option($prefixedOptionName, $value), "Failed to add option: $prefixedOptionName");
        }
    }

    /**
     * @inheritDoc
     */
    public function setSiteOption(string $optionName, mixed $value): void
    {
        $prefixedOptionName = $this->prefixOptionName($optionName);
        if ($this->hasSiteOption($optionName)) {
            self::verify(
                update_site_option($prefixedOptionName, $value),
                "Failed to update site option: $prefixedOptionName"
            );
        } else {
            self::verify(
                add_site_option($prefixedOptionName, $value),
                "Failed to add site option: $prefixedOptionName"
            );
        }
    }
}
