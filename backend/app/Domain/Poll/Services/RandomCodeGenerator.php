<?php

namespace App\Domain\Poll\Services;

use App\Domain\Poll\Contracts\CodeGeneratorInterface;

class RandomCodeGenerator implements CodeGeneratorInterface
{

    private string $chars = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
    private int $length = 6;

    public function generate(): string
    {
        $result = '';
        $max = strlen($this->chars) - 1;
        for ($i = 0; $i < $this->length; $i++) {
            $result .= $this->chars[random_int(0, $max)];
        }
        return $result;
    }
}
