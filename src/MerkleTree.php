<?php

namespace MerkleTreePhp;


use Exception;

class MerkleTree extends Base
{

    private bool $duplicateOdd = false;
    private $hashFn = null;
    private bool $hashLeaves;
    private bool $isBitcoinTree;
    /** @var Buffer[] */
    private array $leaves = [];
    /** @var Buffer[] */
    private array $layers = [];

    private bool $sortLeaves;
    private bool $sortPairs;
    private bool $sort = false;

    private $fillDefaultHash = null;

    /**
     * @param string[] $leaves
     * @param callable $hashFn
     * @param Options $options
     * @throws Exception
     */
    public function __construct(array $leaves, callable $hashFn, Options $options)
    {
        parent::__construct();

        $this->isBitcoinTree = !!$options->isBitcoinTree;
        $this->hashLeaves = !!$options->hashLeaves;
        $this->sortLeaves = !!$options->sortLeaves;
        $this->sortPairs = !!$options->sortPairs;

        if ($options->fillDefaultHash) {

            if (is_callable($options->fillDefaultHash)) {
                $this->fillDefaultHash = $options->fillDefaultHash;
            } else if (Buffer::isBuffer($options->fillDefaultHash) || is_string($options->fillDefaultHash)) {
                //todo
            } else {
                throw new Exception('method "fillDefaultHash" must be a function, Buffer, or string');
            }
        }

        $this->sort = !!$options->sort;

        if ($this->sort) {
            $this->sortLeaves = true;
            $this->sortPairs = true;
        }

        $this->duplicateOdd = !!$options->duplicateOdd;

        $this->hashFn = $hashFn;

        $this->processLeaves($leaves);

    }

    private function processLeaves(array $leaves)
    {
        if ($this->hashLeaves) {
            $leaves = array_map($this->hashFn, $leaves);
        }

        $this->leaves = array_map(function ($leaf) {
            return $this->bufferify($leaf);
        }, $leaves);

        if ($this->sortLeaves) {
            sort($this->leaves);
        }

        if ($this->fillDefaultHash) {
            for ($i = 0; $i < pow(2, ceil(log(count($this->leaves, 2)))); $i++) {
                if ($i >= count($this->leaves)) {
                    $this->leaves[] = $this->bufferify(
                        call_user_func($this->fillDefaultHash, $i, $this->hashFn)
                    );
                }
            }
        }

        $this->layers = [$this->leaves];
        $this->_createHashes($this->leaves);

    }

    private function _createHashes(array $nodes)
    {
        while (count($nodes) > 1) {
            $layerIndex = count($this->layers);
            $this->layers[] = [];
            for ($i = 0; $i < count($nodes); $i += 2) {
                if ($i + 1 === count($nodes)) {
                    $data = $nodes[count($nodes) - 1];
                    if ($this->isBitcoinTree) {
                        $data = Buffer::concat([$data->reverse(), $data->reverse()]);
                        $hash = call_user_func($this->hashFn, $data);
                        $hash = $hash->reverse();
                        $this->layers[$layerIndex][] = $hash;
                        continue;
                    } else {
                        if ($this->duplicateOdd) {
                            // continue with creating layer
                        } else {
                            $this->layers[$layerIndex][] = $nodes[$i];
                            continue;
                        }
                    }
                }
                $left = $nodes[$i];
                $right = $i + 1 === count($nodes) ? $left : $nodes[$i + 1];
                $data = null;
                $combined = null;
                if ($this->isBitcoinTree) {
                    $combined = [$left->reverse(), $right->reverse()];
                } else {
                    $combined = [$left, $right];
                }

                if ($this->sortPairs) {
                    usort($combined, Buffer::class . '::compare');
                }
                $data = Buffer::concat($combined);
                $hash = call_user_func($this->hashFn, $data);
                $this->layers[$layerIndex][] = $hash;
            }
            $nodes = $this->layers[$layerIndex];
        }
    }

    public function getRoot(): Buffer
    {
        if (count($this->layers) === 0) {
            return Buffer::from([]);
        }
        return $this->layers[count($this->layers) - 1][0] ?? Buffer::from([]);
    }

    public function getHexRoot(): string
    {
        return $this->bufferToHex($this->getRoot());
    }

    /**
     * @throws Exception
     */
    public function getProof($leaf, $index): array
    {
        if ($leaf === null) {
            throw new Exception("leaf is required'");
        }

        $leaf = $this->bufferify($leaf);

        $proof = [];

        if ($index == null) {
            $index = -1;
            for ($i = 0; $i < count($this->leaves); $i++) {
                if (Buffer::compare($leaf, $this->leaves[$i]) === 0) {
                    $index = $i;
                }
            }
        }

        if ($index == -1) {
            return [];
        }

        for ($i = 0; $i < count($this->layers); $i++) {
            $layer = $this->layers[$i];
            $isRightNode = $index % 2;

            $pairIndex = ($isRightNode ? $index - 1 : ($this->isBitcoinTree && $index == count($layer) - 1 && $i < count($this->layers) - 1
                // Proof Generation for Bitcoin Trees
                ? $index
                // Proof Generation for Non-Bitcoin Trees
                : $index + 1));

            if ($pairIndex < count($layer)) {
                $proof[] = ([
                    "position" => $isRightNode ? 'left' : 'right',
                    "data" => $layer[$pairIndex]
                ]);
            }
            // set index to parent index
            $index = ($index / 2) | 0;
        }

        return $proof;
    }


    /**
     * @throws Exception
     */
    public function getHexProof(string $leaf, $index = null): array
    {
        $arr = $this->getProof($leaf, $index);
        return array_map(function ($item) {
            return $this->bufferToHex($item['data']);
        }, $arr);
    }

    /**
     * @return Buffer[]
     */
    public function getLeaves(): array
    {
        return $this->leaves;
    }

    /**
     * @param Buffer[] $leaves
     */
    public function setLeaves(array $leaves): void
    {
        $this->leaves = $leaves;
    }



}
