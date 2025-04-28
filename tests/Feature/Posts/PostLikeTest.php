<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot like a post', function () {
    $post = Post::factory()->create();

    $response = $this->postJson(route('posts.like', $post));

    $response->assertStatus(401);
});

test('authenticated user can like a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('posts.like', $post));

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Like toggled successfully',
            'data' => [
                'likes_count' => 1
            ]
        ]);

    $this->assertDatabaseHas('likes', [
        'user_id' => $user->id,
        'post_id' => $post->id
    ]);
});

test('authenticated user can unlike a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->postJson(route('posts.like', $post));

    $response = $this->actingAs($user)
        ->postJson(route('posts.like', $post));

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Like toggled successfully',
            'data' => [
                'likes_count' => 0
            ]
        ]);

    $this->assertDatabaseMissing('likes', [
        'user_id' => $user->id,
        'post_id' => $post->id
    ]);
});

test('multiple users can like the same post', function () {
    $users = User::factory()->count(3)->create();
    $post = Post::factory()->create();

    foreach ($users as $user) {
        $response = $this->actingAs($user)
            ->postJson(route('posts.like', $post));

        $response->assertStatus(200);
    }

    $this->assertDatabaseCount('likes', 3);
    $this->assertEquals(3, $post->fresh()->likes()->count());
});

test('like count is accurate after multiple toggles', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    for ($i = 0; $i < 3; $i++) {
        $this->actingAs($user)
            ->postJson(route('posts.like', $post));
    }

    $response = $this->actingAs($user)
        ->postJson(route('posts.like', $post));

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'likes_count' => 0
            ]
        ]);
});
