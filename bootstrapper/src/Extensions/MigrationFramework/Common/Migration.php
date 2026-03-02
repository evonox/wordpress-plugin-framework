<?php

namespace __PLUGIN__\Extensions\MigrationFramework\Common;

class Migration
{
    public function __construct(
        public string $pluginName,
        public string $pluginVersion,
        public int $migrationOrder,
        public string $className
    ) {
    }
}
