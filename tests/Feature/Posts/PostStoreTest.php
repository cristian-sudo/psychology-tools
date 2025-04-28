<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Carbon\Carbon;

uses(RefreshDatabase::class);

test('guest cannot store a post', function () {
    $response = $this->post(route('posts.store'), [
        'title' => 'Test Post',
        'content' => 'Test Content'
    ]);

    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('posts', 0);
});

test('authenticated user can store a post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('posts.store'), [
            'title' => 'Test Post',
            'content' => 'Test Content'
        ]);

    $response->assertRedirect(route('welcome'));

    $this->assertDatabaseHas('posts', [
        'title' => 'Test Post',
        'slug' => Str::slug('Test Post'),
        'user_id' => $user->id,
        'content' => 'Test Content',
    ]);
});

test('title is required', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('posts.store'), [
            'content' => 'Test Content'
        ]);

    $response->assertSessionHasErrors('title');
    $this->assertDatabaseCount('posts', 0);
});

test('title cannot be longer than 180 characters', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('posts.store'), [
            'title' => Str::random(181),
            'content' => 'Test Content'
        ]);

    $response->assertSessionHasErrors('title');
    $this->assertDatabaseCount('posts', 0);
});

test('content is required', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('posts.store'), [
            'title' => 'Test Post'
        ]);

    $response->assertSessionHasErrors('content');
    $this->assertDatabaseCount('posts', 0);
});

test('content cannot be longer than 512 characters', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('posts.store'), [
            'title' => 'Test Post',
            'content' => Str::random(513)
        ]);

    $response->assertSessionHasErrors('content');
    $this->assertDatabaseCount('posts', 0);
});

test('post is created with correct published_at timestamp', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('posts.store'), [
            'title' => 'Test Post',
            'content' => 'Test Content'
        ]);

    $post = Post::first();
    $this->assertNotNull($post->published_at);

    $publishedAt = Carbon::parse($post->published_at);
    $now = Carbon::parse(now());

    $this->assertTrue($publishedAt->diffInSeconds($now) <= 1);
});
