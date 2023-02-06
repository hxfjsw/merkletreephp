<?php


use MerkleTreePhp\Buffer;
use MerkleTreePhp\MerkleTree;
use MerkleTreePhp\Options;
use phpseclib\Math\BigInteger;
use Web3\Utils;

require __DIR__ . '/vendor/autoload.php';

$whitelistAddress = [
//    '0x5B38Da6a701c568545dCfcB03FcB875f56beddC4' . Utils::toHex(json_encode(["in" => 100, "out" => 100])),
//    '0x5B38Da6a701c568545dCfcB03FcB875f56beddC5' . Utils::toHex(json_encode(["in" => 100, "out" => 100])),
//    '0x5B38Da6a701c568545dCfcB03FcB875f56beddC6' . Utils::toHex(json_encode(["in" => 100, "out" => 100])),
];

//生成mock数据
var_dump(base64_encode(json_encode(["in" => 10, "out" => 100])));
for ($i = 0; $i <= 50000; $i++) {
    $addr = new BigInteger("0x5B38Da6a701c568545dCfcB03FcB875f56beddC4", 16);
    $addr = $addr->add(new BigInteger($i));
//    $whitelistAddress[] = '0x' . $addr->toHex();
    $content = '0x' . $addr->toHex() . Utils::toHex(base64_encode(json_encode(["in" => 10, "out" => 100])));
    $whitelistAddress[] = Utils::sha3($content);
//    $whitelistAddress[]=$content;
}
//var_dump($whitelistAddress);

file_put_contents("mock.php","<?php\n \$whitelistAddress= " . var_export($whitelistAddress,true).";");
