<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Services\PostService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    use ApiResponseTrait;

    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Get a list of posts with optional filters.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPosts(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'author_id', 'sort_by']);
        $posts = $this->postService->getPosts($filters);
        return $this->successResponse($posts);
    }

    /**
     * Show the form for creating a new post.
     *
     * @return View
     */
    public function create(): View
    {
        return view('posts.create');
    }

    /**
     * Store a newly created post in DB.
     *
     * @param StorePostRequest $request
     * @return RedirectResponse
     *
     * Note: A DTO could be used for validation, but since we don't need to return validation errors as JSON,
     * a custom request class is more suitable here :)
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $validated_data = $request->validated();

        Post::create([
            'title' => $validated_data['title'],
            'slug' => Str::slug($validated_data['title']),
            'user_id' => Auth::id(),
            'content' => $validated_data['content'],
            'published_at' => now(),
        ]);
        return redirect()->route('welcome');
    }

    /**
     * Toggle like on a post for the authenticated user.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function like(Post $post): JsonResponse
    {
        $likesCount = $this->postService->toggleLike($post);
        return $this->successResponse(['likes_count' => $likesCount], 'Like toggled successfully');
    }

    /**
     * Get a list of authors.
     *
     * @return JsonResponse
     */
    public function getAuthors(): JsonResponse
    {
        $authors = $this->postService->getAuthors();
        return $this->successResponse($authors);
    }
}
