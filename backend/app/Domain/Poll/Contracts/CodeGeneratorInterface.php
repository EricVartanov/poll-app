<?php

namespace App\Domain\Poll\Contracts;

interface CodeGeneratorInterface
{
    public function generate(): string;
}
