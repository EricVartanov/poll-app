<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a poll and returns short_code', function () {
    $response = $this->postJson('/api/polls', [
        'title' => 'Какой цвет лучше?',
        'options' => ['Красный', 'Синий', 'Зелёный'],
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['short_code']);
});

it('rejects creation when title is too short', function () {
    $response = $this->postJson('/api/polls', [
        'title' => 'Hi',
        'options' => ['Красный', 'Синий'],
    ]);

    $response->assertStatus(422);
});

it('rejects creation with only one option', function () {
    $response = $this->postJson('/api/polls', [
        'title' => 'Какой цвет лучше?',
        'options' => ['Красный'],
    ]);

    $response->assertStatus(422);
});

it('returns a poll by short code with has_voted false', function () {
    $shortCode = $this->postJson('/api/polls', [
        'title' => 'Какой цвет лучше?',
        'options' => ['Красный', 'Синий', 'Зелёный'],
    ])->json('short_code');

    $response = $this->getJson("/api/polls/{$shortCode}");

    $response->assertStatus(200)
        ->assertJsonStructure(['title', 'options', 'has_voted'])
        ->assertJsonPath('has_voted', false);
});

it('records a vote and returns a votes array', function () {
    $shortCode = $this->postJson('/api/polls', [
        'title' => 'Какой цвет лучше?',
        'options' => ['Красный', 'Синий', 'Зелёный'],
    ])->json('short_code');

    $optionId = $this->getJson("/api/polls/{$shortCode}")->json('options.0.id');

    $response = $this->postJson("/api/polls/{$shortCode}/vote", [
        'option_id' => $optionId,
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['votes']);

    expect($response->json('votes'))->toBeArray();
});

it('prevents double voting from the same IP', function () {
    $shortCode = $this->postJson('/api/polls', [
        'title' => 'Какой цвет лучше?',
        'options' => ['Красный', 'Синий', 'Зелёный'],
    ])->json('short_code');

    $optionId = $this->getJson("/api/polls/{$shortCode}")->json('options.0.id');

    $this->postJson("/api/polls/{$shortCode}/vote", [
        'option_id' => $optionId,
    ])->assertStatus(201);

    $this->postJson("/api/polls/{$shortCode}/vote", [
        'option_id' => $optionId,
    ])->assertStatus(409);
});

it('shows results when the caller has already voted', function () {
    $shortCode = $this->postJson('/api/polls', [
        'title' => 'Какой цвет лучше?',
        'options' => ['Красный', 'Синий', 'Зелёный'],
    ])->json('short_code');

    $optionId = $this->getJson("/api/polls/{$shortCode}")->json('options.0.id');

    $this->postJson("/api/polls/{$shortCode}/vote", [
        'option_id' => $optionId,
    ])->assertStatus(201);

    $response = $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
        ->getJson("/api/polls/{$shortCode}");

    $response->assertStatus(200)
        ->assertJsonPath('has_voted', true)
        ->assertJsonStructure(['results']);
});
