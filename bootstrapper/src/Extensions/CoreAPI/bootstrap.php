<?php

use __PLUGIN__\Extensions\CoreAPI\Adapters\DatabaseAdapter;
use __PLUGIN__\Extensions\CoreAPI\Adapters\OptionsAdapter;
use __PLUGIN__\Extensions\CoreAPI\Interfaces\DatabaseAPI;
use __PLUGIN__\Extensions\CoreAPI\Interfaces\OptionsAPI;
use __PLUGIN__\Extensions\CoreAPI\Interfaces\TransientsAPI;
use __PLUGIN__\Extensions\CoreAPI\Interfaces\MetadataAPI;
use __PLUGIN__\Extensions\CoreAPI\Adapters\TransientsAdapter;
use __PLUGIN__\Extensions\CoreAPI\Adapters\MetadataAdapter;
use __PLUGIN__\Framework\DI\Container;

if (!defined('ABSPATH')) {
    exit;
}

Container::get()->bind(DatabaseAPI::class)->toClass(DatabaseAdapter::class)->isSingleScoped();
Container::get()->bind(OptionsAPI::class)->toClass(OptionsAdapter::class)->isSingleScoped();
Container::get()->bind(TransientsAPI::class)->toClass(TransientsAdapter::class)->isSingleScoped();
Container::get()->bind(MetadataAPI::class)->toClass(MetadataAdapter::class)->isSingleScoped();
