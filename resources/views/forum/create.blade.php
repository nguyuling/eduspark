<x-forum-layout>
    <h1 class="text-2xl font-bold mb-4">Create New Post</h1>

    <form action="{{ route('forum.store') }}" method="POST">
        @csrf

        <label class="block font-semibold">Title</label>
        <input name="title" class="w-full border p-2 rounded mb-3">

        <label class="block font-semibold">Content</label>
        <textarea name="content" class="w-full border p-2 rounded mb-3"></textarea>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">Post</button>
    </form>
</x-forum-layout>

