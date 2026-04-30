<?php

namespace App\Domain\Poll\Factories;

use App\Domain\Poll\Contracts\CodeGeneratorInterface;
use App\Domain\Poll\Models\Option;
use App\Domain\Poll\Models\Poll;
use App\Domain\Poll\Services\RandomCodeGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Poll>
 */
class PollFactory
{
    private CodeGeneratorInterface $codeGenerator;

    public function __construct(?CodeGeneratorInterface $codeGenerator = null)
    {
        $this->codeGenerator = $codeGenerator ?? new RandomCodeGenerator();
    }

    public function create(string $title, array $optionsArray): Poll
    {
        $shortCode = $this->generateUniqueCode();

        $poll = Poll::create([
            'title' => $title,
            'short_code' => $shortCode,
        ]);

        foreach ($optionsArray as $text) {
            Option::create([
                'poll_id' => $poll->id,
                'text' => $text,
            ]);
        }

        return $poll->load('options');
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = $this->codeGenerator->generate();
        } while (Poll::where('short_code', $code)->exists());

        return $code;
    }
}
