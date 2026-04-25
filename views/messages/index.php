<div class="h-[calc(100vh-100px)] max-h-[800px] max-w-6xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 flex overflow-hidden">
    
    <!-- Contacts Sidebar -->
    <div class="w-1/3 border-r border-gray-100 flex flex-col bg-gray-50/50">
        <div class="p-4 border-b border-gray-100 bg-white">
            <h2 class="text-xl font-bold text-gray-800">Chat</h2>
            <p class="text-xs text-gray-500 mt-1">Select a conversation to start chatting</p>
        </div>
        
        <div class="flex-1 overflow-y-auto p-2 space-y-1" id="contactsList">
            <?php if (empty($contacts)): ?>
                <div class="p-6 text-center text-gray-400">
                    <i class="fas fa-users-slash text-3xl mb-2 opacity-50"></i>
                    <p class="text-sm font-medium">No contacts available.</p>
                    <p class="text-xs mt-1">Contacts appear here once a rental request is paid.</p>
                </div>
            <?php else: ?>
                <?php foreach ($contacts as $contact): ?>
                    <button class="w-full text-left p-3 rounded-xl hover:bg-white hover:shadow-sm transition-all flex items-center gap-3 contact-btn" 
                            data-id="<?= $contact['id'] ?>" 
                            data-name="<?= htmlspecialchars($contact['name']) ?>"
                            data-type="<?= htmlspecialchars($contact['type']) ?>">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                                <?= strtoupper(substr($contact['name'], 0, 1)) ?>
                            </div>
                            <?php if ($contact['unread'] > 0): ?>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center border-2 border-white unread-badge">
                                    <?= $contact['unread'] ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 truncate"><?= htmlspecialchars($contact['name']) ?></h4>
                            <p class="text-xs font-semibold text-gray-500 uppercase"><?= htmlspecialchars($contact['type']) ?></p>
                        </div>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col bg-white hidden" id="chatArea">
        <!-- Chat Header -->
        <div class="p-4 border-b border-gray-100 flex items-center gap-3 bg-white shadow-sm z-10">
            <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold shadow-sm" id="chatAvatar">
                ?
            </div>
            <div>
                <h3 class="font-bold text-gray-900 leading-tight" id="chatName">Select Contact</h3>
                <p class="text-xs text-gray-500 font-semibold uppercase" id="chatType">--</p>
            </div>
        </div>

        <!-- Chat Thread -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50" id="chatThread">
            <!-- Messages injected here via JS -->
            <div class="flex items-center justify-center h-full text-gray-400">
                <p>Loading messages...</p>
            </div>
        </div>

        <!-- Message Input -->
        <div class="p-4 bg-white border-t border-gray-100">
            <form id="messageForm" class="flex gap-2">
                <input type="hidden" id="receiverId" name="receiver_id">
                <input type="text" id="messageInput" name="message" required autocomplete="off"
                       placeholder="Type your message here..."
                       class="flex-1 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition-colors outline-none">
                <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-xl text-sm px-5 py-3 text-center transition-colors shadow-md flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> <span class="hidden sm:inline">Send</span>
                </button>
            </form>
        </div>
    </div>
    
    <!-- Empty State -->
    <div class="flex-1 flex flex-col items-center justify-center bg-slate-50 text-center" id="emptyState">
        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-sm mb-4">
            <i class="fas fa-comments text-4xl text-blue-200"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800">Your Chats</h3>
        <p class="text-gray-500 mt-2 max-w-sm">Select a contact from the left menu to view your conversation or start a new one.</p>
    </div>
</div>

