<?php

namespace MerkleTreePhp;


class Base
{


    public function __construct()
    {
    }

    public function bufferToHex(Buffer $value): string
    {
        return '0x' . $value->toHex();
    }

    public function bufferify(string $value): Buffer
    {
        return Buffer::fromHex($value);
    }


}