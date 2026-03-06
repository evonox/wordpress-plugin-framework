<?php

namespace WPTests;

use __PLUGIN__\Extensions\CoreAPI\Interfaces\OptionsAPI;
use __PLUGIN__\Framework\DI\Container;
use PHPUnit\Framework\TestCase;

class OptionsAPITest extends TestCase
{
    private OptionsAPI $api;

    public function setUp(): void
    {
        $this->api = Container::get()->resolve(OptionsAPI::class);
        if ($this->api->hasOption("option_name")) {
            $this->api->deleteOption("option_name");
        }
        if ($this->api->hasSiteOption("option_name")) {
            $this->api->deleteSiteOption("option_name");
        }
    }

    public function testOptionsDoNotExist(): void
    {
        $this->assertFalse($this->api->hasOption("option_name"));
        $this->assertFalse($this->api->hasSiteOption("option_name"));
    }

    public function testOptionIsAdded(): void
    {
        $this->assertFalse($this->api->hasOption("option_name"));
        $this->api->setOption("option_name", "Hello");
        $this->assertEquals($this->api->getOption("option_name"), "Hello");
    }

    public function testOptionIsUpdated(): void
    {
        $this->assertFalse($this->api->hasOption("option_name"));
        $this->api->setOption("option_name", "Hello");
        $this->assertEquals($this->api->getOption("option_name"), "Hello");
        $this->api->setOption("option_name", "Hello2");
        $this->assertEquals($this->api->getOption("option_name"), "Hello2");
    }

    public function testOptionIsDeleted(): void
    {
        $this->assertFalse($this->api->hasOption("option_name"));
        $this->api->setOption("option_name", "value");
        $this->assertTrue($this->api->hasOption("option_name"));
        $this->api->deleteOption("option_name");
        $this->assertFalse($this->api->hasOption("option_name"));
    }

    public function testSiteOptionIsAdded(): void
    {
        $this->assertFalse($this->api->hasSiteOption("option_name"));
        $this->api->setSiteOption("option_name", "Hello");
        $this->assertEquals($this->api->getSiteOption("option_name"), "Hello");
    }

    public function testSiteOptionIsUpdated(): void
    {
        $this->assertFalse($this->api->hasSiteOption("option_name"));
        $this->api->setSiteOption("option_name", "Hello");
        $this->assertEquals($this->api->getSiteOption("option_name"), "Hello");
        $this->api->setSiteOption("option_name", "Hello2");
        $this->assertEquals($this->api->getSiteOption("option_name"), "Hello2");
    }

    public function testSiteOptionIsDeleted(): void
    {
        $this->assertFalse($this->api->hasSiteOption("option_name"));
        $this->api->setSiteOption("option_name", "value");
        $this->assertTrue($this->api->hasSiteOption("option_name"));
        $this->api->deleteSiteOption("option_name");
        $this->assertFalse($this->api->hasSiteOption("option_name"));
    }
}
