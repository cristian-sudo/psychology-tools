<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->posts = Post::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'published_at' => now(),
    ]);
});

test('it can get all posts', function () {
    $response = $this->getJson(route('api.posts'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'content',
                    'published_at',
                    'user' => [
                        'id',
                        'name'
                    ],
                    'likes_count'
                ]
            ]
        ])
        ->assertJsonCount(3, 'data');
});

test('it can filter posts by search term', function () {
    $post = $this->posts->first();
    
    $response = $this->getJson(route('api.posts', ['search' => $post->title]));

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'title' => $post->title
        ]);
});

test('it can filter posts by author', function () {
    $response = $this->getJson(route('api.posts', ['author_id' => $this->user->id]));

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonFragment([
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name
            ]
        ]);
});

test('it can sort posts by title', function () {
    $response = $this->getJson(route('api.posts', ['sort_by' => 'title']));

    $response->assertStatus(200);
    
    $posts = collect($response->json('data'));
    $sortedTitles = $posts->pluck('title')->sort()->values()->toArray();
    $responseTitles = $posts->pluck('title')->values()->toArray();
    
    expect($responseTitles)->toBe($sortedTitles);
});

test('it can sort posts by published date', function () {
    $response = $this->getJson(route('api.posts', ['sort_by' => 'published_at']));

    $response->assertStatus(200);
    
    $posts = collect($response->json('data'));
    $sortedDates = $posts->pluck('published_at')
        ->map(fn ($date) => strtotime($date))
        ->sort()
        ->values()
        ->toArray();
    $responseDates = $posts->pluck('published_at')
        ->map(fn ($date) => strtotime($date))
        ->values()
        ->toArray();
    
    expect($responseDates)->toBe($sortedDates);
});

test('it can combine multiple filters', function () {
    $post = $this->posts->first();
    
    $response = $this->getJson(route('api.posts', [
        'search' => $post->title,
        'author_id' => $this->user->id,
        'sort_by' => 'title'
    ]));

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'title' => $post->title,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name
            ]
        ]);
});

test('it returns empty array when no posts match filters', function () {
    $response = $this->getJson(route('api.posts', [
        'search' => 'nonexistent post title',
        'author_id' => 999
    ]));

    $response->assertStatus(200)
        ->assertJsonCount(0, 'data');
});

test('it shows the create post form', function () {
    $this->actingAs($this->user)
        ->get(route('posts.create'))
        ->assertStatus(200)
        ->assertViewIs('posts.create');
});
