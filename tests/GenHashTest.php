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
            '0xa8d17cc9caf29af964d19267ddeb4dff122697b0',
            '0x9eca64466f257793eaa52fcfff5066894b76a149',
            '0x7ffc57839b00206d1ad20c69a1981b489f772031',
            '0xfe3b557e8fb62b89f4916b721be55ceb828dbd73',
            '0xa49fbcd6f175d8fe0d2642d580c794f82c04ebe1'
        ];

        $leafNodes = array_map(fn($address) => Utils::sha3($address), $whitelistAddress);

        $options = new Options();
        $options->sortPairs = true;

        $hashFn = fn(Buffer $bf) => Buffer::fromHex(Utils::sha3('0x' . $bf->toHex()));

        $merkleTree = new MerkleTree($leafNodes, $hashFn, $options);

        $root = $merkleTree->getHexRoot();
        $this->assertEquals('0x2922fd8b8abc042cc2dc1f1baa730413d730bbc1a6b121c6c583c1fe60d5d701', $root);

        $leaf = $whitelistAddress[0];
        $this->assertEquals('0x6dC0c0be4c8B2dFE750156dc7d59FaABFb5B923D', $leaf);

        $proof = $merkleTree->getHexProof(Utils::sha3($leaf));


        $this->assertEquals(
            [
                "0x7fa4f9a213fc25511745e0fe7627ab0d7145664238bd854fb781559c2ddbf9c4",
                "0x93e81a62b88a1c72da1ba89088622ce8e0e47054340132e6779e46b24c9d3c3c",
                "0xafa5e48ddc901cb3ccc1bae2c19891b338ea8f192a5857ead3796d1dd858bb48"
            ],
            $proof);
    }

}