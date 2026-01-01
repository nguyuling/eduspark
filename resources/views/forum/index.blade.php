@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        {{-- Forum Header --}}
        <div class="header">
            <div>
                <div class="title">Forum</div>
                <div class="sub">Perbincangan dan pertanyaan komuniti</div>
            </div>
            <a href="{{ route('forum.create') }}"
               style="display:inline-block; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer; margin-top:15px;"
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
                <i class="bi bi-plus-lg"></i>
                Cipta Post
            </a>
        </div>

        {{-- Search Form --}}
        <section class="panel" style="margin-bottom:20px; margin-top:10px;">
            <form method="GET" action="{{ route('forum.index') }}" id="searchForm"
                  style="display:flex; align-items:center; gap:12px;">
                <input type="text" name="search" id="searchInput"
                       value="{{ request('search') }}"
                       placeholder="Cari posts..."
                       style="flex:1; height:40px; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:14px; background:transparent; color:inherit; transition:border-color .2s ease, background .2s ease;"
                       onfocus="this.style.borderColor='var(--accent)'; this.style.background='rgba(106,77,247,0.05)';"
                       onblur="this.style.borderColor='#d1d5db'; this.style.background='transparent';">
                <button type="submit"
                        style="display:inline-flex; align-items:center; justify-content:center; background:transparent; box-shadow:none; color:var(--accent); padding:0; border:none; cursor:pointer; font-size:20px; transition:opacity .2s ease;"
                        onmouseover="this.style.opacity='0.7';"
                        onmouseout="this.style.opacity='1';"
                        title="Cari">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </section>

        {{-- Posts List --}}
        <div class="space-y-4">
            @forelse ($posts as $post)
                <div class="card" style="position:relative; margin-top:20px; margin-bottom:20px;">
                    {{-- ACTION BUTTONS --}}
                    @if(Auth::check() && Auth::id() === $post->author_id)
                        <div style="display:flex; gap:12px; position:absolute; top:20px; right:20px;">
                            <a href="{{ route('forum.edit', $post->id) }}"
                               style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;"
                               onmouseover="this.style.opacity='0.7';"
                               onmouseout="this.style.opacity='1';"
                               title="Kemaskini">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <form action="{{ route('forum.destroy', $post->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--danger); padding:0; font-size:24px; transition:opacity .2s ease; cursor:pointer;"
                                        onmouseover="this.style.opacity='0.7';"
                                        onmouseout="this.style.opacity='1';"
                                        onclick="return confirm('Anda pasti ingin memadam post ini?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Post Header --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            @php
                                $userName = optional($post->user)->name ?? $post->author_name ?? 'Deleted User';
                                $firstLetter = strtoupper(substr($userName, 0, 1));
                                $colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E2', '#F8B88B', '#52C41A'];
                                $colorIndex = abs(crc32($userName)) % count($colors);
                                $bgColor = $colors[$colorIndex];
                            @endphp

                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg"
                                 style="background-color: {{ $bgColor }};">
                                {{ $firstLetter }}
                            </div>

                            <div>
                                @if($post->user)
                                    <p class="font-semibold user-name" data-user-id="{{ $post->user->id }}">
                                        {{ $userName }}
                                    </p>
                                @else
                                    <p class="font-semibold text-gray-500">
                                        Deleted User
                                    </p>
                                @endif

                                <p class="text-xs opacity-60">
                                    {{ $post->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Post Content --}}
                    <div class="mb-4" style="margin-left:16px;">
                        <h2 class="text-xl font-bold mb-2">
                            <a href="{{ route('forum.show', $post->id) }}" class="link-primary hover:opacity-80 transition">
                                {{ $post->title }}
                            </a>
                        </h2>
                        <p class="opacity-80">
                            {{ Str::limit($post->content, 200) }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-center py-12 opacity-60">Tiada post lagi.</p>
            @endforelse
        </div>

        {{-- Floating Chat Button --}}
        <div id="chat-container" style="position:fixed; bottom:80px; right:100px; width:auto; z-index:9999; display:flex; flex-direction:row-reverse; align-items:flex-end; gap:10px;">
            <button id="chat-toggle"
        style="width:50px; height:50px; border-radius:50%; background:#6a4df7; color:white; font-size:24px; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(0,0,0,0.3); 
               display:flex; align-items:center; justify-content:center; flex-shrink:0;">
    📩
</button>


            <div id="chat-box" style="display:none; background:white; border:1px solid #ccc; border-radius:12px; width:300px; max-height:500px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.2);">
                <div id="chat-header" style="background:#6a4df7; color:white; padding:10px; font-weight:bold; cursor:move;">
                    Mesej
                    <span id="chat-close" style="float:right; cursor:pointer;">✖</span>
                </div>
                <div style="padding:8px; border-bottom:1px solid #ccc;">
    <input type="text" id="chat-search" placeholder="Search user..." 
           style="width:100%; padding:6px 8px; border:1px solid #ddd; border-radius:6px;">
</div>
<div id="chat-users" style="max-height:130px; overflow-y:auto;"></div>

                <div id="chat-messages" style="padding:10px; max-height:200px; overflow-y:auto;"></div>
                <div style="display:flex; border-top:1px solid #ccc;">
                    <input type="text" id="chat-input" placeholder="Type a message..." style="flex:1; padding:8px; border:none;">
                    <button id="chat-send" style="background:#6a4df7; color:white; border:none; padding:0 12px;">Send</button>
                </div>
            </div>
        </div>

        {{-- JS --}}
       <script>
document.addEventListener('DOMContentLoaded', function() {
    // User name menu
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('user-name')) {
            const userId = e.target.dataset.userId;
            const menu = document.createElement('div');
            menu.style.position = 'absolute';
            menu.style.background = '#fff';
            menu.style.border = '1px solid #ddd';
            menu.style.borderRadius = '6px';
            menu.style.padding = '8px';
            menu.style.zIndex = 9999;
            menu.innerHTML = `
                <a href="/users/${userId}" style="display:block;margin-bottom:6px;">Lihat Profil</a>
                <a href="#" class="direct-message-link" data-user-id="${userId}">Hantar Mesej Peribadi</a>
            `;
            document.body.appendChild(menu);
            menu.style.top = e.pageY + 'px';
            menu.style.left = e.pageX + 'px';
            setTimeout(() => menu.remove(), 3000);

            // Direct message click
            menu.querySelector('.direct-message-link').addEventListener('click', function(ev) {
                ev.preventDefault();
                const uid = this.dataset.userId;
                openChat(uid);
            });
        }
    });

    // Search form
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    searchForm.addEventListener('submit', function () {
        setTimeout(() => { searchInput.value = ''; }, 100);
    });

    // Floating Chat JS
    const toggleBtn = document.getElementById('chat-toggle');
    const chatBox = document.getElementById('chat-box');
    const chatClose = document.getElementById('chat-close');
    const chatUsers = document.getElementById('chat-users');
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');
    const chatSearch = document.getElementById('chat-search');
    let activeUserId = null;
    let pollingInterval = null;
    let allUsers = []; // store all users for search filter
    const currentUserId = {{ auth()->id() }}; // Store current user ID

    // Toggle chat box
    toggleBtn.onclick = () => chatBox.style.display = chatBox.style.display === 'block' ? 'none' : 'block';
    chatClose.onclick = () => chatBox.style.display = 'none';

    // Load all users
    function loadChatUsers() {
        fetch('{{ route("messages.index") }}')
            .then(res => res.json())
            .then(users => {
                allUsers = users; // store full list
                renderUsers(users);
            });
    }

    // Render user list
    function renderUsers(users) {
        chatUsers.innerHTML = '';
        users.forEach(user => {
            const div = document.createElement('div');
            div.textContent = user.name;
            div.style.padding = '8px';
            div.style.cursor = 'pointer';
            div.style.borderBottom = '1px solid #eee';
            div.onclick = () => openChat(user.id);
            chatUsers.appendChild(div);
        });
    }

    // Dynamic search filter
    chatSearch.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const filtered = allUsers.filter(u => u.name.toLowerCase().includes(query));
        renderUsers(filtered);
    });

    loadChatUsers();

    // Open chat with a user
    function openChat(userId) {
        activeUserId = userId;
        if(chatBox.style.display !== 'block') chatBox.style.display = 'block'; // auto-open
        chatMessages.innerHTML = '';
        if(pollingInterval) clearInterval(pollingInterval);
        fetchConversation();
        pollingInterval = setInterval(fetchConversation, 2000);
    }

    function fetchConversation(){
        if(!activeUserId) return;
        fetch(`/messages/conversation/${activeUserId}`)
            .then(res=>res.json())
            .then(messages=>{
                chatMessages.innerHTML='';
                messages.forEach(m=>{
                    const div = document.createElement('div');
                    div.textContent = (m.sender_id === currentUserId ? 'You: ' : 'Them: ') + m.message;
                    div.style.marginBottom='6px';
                    chatMessages.appendChild(div);
                });
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
    }

    chatSend.onclick = ()=>{
        if(!activeUserId) return alert('Select a user first!');
        const message = chatInput.value.trim();
        if(!message) return;
        fetch('{{ route("messages.send") }}', {
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
            body:JSON.stringify({receiver_id:activeUserId, message})
        }).then(res=>res.json())
        .then(msg=>{
            chatInput.value='';
            fetchConversation();
        });
    };
});
</script>



    </main>
</div>
@endsection
