<x-forum-layout>

    <h1 class="text-3xl font-bold mb-2">{{ $post->title }}</h1>

    <div class="flex items-center gap-2 mb-4">
        <img src="{{ $post->author_avatar }}" class="w-10 h-10 rounded-full">
        <span class="font-semibold">{{ $post->author_name }}</span>
    </div>

    <p class="mb-6">{{ $post->content }}</p>

    {{-- EDIT / DELETE BUTTONS --}}
    <div class="mb-6 flex gap-2">
        <a href="{{ route('forum.edit', $post->id) }}"
           class="px-3 py-2 bg-yellow-500 text-white rounded">Edit</a>

        <form action="{{ route('forum.destroy', $post->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button class="px-3 py-2 bg-red-600 text-white rounded"
                    onclick="return confirm('Delete this post?')">Delete</button>
        </form>
    </div>

    <hr class="my-4">

    <h2 class="text-xl font-bold mb-2">Replies</h2>

    @foreach ($post->replies as $reply)
        <div class="border p-3 rounded mb-2 bg-gray-50">
            <div class="flex items-center gap-2">
                <img src="{{ $reply->author_avatar }}" class="w-8 h-8 rounded-full">
                <strong>{{ $reply->author_name }}</strong>
            </div>
            <p class="ml-10">{{ $reply->content }}</p>
        </div>
    @endforeach

    <form action="{{ route('forum.reply', $post->id) }}" method="POST" class="mt-4">
        @csrf

        <textarea name="reply" class="w-full border p-2 rounded mb-2" placeholder="Write a reply..."></textarea>

        <button class="px-4 py-2 bg-green-600 text-white rounded">Reply</button>
    </form>

</x-forum-layout>
