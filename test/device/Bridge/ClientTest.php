<?php

declare(strict_types=1);

namespace BitWasp\Test\Trezor\Device\Bridge;

use BitWasp\Test\Trezor\Device\TestCase;
use BitWasp\Trezor\Bridge\Client;
use BitWasp\Trezor\Bridge\Http\HttpClient;

class ClientTest extends TestCase
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var Client
     */
    private $client;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->httpClient = HttpClient::forUri("http://localhost:21325");
        $this->client = new Client($this->httpClient);
    }

    public function testBridgeVersion()
    {
        $version = $this->client->bridgeVersion();
        $this->assertEquals("2.0.12", $version->version());
    }

    public function testAcquireAndRelease()
    {
        $devices = $this->client->listDevices();
        $this->assertCount(2, $devices);
        $this->assertEquals("emulator21324", $devices->devices()[0]->getPath());
        $this->assertEquals(null, $devices->devices()[0]->getSession());

        $session = $this->client->acquire($devices->devices()[0]);
        $this->assertTrue($session->isActive());

        $session->release();
        $this->assertFalse($session->isActive());

        $devices = $this->client->listDevices();
        $this->assertCount(2, $devices);
        $this->assertEquals("emulator21324", $devices->devices()[0]->getPath());
        $this->assertEquals(null, $devices->devices()[0]->getSession());
    }
}
