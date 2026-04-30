<?php

namespace App\Domain\Poll\Contracts;

use App\Domain\Poll\Models\Poll;

interface PollRepositoryInterface
{
    public function findByShortCode(string $shortCode): Poll;
    public function hasVoted(int $pollId, string $ip): bool;
}
