<?php

namespace App\Http\Controllers\Api;

use App\Domain\Poll\Contracts\PollRepositoryInterface;
use App\Domain\Poll\Contracts\VoteRepositoryInterface;
use App\Domain\Poll\Factories\PollFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePollRequest;
use App\Http\Requests\VoteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function __construct(
        private PollFactory $factory,
        private PollRepositoryInterface $polls,
        private VoteRepositoryInterface $votes,
    ) {}


    // POST /api/polls
    public function store(CreatePollRequest $request): JsonResponse
    {

        $poll = $this->factory->create(
            $request->title,
            $request->options
        );

        return response()->json(['short_code' => $poll->short_code], 201);
    }

    // GET /api/polls/{short_code}
    public function show(string $shortCode, Request $request): JsonResponse
    {
        $poll = $this->polls->findByShortCode($shortCode);
        if (!$poll) return response()->json(['message' => 'Not found'], 404);

        $ip = $request->ip();
        $hasVoted = $this->polls->hasVoted($poll->id, $ip);

        $data = [
            'short_code' => $poll->short_code,
            'title' => $poll->title,
            'options' => $poll->options->map(fn($o) => ['id' => $o->id, 'text' => $o->text]),
            'has_voted' => $hasVoted,
        ];

        if ($hasVoted) {
            $data['results'] = $this->votes->getResults($poll->id);
        }

        return response()->json($data);
    }


    // POST /api/polls/{short_code}/vote
    public function vote(string $shortCode, VoteRequest $request)
    {
        $poll = $this->polls->findByShortCode($shortCode);
        if (!$poll) return response()->json(['message' => 'Not found'], 404);

        $ip = $request->ip();

        if ($this->polls->hasVoted($poll->id, $ip)) {
            return response()->json(['message' => 'Already voted'], 409);
        }

        // Validate option belongs to this poll
        $optionExists = $poll->options->contains('id', $request->option_id);
        if (!$optionExists) {
            return response()->json(['message' => 'Invalid option'], 422);
        }

        $this->votes->createVote($poll->id, $request->option_id, $ip);

        return response()->json([
            'votes' => $this->votes->getResults($poll->id)
        ], 201);
    }

}
