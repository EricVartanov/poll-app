<?php

namespace App\Domain\Poll\Repositories;

use App\Domain\Poll\Contracts\VoteRepositoryInterface;
use App\Domain\Poll\Models\Option;
use App\Domain\Poll\Models\Vote;

class VoteRepository implements VoteRepositoryInterface
{
    public function createVote(int $pollId, int $optionId, string $ip): Vote
    {
        return Vote::create([
            'poll_id' => $pollId,
            'option_id' => $optionId,
            'ip_address' => $ip,
        ]);
    }

    public function getResults(int $pollId): array
    {
        $options = Option::withCount(['votes' => function ($q) use ($pollId) {
            $q->where('poll_id', $pollId);
        }])->where('poll_id', $pollId)->get();

        return $options->map(fn($opt) => [
            'option_id' => $opt->id,
            'option' => $opt->text,
            'count' => $opt->votes_count,
        ])->toArray();
    }
}
