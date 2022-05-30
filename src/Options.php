<?php

namespace MerkleTreePhp;

class Options
{
    /** If set to `true`, an odd node will be duplicated and combined to make a pair to generate the layer hash. */
    public ?bool $duplicateOdd = false;
    /** If set to `true`, the leaves will hashed using the set hashing algorithms. */
    public ?bool $hashLeaves = false;
    /** If set to `true`, constructs the Merkle Tree using the [Bitcoin Merkle Tree implementation](http://www.righto.com/2014/02/bitcoin-mining-hard-way-algorithms.html). Enable it when you need to replicate Bitcoin constructed Merkle Trees. In Bitcoin Merkle Trees, single nodes are combined with themselves, and each output hash is hashed again. */
    public ?bool $isBitcoinTree = false;
    /** If set to `true`, the leaves will be sorted. */
    public ?bool $sortLeaves = false;
    /** If set to `true`, the hashing pairs will be sorted. */
    public ?bool $sortPairs = false;
    /** If set to `true`, the leaves and hashing pairs will be sorted. */
    public ?bool $sort = false;

    /**
     * If defined, the resulting hash of this function will be used to fill in odd numbered layers.
     * @var Callable | Buffer | string
     */
    public mixed $fillDefaultHash = null;
}