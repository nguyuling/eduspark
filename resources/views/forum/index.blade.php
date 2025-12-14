<x-forum-layout>
    <h1 class="text-3xl font-bold mb-6">Forum Posts</h1>

    <div class="flex justify-between mb-6">
        <h1 class="text-2xl font-bold">Forum</h1>
        <a href="{{ route('forum.create') }}"
           class="flex items-center gap-2 px-5 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 shadow-sm transition">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
           </svg>
           Create Post
        </a>
    </div>

    {{-- Search Form --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('forum.index') }}" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search posts..." 
                   class="border rounded px-4 py-2 w-full md:w-1/3 text-lg">
            <button type="submit" 
                    class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow-sm transition">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('forum.index') }}" 
                   class="px-5 py-2 bg-gray-400 text-white rounded hover:bg-gray-500 shadow-sm transition">
                   Clear
                </a>
            @endif
        </form>
    </div>

    <div class="space-y-6">
        @forelse ($posts as $post)
            <div class="bg-white shadow-lg rounded-lg border border-gray-200 p-6">

                {{-- Author Info --}}
                <div class="flex items-center mb-4">
                    <img src="{{ $post->author_avatar }}" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <p class="font-semibold text-lg">{{ $post->author_name }}</p>
                        <p class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                {{-- Post Title & Content --}}
                <h2 class="text-2xl font-bold mb-2">
                    <a href="{{ route('forum.show', $post->id) }}" class="text-blue-700 hover:underline">
                        {{ $post->title }}
                    </a>
                </h2>
                <p class="text-gray-700 text-lg mt-2">{{ Str::limit($post->content, 200) }}</p>

                {{-- Action Buttons --}}
                <div class="mt-4 flex flex-wrap gap-3">

                    {{-- Edit --}}
                    <a href="{{ route('forum.edit', $post->id) }}" 
                       class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow-sm transition">
                       Edit
                    </a>

                    {{-- Delete --}}
                    <form action="{{ route('forum.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow-sm transition">
                            Delete
                        </button>
                    </form>

                    {{-- Reply --}}
                    <button onclick="toggleReplyForm({{ $post->id }})"
                            class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow-sm transition">
                        Reply
                    </button>

                </div>

                {{-- Reply Form --}}
                <div id="reply-form-{{ $post->id }}" class="mt-4 hidden">
                    <form action="{{ route('forum.reply', $post->id) }}" method="POST">
                        @csrf
                        <textarea name="content" class="w-full border p-3 rounded mb-2 text-lg" rows="3" placeholder="Write your reply..."></textarea>
                        <button type="submit" 
                                class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow-sm transition">
                            Submit Reply
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <p class="text-gray-500 text-lg">No posts found.</p>
        @endforelse
    </div>

    <script>
        function toggleReplyForm(postId) {
            const form = document.getElementById('reply-form-' + postId);
            form.classList.toggle('hidden');
        }
    </script>

</x-forum-layout>
