<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Collection;

class PostService
{
    /**
     * @param array $filters An associative array of filters to apply.
     * Possible keys: 'search', 'author_id', 'sort_by'.
     * @return Collection A collection of filtered posts.
     */
    public function getPosts($filters): Collection
    {
        $query = Post::query()
            ->whereNotNull('published_at')
            ->with(['user:id,name'])
            ->withCount('likes');

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['author_id'])) {
            $query->where('user_id', $filters['author_id']);
        }

        if (!empty($filters['sort_by'])) {
            $query->orderBy($filters['sort_by']);
        }

        return $query->get();
    }

    /**
     * @param Post $post The post object to toggle the like status on.
     * @return int The total number of likes for the post after toggling.
     */
    public function toggleLike(Post $post): int
    {
        $like = $post->likes()->where('user_id', Auth::id())->first();

        if ($like) {
            $like->delete();
        } else {
            $post->likes()->create([
                'user_id' => Auth::id(),
            ]);
        }

        return $post->likes()->count();
    }

    /**
     * Retrieve all authors with their IDs and names.
     * @return Collection A collection of users with 'id' and 'name' attributes.
     */
    public function getAuthors(): Collection
    {
        return User::all(['id', 'name']);
    }
}
