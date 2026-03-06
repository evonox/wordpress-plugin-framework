<?php

namespace WPTests;

use __PLUGIN__\Extensions\CoreAPI\Interfaces\TransientsAPI;
use __PLUGIN__\Framework\DI\Container;
use PHPUnit\Framework\TestCase;

class TransientAPITest extends TestCase
{
    private TransientsAPI $api;

    public function setUp(): void
    {
        $this->api = Container::get()->resolve(TransientsAPI::class);
        if ($this->api->existsTransient("some_transient")) {
            $this->api->deleteTransient("some_transient");
        }
        if ($this->api->existsSiteTransient("some_transient")) {
            $this->api->deleteSiteTransient("some_transient");
        }
    }

    public function testTrasientNotExists()
    {
        $result = $this->api->getTransient("some_transient");
        $this->assertFalse($result);
        $result = $this->api->getSiteTransient("some_transient");
        $this->assertFalse($result);
    }

    public function testTransientIsReturned()
    {
        $this->api->setTransient("some_transient", ["a" => 12]);
        $result = $this->api->getTransient("some_transient");
        $this->assertEquals($result, ["a" => 12]);
        $result = $this->api->getSiteTransient("some_transient");
        $this->assertFalse($result);
    }

    public function testTransientIsDeleted()
    {
        $this->api->setTransient("some_transient", ["b" => 12]);
        $this->assertTrue($this->api->existsTransient("some_transient"));
        $this->api->deleteTransient("some_transient");
        $this->assertFalse($this->api->existsTransient("some_transient"));
    }

    public function testSiteTransientIsReturned()
    {
        $this->api->setSiteTransient("some_transient", ['a' => 12]);
        $result = $this->api->getSiteTransient('some_transient');
        $this->assertEquals($result, ['a' => 12]);
        $result = $this->api->getTransient('some_transient');
        $this->assertFalse($result);
    }

    public function testSiteTransientIsDeleted()
    {
        $this->api->setSiteTransient('some_transient', ['b' => 12]);
        $this->assertTrue($this->api->existsSiteTransient('some_transient'));
        $this->api->deleteSiteTransient('some_transient');
        $this->assertFalse($this->api->existsSiteTransient('some_transient'));
    }
}
