<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PollResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'short_code' => $this->short_code,
            'title' => $this->title,
            'options' => $this->options->map(function ($option) {
                return [
                    'id' => $option->id,
                    'text' => $option->text,
                ];
            }),
        ];
    }
}
