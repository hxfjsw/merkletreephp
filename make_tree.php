<?php


use MerkleTreePhp\Buffer;
use MerkleTreePhp\MerkleTree;
use MerkleTreePhp\Options;
use Web3\Utils;

require __DIR__ . '/vendor/autoload.php';

require __DIR__.'/mock.php';

echo "start make_tree\n";

$options = new Options();
$options->sortPairs = true;

$hashFn = fn(Buffer $bf) => Buffer::fromHex(Utils::sha3('0x' . $bf->toHex()));
$merkleTree = new MerkleTree($whitelistAddress, $hashFn, $options);

file_put_contents("leaves.php","<?php\n \$leaves= " . var_export($merkleTree->getLeaves(),true).";");
