@extends('layouts.layout')

@section('title', 'Create Post')

@section('content')
    <div>

        @auth
            <div class="flex w-full justify-center">
                <a href="{{ route('posts.create') }}" class="mb-14 inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Add a new post</a>
            </div>
        @endauth

        @guest
            <div class="flex w-full justify-center">
                <p class="mb-4 text-center text-gray-700">
                    Please <a href="{{ route('login') }}" class="text-blue-500 hover:underline">log in</a> or
                    <a href="{{ route('register') }}" class="text-blue-500 hover:underline">register</a> to create posts and like posts.
                </p>
            </div>
        @endguest

        <div class="w-full lg:max-w-4xl max-w-[335px] space-y-6">
            <form id="filter-form" class="flex flex-col lg:flex-row gap-4 items-center bg-white p-4 rounded shadow">
                <input type="text" id="search" placeholder="Search by title..." class="px-4 py-2 border rounded w-full lg:w-auto focus:outline-none focus:ring-2 focus:ring-blue-500">
                <select id="author-filter" class="px-4 py-2 border rounded w-[170px] focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Filter by Author</option>
                </select>
                <select id="sort-by" class="px-4 py-2 border rounded w-[170px] focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                    <option value="title">Sort by Title</option>
                    <option value="published_at">Sort by Date</option>
                </select>
                <div class="flex space-x-2">
                    <button type="button" id="apply-filters" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Apply</button>
                    <button type="button" id="reset-filters" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Reset</button>
                </div>
            </form>

            <div id="posts-container" class="space-y-4">
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('search');
                const authorFilter = document.getElementById('author-filter');
                const sortBySelect = document.getElementById('sort-by');
                const postsContainer = document.getElementById('posts-container');
                const applyFiltersButton = document.getElementById('apply-filters');
                const resetFiltersButton = document.getElementById('reset-filters');

                initializeEventListeners();

                fetchAuthors();
                fetchPosts();

                function initializeEventListeners() {
                    applyFiltersButton.addEventListener('click', fetchPosts);
                    resetFiltersButton.addEventListener('click', resetFilters);
                    searchInput.addEventListener('input', debounce(fetchPosts, 1000));
                }

                async function fetchAuthors() {
                    try {
                        const response = await fetch('{{ route('api.authors') }}');
                        const data = await response.json();
                        if (data.status === 'success') {
                            populateAuthorFilter(data.data);
                        }
                    } catch (error) {
                        console.error('Error fetching authors:', error);
                    }
                }

                function populateAuthorFilter(authors) {
                    authors.forEach(author => {
                        const option = document.createElement('option');
                        option.value = author.id;
                        option.textContent = author.name;
                        authorFilter.appendChild(option);
                    });
                }

                async function fetchPosts() {
                    const search = searchInput.value;
                    const authorId = authorFilter.value;
                    const sortBy = sortBySelect.value;

                    const queryParams = new URLSearchParams();
                    if (search) queryParams.append('search', search);
                    if (authorId) queryParams.append('author_id', authorId);
                    if (sortBy) queryParams.append('sort_by', sortBy);

                    try {
                        const response = await fetch(`{{ route('api.posts') }}?${queryParams.toString()}`);
                        const data = await response.json();
                        if (data.status === 'success') {
                            updatePostsContainer(data.data);
                        }
                    } catch (error) {
                        console.error('Error fetching posts:', error);
                    }
                }

                function updatePostsContainer(posts) {
                    postsContainer.innerHTML = '';

                    if (posts.length === 0) {
                        const noPostsMessage = document.createElement('div');
                        noPostsMessage.classList.add('text-center', 'text-gray-700', 'mt-6');
                        noPostsMessage.textContent = 'No posts available at the moment. Please check back later.';
                        postsContainer.appendChild(noPostsMessage);
                        return;
                    }

                    posts.forEach(post => {
                        const postElement = document.createElement('div');
                        postElement.classList.add('post', 'p-6', 'bg-white', 'border', 'rounded', 'shadow');
                        postElement.innerHTML = `
                                                <h2 class="text-2xl font-semibold mb-2">${post.title}</h2>
                                                <p class="mb-4 text-gray-700">${post.content}</p>
                                                <div class="text-sm text-gray-500 mb-4">By ${post.user.name} on ${new Date(post.published_at).toLocaleDateString()}</div>
                                                @auth
                                                    <button class="like-button px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600" data-id="${post.id}">Like</button>
                                                @endauth
                                                <span class="likes-count ml-2 text-gray-600">${post.likes_count || 0} Likes</span>
                                                `;
                        postsContainer.appendChild(postElement);
                    });

                    document.querySelectorAll('.like-button').forEach(button => {
                        button.addEventListener('click', function() {
                            likePost(this.dataset.id);
                        });
                    });
                }

                function resetFilters() {
                    searchInput.value = '';
                    authorFilter.value = '';
                    sortBySelect.value = 'title';
                    fetchPosts();
                }

                function debounce(func, wait) {
                    let timeout;
                    return function(...args) {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(this, args), wait);
                    };
                }

                @auth
                async function likePost(postId) {
                    try {
                        const response = await fetch(`/posts/${postId}/like`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            credentials: 'same-origin'
                        });
                        const data = await response.json();
                        if (data.status === 'success') {
                            const postElement = document.querySelector(`.like-button[data-id="${postId}"]`).parentElement;
                            const likesCountElement = postElement.querySelector('.likes-count');
                            likesCountElement.textContent = `${data.data.likes_count} Likes`;
                        }
                    } catch (error) {
                        console.error('Error liking post:', error);
                    }
                }
                @endauth
            });
        </script>
    </div>
@endsection
