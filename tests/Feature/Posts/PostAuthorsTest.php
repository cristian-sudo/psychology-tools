<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can get all authors', function () {
    $users = User::factory()->count(3)->create();
    
    $response = $this->getJson(route('api.authors'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id',
                    'name'
                ]
            ]
        ])
        ->assertJsonCount(3, 'data');
});

test('authors are returned with correct structure', function () {
    $user = User::factory()->create();
    
    $response = $this->getJson(route('api.authors'));

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'data' => [
                [
                    'id' => $user->id,
                    'name' => $user->name
                ]
            ]
        ]);
}); 