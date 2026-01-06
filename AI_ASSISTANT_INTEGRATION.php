<?php
/**
 * AI ASSISTANT INTEGRATION - BLADE CODE
 * 
 * INSTRUCTIONS:
 * 1. Find the section: {{-- Floating Chat Button --}}
 * 2. Replace the entire chat-users renderUsers() JavaScript with the code below
 * 3. Keep all other chat functionality unchanged
 * 
 * The AI Assistant will appear at the top of the user list automatically
 */
?>

{{-- REPLACE THIS SECTION IN resources/views/forum/index.blade.php --}}
{{-- Find: <div id="chat-users" style="max-height:130px; overflow-y:auto;"></div> --}}

{{-- 
    UPDATED BLADE CODE WITH AI ASSISTANT
    Replace the chat users rendering section in the JavaScript
--}}

<script>
    /**
     * AI Assistant Integration - Add to existing chat JavaScript
     * This code maintains all existing chat functionality while adding AI support
     */
    
    // Add this AFTER the existing loadChatUsers() function
    // and BEFORE renderUsers() function
    
    // AI Assistant User Object
    const AI_ASSISTANT = {
        id: 'ai-assistant',
        name: 'EduSpark AI Assistant',
        is_ai: true
    };

    // Modified renderUsers function to include AI Assistant
    function renderUsers(users) {
        chatUsers.innerHTML = '';
        
        // ALWAYS add AI Assistant at the top
        const aiDiv = document.createElement('div');
        aiDiv.textContent = AI_ASSISTANT.name;
        aiDiv.style.padding = '10px 8px';
        aiDiv.style.cursor = 'pointer';
        aiDiv.style.borderBottom = '2px solid #6a4df7';
        aiDiv.style.fontWeight = 'bold';
        aiDiv.style.color = '#6a4df7';
        aiDiv.style.backgroundColor = 'rgba(106, 77, 247, 0.1)';
        aiDiv.onclick = () => openAIChat();
        chatUsers.appendChild(aiDiv);
        
        // Then render regular users
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

    // New function: Open AI Chat
    function openAIChat() {
        activeUserId = 'ai-assistant';
        if(chatBox.style.display !== 'block') chatBox.style.display = 'block';
        
        // Clear existing messages and interval
        chatMessages.innerHTML = '';
        if(pollingInterval) clearInterval(pollingInterval);
        
        // Load AI conversation from localStorage
        loadAIConversation();
    }

    // Load AI conversation from browser storage
    function loadAIConversation() {
        const stored = localStorage.getItem('ai-conversation-' + currentUserId) || '[]';
        const messages = JSON.parse(stored);
        
        chatMessages.innerHTML = '';
        messages.forEach(m => {
            const div = document.createElement('div');
            div.textContent = (m.sender === 'user' ? 'You: ' : 'AI: ') + m.text;
            div.style.marginBottom = '8px';
            div.style.padding = '6px 8px';
            div.style.borderRadius = '4px';
            div.style.wordWrap = 'break-word';
            if(m.sender === 'user') {
                div.style.backgroundColor = '#e3f2fd';
                div.style.textAlign = 'right';
            } else {
                div.style.backgroundColor = '#f5f5f5';
            }
            chatMessages.appendChild(div);
        });
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Save AI message to localStorage
    function saveAIMessage(sender, text) {
        const stored = localStorage.getItem('ai-conversation-' + currentUserId) || '[]';
        const messages = JSON.parse(stored);
        messages.push({ sender, text, timestamp: new Date().toISOString() });
        localStorage.setItem('ai-conversation-' + currentUserId, JSON.stringify(messages));
    }

    // Modified openChat to reset AI state
    const originalOpenChat = openChat;
    function openChat(userId) {
        // If switching away from AI, clear the flag
        if(activeUserId === 'ai-assistant') {
            activeUserId = null;
        }
        originalOpenChat(userId);
    }

    // Modified chatSend to handle AI Assistant
    const originalChatSend = () => {
        if(activeUserId === 'ai-assistant') {
            sendMessageToAI();
        } else {
            // Original logic
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
        }
    };

    chatSend.onclick = originalChatSend;

    // Send message to AI Assistant
    function sendMessageToAI() {
        const message = chatInput.value.trim();
        if(!message) return;
        
        // Show user message immediately
        saveAIMessage('user', message);
        const userDiv = document.createElement('div');
        userDiv.textContent = 'You: ' + message;
        userDiv.style.marginBottom = '8px';
        userDiv.style.padding = '6px 8px';
        userDiv.style.borderRadius = '4px';
        userDiv.style.backgroundColor = '#e3f2fd';
        userDiv.style.textAlign = 'right';
        userDiv.style.wordWrap = 'break-word';
        chatMessages.appendChild(userDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Show loading indicator
        const loadingDiv = document.createElement('div');
        loadingDiv.textContent = 'AI: Thinking...';
        loadingDiv.id = 'loading-indicator';
        loadingDiv.style.marginBottom = '8px';
        loadingDiv.style.padding = '6px 8px';
        loadingDiv.style.borderRadius = '4px';
        loadingDiv.style.backgroundColor = '#f5f5f5';
        loadingDiv.style.fontSize = '12px';
        loadingDiv.style.opacity = '0.6';
        chatMessages.appendChild(loadingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Send to backend
        fetch('/api/ai-chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message })
        })
        .then(res => res.json())
        .then(data => {
            // Remove loading indicator
            const loadingEl = document.getElementById('loading-indicator');
            if(loadingEl) loadingEl.remove();
            
            if(data.success && data.reply) {
                // Save AI response
                saveAIMessage('ai', data.reply);
                
                // Show AI response
                const aiDiv = document.createElement('div');
                aiDiv.textContent = 'AI: ' + data.reply;
                aiDiv.style.marginBottom = '8px';
                aiDiv.style.padding = '6px 8px';
                aiDiv.style.borderRadius = '4px';
                aiDiv.style.backgroundColor = '#f5f5f5';
                aiDiv.style.wordWrap = 'break-word';
                chatMessages.appendChild(aiDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            } else {
                alert('Error getting AI response');
            }
            
            chatInput.value = '';
        })
        .catch(err => {
            console.error('AI Chat Error:', err);
            const loadingEl = document.getElementById('loading-indicator');
            if(loadingEl) loadingEl.remove();
            alert('Failed to connect to AI Assistant');
            chatInput.value = '';
        });
    }
});
</script>
