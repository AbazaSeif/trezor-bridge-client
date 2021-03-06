<?php

declare(strict_types=1);

namespace BitWasp\Test\Trezor\Bridge\Client;

use BitWasp\Test\Trezor\Bridge\Message\TestCase;
use BitWasp\Test\Trezor\MockHttpStack;
use BitWasp\Trezor\Bridge\Client;
use BitWasp\Trezor\Device\Message;
use BitWasp\TrezorProto\Features;
use BitWasp\TrezorProto\Initialize;
use BitWasp\TrezorProto\MessageType;
use GuzzleHttp\Psr7\Response;

class CallTest extends TestCase
{
    public function testMockCall()
    {
        $httpStack = new MockHttpStack(
            "http://localhost:21325",
            [],
            new Response(200, [], '0011000002450a11626974636f696e7472657a6f722e636f6d100118062000321836423635333939463643414335463943424430383045343738014000520e74657374696e672d7472657a6f725a240a07426974636f696e120342544318002080897a2805489ee4a22450e4dba224580168005a270a07546573746e6574120454455354186f2080ade20428c40148cf8fd621509487d621580168005a240a0542636173681203424348180020a0c21e2805489ee4a22450e4dba2245800600068015a260a084e616d65636f696e12034e4d4318342080ade204280548e2c8f60c50feb9f60c580068005a260a084c697465636f696e12034c544318302080b48913283248e2c8f60c50feb9f60c580168005a280a08446f6765636f696e1204444f4745181e208094ebdc03281648fd95eb17509887eb17580068005a220a0444617368120444415348184c20a08d06281048cca5f91750f8a5f917580068005a240a055a6361736812035a454318b83920c0843d28bd39489ee4a22450e4dba224580068005a2b0a0c426974636f696e20476f6c641203425447182620a0c21e2817489ee4a22450e4dba2245801604f68015a250a0844696769427974651203444742181e20a0c21e2805489ee4a22450e4dba224580168005a270a084d6f6e61636f696e12044d4f4e41183220c096b1022837489ee4a22450e4dba2245801680060016a14723cf295a72ce07b96047901bb8c2e461a2488f872207651b7caba5aae0cc1c65c8304f760396f77606cd3990c991598f0e22a81e0077800800100880100980100a00100')
        );

        $httpClient = $httpStack->getClient();
        $client = new Client($httpClient);

        $msg = new Message(MessageType::MessageType_Initialize(), new Initialize());
        $res = $client->call('2', $msg);

        $this->assertCount(1, $httpStack->getRequestLogs(), 'should perform all requests');

        $this->assertEquals(MessageType::MessageType_Features()->value(), $res->getType());
        $this->assertInstanceOf(Features::class, $res->getProto());
    }
}
