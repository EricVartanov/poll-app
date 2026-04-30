<?php

namespace App\Domain\Poll\Repositories;

use App\Domain\Poll\Contracts\PollRepositoryInterface;
use App\Domain\Poll\Models\Poll;
use App\Domain\Poll\Models\Vote;

class PollRepository implements PollRepositoryInterface
{
    public function findByShortCode(string $shortCode): Poll
    {
        return Poll::with('options')->where('short_code', $shortCode)->first();
    }

    public function hasVoted(int $pollId, string $ip): bool
    {
        return Vote::where('poll_id', $pollId)->where('ip_address', $ip)->exists();
    }
}

