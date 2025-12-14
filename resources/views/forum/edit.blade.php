<x-forum-layout>
    <h1 class="text-2xl font-bold mb-4">Edit Post</h1>

    <form action="{{ route('forum.update', $post->id) }}" method="POST">
        @csrf
        @method('PUT')

        <label class="block font-semibold">Title</label>
        <input name="title" class="w-full border p-2 rounded mb-3" value="{{ $post->title }}">

        <label class="block font-semibold">Content</label>
        <textarea name="content" class="w-full border p-2 rounded mb-3">{{ $post->content }}</textarea>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">Save Changes</button>
    </form>
</x-forum-layout>
