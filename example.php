<?php

use MerkleTreePhp\Buffer;
use MerkleTreePhp\MerkleTree;
use MerkleTreePhp\Options;
use phpseclib\Math\BigInteger;
use Web3\Utils;

require __DIR__ . '/vendor/autoload.php';

require __DIR__.'/tree.php';

echo "start\n";

$options = new Options();
$options->sortPairs = true;

$hashFn = fn(Buffer $bf) => Buffer::fromHex(Utils::sha3('0x' . $bf->toHex()));
$merkleTree = new MerkleTree([], $hashFn, $options);
$merkleTree->setLeaves($leaves);
$merkleTree->setLayers($layers);

$root = $merkleTree->getHexRoot();
echo "root: " . $root . PHP_EOL;

$leaf = '0xc7b673913e13035b725c553b8d72a41019a0b281ca716f502426bd2269d42e4e';//$whitelistAddress[0];
echo "leaf: " . $leaf . PHP_EOL;

$start_time = microtime(true);

$proof = $merkleTree->getHexProof($leaf);
echo "proof: " . json_encode($proof) . PHP_EOL;

echo "time used:" . microtime(true) - $start_time . " seconds";