<script>
    const currentUserId = <?= json_encode($userId) ?>;
    let activeContactId = null;
    let pollInterval = null;

    const chatArea = document.getElementById('chatArea');
    const emptyState = document.getElementById('emptyState');
    const chatName = document.getElementById('chatName');
    const chatType = document.getElementById('chatType');
    const chatAvatar = document.getElementById('chatAvatar');
    const receiverIdInput = document.getElementById('receiverId');
    const chatThread = document.getElementById('chatThread');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');

    // Handle Contact Selection
    document.querySelectorAll('.contact-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // UI styling for active contact
            document.querySelectorAll('.contact-btn').forEach(b => b.classList.remove('bg-blue-50', 'shadow-sm', 'border-blue-200', 'border'));
            this.classList.add('bg-blue-50', 'shadow-sm', 'border-blue-200', 'border');
            
            // Remove unread badge visually
            const badge = this.querySelector('.unread-badge');
            if (badge) badge.remove();

            // Set active contact
            activeContactId = this.dataset.id;
            receiverIdInput.value = activeContactId;

            // Update Header
            const name = this.dataset.name;
            chatName.textContent = name;
            chatType.textContent = this.dataset.type;
            chatAvatar.textContent = name.substring(0, 1).toUpperCase();

            // Show Chat Area
            emptyState.classList.add('hidden');
            chatArea.classList.remove('hidden');
            chatArea.classList.add('flex');

            // Initial Fetch & Start Polling
            fetchMessages(true);
            if (pollInterval) clearInterval(pollInterval);
            pollInterval = setInterval(() => fetchMessages(false), 3000);
            
            messageInput.focus();
        });
    });

    // Fetch Messages
    async function fetchMessages(scrollToBottom = false) {
        if (!activeContactId) return;

        try {
            const response = await fetch(`<?= APP_URL ?>/messages/fetch?contact_id=${activeContactId}`);
            const data = await response.json();

            if (data.success) {
                renderMessages(data.messages, scrollToBottom);
            }
        } catch (error) {
            console.error('Error fetching messages:', error);
        }
    }

    // Render Messages
    function renderMessages(messages, scrollToBottom) {
        if (messages.length === 0) {
            chatThread.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full text-gray-400">
                    <i class="far fa-comment-dots text-3xl mb-2 opacity-50"></i>
                    <p class="text-sm">No messages yet. Say hello!</p>
                </div>
            `;
            return;
        }

        let html = '';
        let lastDate = null;

        messages.forEach(msg => {
            const isMe = msg.sender_id == currentUserId;
            
            // Format time
            const dateObj = new Date(msg.created_at);
            const timeStr = dateObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            const dateStr = dateObj.toLocaleDateString();

            // Insert date separator if day changes
            if (dateStr !== lastDate) {
                html += `
                    <div class="flex justify-center my-4">
                        <span class="bg-white border border-gray-100 text-gray-400 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            ${dateStr}
                        </span>
                    </div>
                `;
                lastDate = dateStr;
            }

            if (isMe) {
                html += `
                    <div class="flex justify-end mb-4 animate-fade-in">
                        <div class="bg-blue-600 text-white rounded-2xl rounded-tr-sm py-2.5 px-4 max-w-[75%] shadow-sm">
                            <p class="text-sm break-words whitespace-pre-wrap">${escapeHtml(msg.message)}</p>
                            <p class="text-[10px] text-blue-200 text-right mt-1.5 flex items-center justify-end gap-1">
                                ${timeStr} 
                                <i class="fas fa-check-double ${msg.is_read ? 'text-blue-200' : 'text-blue-400/50'}"></i>
                            </p>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="flex justify-start mb-4 animate-fade-in">
                        <div class="bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-sm py-2.5 px-4 max-w-[75%] shadow-sm">
                            <p class="text-sm break-words whitespace-pre-wrap">${escapeHtml(msg.message)}</p>
                            <p class="text-[10px] text-gray-400 text-left mt-1.5">${timeStr}</p>
                        </div>
                    </div>
                `;
            }
        });

        // Check if we need to update DOM
        // Simple trick: only update if innerHTML changed to avoid scrolling jumps if nothing changed
        if (chatThread.innerHTML !== html) {
            // Need to check if user is scrolled to bottom before updating
            const isScrolledToBottom = chatThread.scrollHeight - chatThread.clientHeight <= chatThread.scrollTop + 50;
            
            chatThread.innerHTML = html;
            
            if (scrollToBottom || isScrolledToBottom) {
                chatThread.scrollTop = chatThread.scrollHeight;
            }
        }
    }

    // Send Message
    messageForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const text = messageInput.value.trim();
        if (!text || !activeContactId) return;

        const formData = new FormData();
        formData.append('receiver_id', activeContactId);
        formData.append('message', text);

        // Optimistically clear input
        messageInput.value = '';
        messageInput.focus();

        try {
            const response = await fetch('<?= APP_URL ?>/messages/send', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.success) {
                fetchMessages(true); // Force scroll to bottom on new message
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert("Failed to send message. Please try again.");
        }
    });

    // Utility: HTML Escaping to prevent XSS
    function escapeHtml(unsafe) {
        return unsafe
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }
</script>
