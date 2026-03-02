<?php

use __PLUGIN__\Extensions\MigrationFramework\Common\MigrationRegistry;
use __PLUGIN__\Extensions\MigrationFramework\Common\MigrationRepository;
use __PLUGIN__\Extensions\MigrationFramework\DBMigrationEngine;
use __PLUGIN__\Framework\DI\Container;

if (!defined('ABSPATH')) {
    exit;
}

Container::get()->bind(MigrationRepository::class)->toSelf()->isSingleScoped();
Container::get()->bind(MigrationRegistry::class)->toSelf()->isSingleScoped();
Container::get()->bind(DBMigrationEngine::class)->toSelf()->isSingleScoped();
