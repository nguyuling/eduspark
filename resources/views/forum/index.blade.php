<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900">Forum</h1>
                <a href="{{ route('forum.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    New Post
                </a>
            </div>

            @if ($posts->isEmpty())
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <p class="text-gray-500 text-lg">No forum posts yet. Be the first to create one!</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($posts as $post)
                        <div class="bg-white rounded-lg shadow hover:shadow-md transition p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900 mb-2">
                                        <a href="{{ route('forum.show', $post->id) }}" class="hover:text-blue-600">
                                            {{ $post->title }}
                                        </a>
                                    </h2>
                                    <p class="text-gray-600 mb-4">{{ Str::limit($post->content, 150) }}</p>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <span class="mr-4">By {{ $post->author_name }}</span>
                                        <span>{{ $post->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                @if (auth()->check() && auth()->id() == $post->user_id)
                                    <div class="flex gap-2">
                                        <a href="{{ route('forum.edit', $post->id) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                        <form action="{{ route('forum.destroy', $post->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Delete?')">Delete</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</body>
</html>
