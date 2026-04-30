<?php

namespace App\Domain\Poll\Contracts;

use App\Domain\Poll\Models\Vote;

interface VoteRepositoryInterface
{
    public function createVote(int $pollId, int $optionId, string $ip): Vote;

    public function getResults(int $pollId): array;
}
