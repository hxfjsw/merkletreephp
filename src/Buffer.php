<?php

namespace MerkleTreePhp;

class Buffer
{

    public array $buffer = [];

    public function length(): int
    {
        return count($this->buffer);
    }

    public function __construct($hexString, $type = 'hex')
    {
        if($hexString==''){

        }else {
            $this->buffer = match ($type) {
                'hex' => array_map('hexdec', str_split($hexString, 2)),
                'empty' => [],
            };
        }
    }

    public static function compare(Buffer $a, Buffer $b): int
    {
        if ($a->buffer === $b->buffer) return 0;

        $x = $a->length();
        $y = $b->length();

        for ($i = 0, $len = min($x, $y); $i < $len; ++$i) {
            if ($a->buffer[$i] !== $b->buffer[$i]) {
                $x = $a->buffer[$i];
                $y = $b->buffer[$i];
                break;
            }
        }

        if ($x < $y) return -1;
        if ($y < $x) return 1;
        return 0;
    }

    public static function concat(array $combined): Buffer
    {
        $buffer = Buffer::from([]);
        foreach ($combined as $item) {
            $buffer->buffer = array_merge($buffer->buffer, $item->buffer);
        }

        return $buffer;
    }

    public static function from(array $array): Buffer
    {
        $buffer = new Buffer('', 'empty');
        $buffer->buffer = $array;
        return $buffer;
    }

    public static function fromHex(string $hexString): Buffer
    {
        return new Buffer(substr($hexString, 2), 'hex');
    }

    public static function isBuffer(mixed $obj):bool
    {
        //todo
        return false;
    }

    public function reverse(): Buffer
    {
        $new_buffer = Buffer::from([]);
        $new_buffer->buffer = array_reverse($this->buffer);
        return $new_buffer;
    }


    public function toHex(): string
    {
        $hex_str = "";
        foreach ($this->buffer as $byte) {
            $hex_str .= sprintf("%02x", $byte);
        }
        return $hex_str;
    }

    public static function __set_state($array){
        $ab = new Buffer('');

        $ab->buffer = $array['buffer'];

        return $ab;
    }
}