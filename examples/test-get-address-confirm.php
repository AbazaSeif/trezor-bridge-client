<?php

declare(strict_types=1);

use BitWasp\Trezor\Device\Command\GetAddressService;
use BitWasp\Trezor\Device\Command\InitializeService;
use BitWasp\Trezor\Device\UserInput\CurrentPinInput;
use BitWasp\Trezor\Device\RequestFactory;
use BitWasp\Trezor\Device\UserInput\CommandLineUserInputRequest;
use BitWasp\Trezor\Device\Util;

require __DIR__ . "/../vendor/autoload.php";

$useNetwork = "BTC";

$httpClient = HttpClient::forUri("http://localhost:21325");
$trezor = new Client($httpClient);

echo "list devices\n";
$devices = $trezor->listDevices();
if (empty($devices)) {
    throw new \Exception("Error! No devices connected!");
}

echo "first device\n";
$firstDevice = $devices->devices()[0];

print_r($firstDevice);

echo "acquire device!\n";
$session = $trezor->acquire($firstDevice);
$sessionId = $session->getSessionId();
$reqFactory = new RequestFactory();

echo "sessionId: {$sessionId}\n";
echo "devicePath: {$session->getDevice()->getPath()}\n";

$initializeCmd = new InitializeService();
$features = $initializeCmd->call($session, $reqFactory->initialize());

if (!($btcNetwork = Util::networkByCoinShortcut($useNetwork, $features))) {
    throw new \RuntimeException("Failed to find requested network ({$useNetwork})");
}

$currentPinInput = new CurrentPinInput(new CommandLineUserInputRequest());
$addressService = new GetAddressService();
$getAddress = $reqFactory->getP2shWitnessKeyHashAddress($btcNetwork->getCoinName(), [1], true);
$address = $addressService->call($session, $currentPinInput, $getAddress);

var_dump($address);

$session->release();
