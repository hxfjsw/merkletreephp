<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use MerkleTreePhp\Buffer;
use MerkleTreePhp\MerkleTree;
use MerkleTreePhp\Options;
use Web3\Utils;

require __DIR__ . '/../vendor/autoload.php';

final class GenHashTest extends TestCase
{
    public function testGen(): void
    {

        $whitelistAddress = [
            '0x6dC0c0be4c8B2dFE750156dc7d59FaABFb5B923D',
            '0xa8d17cc9caf29af964d19267ddeb4dff122697b0'
        ];

        $leafNodes = array_map(fn($address) => Utils::sha3($address), $whitelistAddress);

        $options = new Options();
        $options->sortPairs = true;

        $hashFn = fn(Buffer $bf) => Buffer::fromHex(Utils::sha3('0x' . $bf->toHex()));

        $merkleTree = new MerkleTree($leafNodes, $hashFn, $options);

        $root = $merkleTree->getHexRoot();
        $this->assertEquals('0xdb44a1f32851683f64d15a563ecd3686b67de2075821b6196dbaf7d25604592f', $root);

        $leaf = $whitelistAddress[0];
        $this->assertEquals('0x6dC0c0be4c8B2dFE750156dc7d59FaABFb5B923D', $leaf);

        $proof = $merkleTree->getHexProof(Utils::sha3($leaf));


        $this->assertEquals(["0x7fa4f9a213fc25511745e0fe7627ab0d7145664238bd854fb781559c2ddbf9c4"], $proof);
    }

}