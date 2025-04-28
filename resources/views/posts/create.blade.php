@extends('layouts.layout')

@section('title', 'Create Post')

@section('content')

    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-semibold mb-6">Create a New Post</h1>
        <form method="POST" action="{{ route('posts.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required class="block w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                @if ($errors->has('title'))
                    <span class="text-red-500 text-sm">{{ $errors->first('title') }}</span>
                @endif
            </div>
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                <textarea id="content" name="content" required class="block w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('content') }}</textarea>
                @if ($errors->has('content'))
                    <span class="text-red-500 text-sm">{{ $errors->first('content') }}</span>
                @endif
            </div>
            <button type="submit" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Create Post</button>
        </form>
    </div>
@endsection
