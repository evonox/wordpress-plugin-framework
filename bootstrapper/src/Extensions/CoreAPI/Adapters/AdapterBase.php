<?php

namespace __PLUGIN__\Extensions\CoreAPI\Adapters;

use __PLUGIN__\Framework\Attributes\Inject;
use Exception;

abstract class AdapterBase
{
    #[Inject("PluginPrefix")]
    public string $pluginPrefix = "";
    protected const string NO_PREFIXING_TOKEN = "::";

    protected static function verify(mixed $result, string $errorMessage): void
    {
        if ($result === false) {
            throw new Exception($errorMessage);
        }
    }

    protected function prefixName(string $name): string
    {
        if (str_starts_with($name, self::NO_PREFIXING_TOKEN)) {
            return substr($name, strlen(self::NO_PREFIXING_TOKEN));
        }
        return $this->pluginPrefix . "_" . $name;
    }
}
