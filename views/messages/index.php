<style>
    .chat-container {
        background-color: #e5ddd5;
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
    }
    .bubble {
        position: relative;
        max-width: 75%;
        padding: 8px 12px;
        margin-bottom: 4px;
        box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
    }
    .bubble-out {
        background-color: #dcf8c6;
        align-self: flex-end;
        border-radius: 8px 0 8px 8px;
    }
    .bubble-in {
        background-color: #ffffff;
        align-self: flex-start;
        border-radius: 0 8px 8px 8px;
    }
    .bubble-out::before {
        content: "";
        position: absolute;
        top: 0;
        right: -8px;
        width: 0;
        height: 0;
        border: 4px solid transparent;
        border-left-color: #dcf8c6;
        border-top-color: #dcf8c6;
    }
    .bubble-in::before {
        content: "";
        position: absolute;
        top: 0;
        left: -8px;
        width: 0;
        height: 0;
        border: 4px solid transparent;
        border-right-color: #ffffff;
        border-top-color: #ffffff;
    }
    .voice-note-player {
        display: flex;
        items-center: center;
        gap: 10px;
        min-width: 200px;
        padding: 4px 0;
    }
    .play-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background: transparent;
    }
    .progress-bar-container {
        flex: 1;
        height: 4px;
        background: rgba(0,0,0,0.1);
        border-radius: 2px;
        position: relative;
        cursor: pointer;
    }
    .progress-bar-fill {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        background: #34b7f1;
        border-radius: 2px;
        width: 0%;
    }
    .recording-bar {
        position: absolute;
        inset: 0;
        background: white;
        display: none;
        align-items: center;
        padding: 0 16px;
        gap: 12px;
        z-index: 30;
    }
    .recording-dot {
        width: 10px;
        height: 10px;
        background: #ff4b2b;
        border-radius: 50%;
        animation: pulse 1s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
        100% { opacity: 1; transform: scale(1); }
    }
</style>

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
        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-white shadow-sm z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold shadow-sm" id="chatAvatar">
                    ?
                </div>
                <div class="text-left">
                    <h3 class="font-bold text-gray-900 leading-tight" id="chatName">Select Contact</h3>
                    <p class="text-xs text-gray-500 font-semibold uppercase" id="chatType">--</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button id="voiceCallBtn" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-all" title="Voice Call">
                    <i class="fas fa-phone-alt text-lg"></i>
                </button>
                <button id="videoCallBtn" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-all" title="Video Call">
                    <i class="fas fa-video text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Chat Thread -->
        <div class="flex-1 overflow-y-auto p-6 space-y-2 chat-container flex flex-col" id="chatThread">
            <div class="flex items-center justify-center h-full text-gray-400">
                <p>Loading messages...</p>
            </div>
        </div>

        <!-- Message Input -->
        <div class="p-4 bg-white border-t border-gray-100 relative">
            <div id="recordingBar" class="recording-bar">
                <div class="recording-dot"></div>
                <div class="flex-1 font-mono text-red-600" id="recordTimerDisplay">00:00</div>
                <button type="button" id="btnDeleteRecord" class="text-gray-400 hover:text-red-600 p-2">
                    <i class="fas fa-trash-alt text-lg"></i>
                </button>
                <div class="text-xs text-gray-400 px-4 select-none">Slide to cancel ></div>
                <button type="button" id="btnFinishRecord" class="bg-blue-600 text-white w-10 h-10 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            <form id="messageForm" class="flex gap-2 items-end">
                <input type="hidden" id="receiverId" name="receiver_id">

                <div class="relative group">
                    <button type="button" id="attachmentMenuBtn" class="p-3 text-gray-400 hover:text-blue-600 transition-colors">
                        <i class="fas fa-plus-circle text-xl"></i>
                    </button>
                    <div id="attachmentMenu" class="hidden absolute bottom-full mb-2 left-0 bg-white border border-gray-100 shadow-lg rounded-xl py-2 w-48 z-20">
                        <button type="button" id="btnUploadFile" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 flex items-center gap-3">
                            <i class="fas fa-file-alt text-blue-500"></i> Send File
                        </button>
                    </div>
                </div>

                <input type="text" id="messageInput" name="message" autocomplete="off"
                       placeholder="Type your message here..."
                       class="flex-1 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-full focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition-colors outline-none px-6">

                <button type="button" id="micBtn" class="w-12 h-12 flex items-center justify-center text-gray-500 hover:text-blue-600 transition-all">
                    <i class="fas fa-microphone text-xl"></i>
                </button>

                <button type="submit" id="sendBtn" class="hidden text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full w-12 h-12 flex items-center justify-center transition-colors shadow-md">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>



        <!-- Call Modal -->
        <div id="callModal" class="hidden fixed inset-0 bg-slate-900 z-[60] flex flex-col items-center justify-center text-white backdrop-blur-lg">
            <div class="text-center max-w-sm p-8">
                <div class="w-32 h-32 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl ring-4 ring-white/20" id="callAvatarLarge">
                    ?
                </div>
                <h2 class="text-3xl font-bold mb-2" id="callNameLarge">Contact Name</h2>
                <p class="text-blue-300 font-medium mb-12" id="callStatusText">Calling...</p>

                <div class="flex justify-center gap-6">
                    <button id="btnDeclineCall" class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center hover:bg-red-700 transition-all shadow-lg">
                        <i class="fas fa-phone-slash text-xl"></i>
                    </button>
                    <button id="btnAcceptCall" class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center hover:bg-green-700 transition-all shadow-lg">
                        <i class="fas fa-phone text-xl"></i>
                    </button>
                </div>
            </div>
            <button id="btnEndCall" class="absolute bottom-12 px-8 py-3 bg-red-600 rounded-full font-bold hover:bg-red-700 transition-all hidden">
                End Call
            </button>
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

<script src="https://download.agora.io/sdk/web/AgoraRTC_N-4.11.0.js"></script>
<script>
    const currentUserId = <?= json_encode($userId) ?>;
    const agoraAppId = <?= json_encode($agoraAppId) ?>;
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
            document.querySelectorAll('.contact-btn').forEach(b => b.classList.remove('bg-blue-50', 'shadow-sm', 'border-blue-200', 'border'));
            this.classList.add('bg-blue-50', 'shadow-sm', 'border-blue-200', 'border');

            const badge = this.querySelector('.unread-badge');
            if (badge) badge.remove();

            activeContactId = this.dataset.id;
            receiverIdInput.value = activeContactId;

            const name = this.dataset.name;
            chatName.textContent = name;
            chatType.textContent = this.dataset.type;
            chatAvatar.textContent = name.substring(0, 1).toUpperCase();

            emptyState.classList.add('hidden');
            chatArea.classList.remove('hidden');
            chatArea.classList.add('flex');

            fetchMessages(true);
            if (pollInterval) clearInterval(pollInterval);
            pollInterval = setInterval(() => fetchMessages(false), 3000);

            messageInput.focus();
        });
    });

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

        messages.forEach((msg, index) => {
            const isMe = msg.sender_id == currentUserId;
            const dateObj = new Date(msg.created_at);
            const timeStr = dateObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            const dateStr = dateObj.toLocaleDateString();

            if (dateStr !== lastDate) {
                html += `<div class="flex justify-center my-4"><span class="bg-[#d1e4f6] text-[#128c7e] text-[10px] font-bold px-3 py-1 rounded shadow-sm uppercase tracking-wider">${dateStr}</span></div>`;
                lastDate = dateStr;
            }

            let contentHtml = '';
            if (msg.type === 'file') {
                if (msg.file_type && msg.file_type.startsWith('image/')) {
                    contentHtml = `<img src="<?= APP_URL ?>/${msg.file_path}" class="max-w-full rounded-lg mb-1 cursor-pointer hover:opacity-90 transition-opacity" onclick="window.open('<?= APP_URL ?>/${msg.file_path}', '_blank')">`;
                } else {
                    contentHtml = `
                        <div class="flex items-center gap-3 p-2 bg-black/5 rounded-lg border border-black/5 cursor-pointer hover:bg-black/10 transition-colors" onclick="window.open('<?= APP_URL ?>/${msg.file_path}', '_blank')">
                            <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded flex items-center justify-center text-xs font-bold">
                                ${msg.file_name ? msg.file_name.split('.').pop().toUpperCase() : 'FILE'}
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <p class="text-xs font-bold text-gray-700 truncate">${msg.file_name || 'Attachment'}</p>
                                <p class="text-[10px] text-gray-400">${msg.file_size ? (msg.file_size / 1024).toFixed(1) + ' KB' : ''}</p>
                            </div>
                            <i class="fas fa-download text-gray-400 text-xs"></i>
                        </div>`;
                }
            } else if (msg.type === 'voice_note') {
                contentHtml = `
                    <div class="voice-note-player" data-src="<?= APP_URL ?>/${msg.file_path}">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="fas fa-microphone"></i>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-white rounded-full flex items-center justify-center text-[8px]">
                                <i class="fas fa-play text-blue-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 space-y-1">
                            <div class="progress-bar-container">
                                <div class="progress-bar-fill"></div>
                            </div>
                            <div class="flex justify-between items-center px-0.5">
                                <span class="text-[9px] text-gray-500 duration">0:00</span>
                                <button class="play-btn-small text-blue-600"><i class="fas fa-play text-xs"></i></button>
                            </div>
                        </div>
                    </div>`;
            } else {
                contentHtml = `<p class="text-sm break-words whitespace-pre-wrap">${escapeHtml(msg.message || '')}</p>`;
            }

            const tickColor = msg.is_read ? 'text-[#34b7f1]' : 'text-gray-400';
            const bubbleClass = isMe ? 'bubble bubble-out' : 'bubble bubble-in';

            html += `
                <div class="${bubbleClass} animate-fade-in">
                    ${contentHtml}
                    <div class="text-[10px] ${isMe ? 'text-gray-500' : 'text-gray-400'} text-right mt-1 flex items-center justify-end gap-1">
                        ${timeStr}
                        ${isMe ? `<i class="fas fa-check-double ${tickColor}"></i>` : ''}
                    </div>
                </div>
            `;
        });

        if (chatThread.innerHTML !== html) {
            const isScrolledToBottom = chatThread.scrollHeight - chatThread.clientHeight <= chatThread.scrollTop + 50;
            chatThread.innerHTML = html;
            setupAudioPlayers();
            if (scrollToBottom || isScrolledToBottom) {
                chatThread.scrollTop = chatThread.scrollHeight;
            }
        }
    }

    function setupAudioPlayers() {
        document.querySelectorAll('.voice-note-player').forEach(player => {
            if (player.dataset.initialized) return;
            player.dataset.initialized = "true";

            const audio = new Audio(player.dataset.src);
            const playBtn = player.querySelector('.play-btn-small');
            const icon = playBtn.querySelector('i');
            const fill = player.querySelector('.progress-bar-fill');
            const durationText = player.querySelector('.duration');

            audio.addEventListener('loadedmetadata', () => {
                const mins = Math.floor(audio.duration / 60);
                const secs = Math.floor(audio.duration % 60);
                durationText.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
            });

            audio.addEventListener('timeupdate', () => {
                const pct = (audio.currentTime / audio.duration) * 100;
                fill.style.width = pct + '%';
                const mins = Math.floor(audio.currentTime / 60);
                const secs = Math.floor(audio.currentTime % 60);
                durationText.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
            });

            audio.addEventListener('ended', () => {
                icon.className = 'fas fa-play text-xs';
                fill.style.width = '0%';
            });

            playBtn.addEventListener('click', () => {
                if (audio.paused) {
                    // Pause all other audios
                    document.querySelectorAll('audio').forEach(a => a.pause());
                    audio.play();
                    icon.className = 'fas fa-pause text-xs';
                } else {
                    audio.pause();
                    icon.className = 'fas fa-play text-xs';
                }
            });

            player.querySelector('.progress-bar-container').addEventListener('click', (e) => {
                const rect = e.currentTarget.getBoundingClientRect();
                const pct = (e.clientX - rect.left) / rect.width;
                audio.currentTime = pct * audio.duration;
            });
        });
    }

    messageInput.addEventListener('input', function() {
        if (this.value.trim().length > 0) {
            micBtn.classList.add('hidden');
            sendBtn.classList.remove('hidden');
        } else {
            micBtn.classList.remove('hidden');
            sendBtn.classList.add('hidden');
        }
    });

    messageForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const text = messageInput.value.trim();
        const attachmentId = document.getElementById('hiddenAttachmentId')?.value;
        const messageType = document.getElementById('hiddenMessageType')?.value || 'text';

        if (!activeContactId) {
            alert("Please select a contact first.");
            return;
        }
        if (!text && !attachmentId) return;

        const formData = new FormData();
        formData.append('receiver_id', activeContactId);
        formData.append('message', text);
        formData.append('attachment_id', attachmentId || '');
        formData.append('type', messageType);

        messageInput.value = '';
        micBtn.classList.remove('hidden');
        sendBtn.classList.add('hidden');
        messageInput.focus();

        try {
            const response = await fetch('<?= APP_URL ?>/messages/send', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                fetchMessages(true);
                // Reset attachment state
                if (document.getElementById('hiddenAttachmentId')) {
                    document.getElementById('hiddenAttachmentId').value = '';
                }
                if (document.getElementById('hiddenMessageType')) {
                    document.getElementById('hiddenMessageType').value = 'text';
                }
            } else {
                alert("Server error: " + (data.error || "Unknown error"));
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert("Failed to send message.");
        }
    });

    function escapeHtml(unsafe) {
        return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    const micBtn = document.getElementById('micBtn');
    const sendBtn = document.getElementById('sendBtn');
    const recordingBar = document.getElementById('recordingBar');
    const recordTimerDisplay = document.getElementById('recordTimerDisplay');
    const btnDeleteRecord = document.getElementById('btnDeleteRecord');
    const btnFinishRecord = document.getElementById('btnFinishRecord');

    attachmentMenuBtn.addEventListener('click', () => attachmentMenu.classList.toggle('hidden'));
    document.addEventListener('click', (e) => {
        if (!attachmentMenuBtn.contains(e.target) && !attachmentMenu.contains(e.target)) {
            attachmentMenu.classList.add('hidden');
        }
    });

    btnUploadFile.addEventListener('click', async () => {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*,application/pdf,.doc,.docx,audio/*';
        input.onchange = async (e) => {
            const file = e.target.files[0];
            if (!file) return;
            const formData = new FormData();
            formData.append('file', file);
            try {
                const response = await fetch('<?= APP_URL ?>/messages/uploadFile', { method: 'POST', body: formData });
                const data = await response.json();
                if (data.success) {
                    let hId = document.getElementById('hiddenAttachmentId') || document.createElement('input');
                    hId.type = 'hidden'; hId.id = 'hiddenAttachmentId'; hId.value = data.attachment_id;
                    if (!hId.parentElement) messageForm.appendChild(hId);
                    let hType = document.getElementById('hiddenMessageType') || document.createElement('input');
                    hType.type = 'hidden'; hType.id = 'hiddenMessageType'; hType.value = 'file';
                    if (!hType.parentElement) messageForm.appendChild(hType);
                    alert('File uploaded! Now click send.');
                    attachmentMenu.classList.add('hidden');
                    micBtn.classList.add('hidden');
                    sendBtn.classList.remove('hidden');
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (error) { alert('Upload failed.'); }
        };
        input.click();
    });

    let mediaRecorder, audioChunks = [], timerInterval, startTime;

    micBtn.addEventListener('click', async () => {
        if (!activeContactId) return;
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];
            mediaRecorder.ondataavailable = (e) => audioChunks.push(e.data);
            mediaRecorder.onstop = async () => {
                if (audioChunks.length > 0) {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    await uploadVoiceNote(audioBlob);
                }
            };
            mediaRecorder.start();
            startTime = Date.now();
            recordingBar.style.display = 'flex';
            timerInterval = setInterval(() => {
                const elapsed = Math.floor((Date.now() - startTime) / 1000);
                recordTimerDisplay.textContent = `${Math.floor(elapsed/60).toString().padStart(2,'0')}:${(elapsed%60).toString().padStart(2,'0')}`;
            }, 1000);
        } catch (e) { alert('Mic access denied.'); }
    });

    btnDeleteRecord.addEventListener('click', () => {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            audioChunks = []; // Clear chunks so onstop doesn't upload
            mediaRecorder.stop();
        }
        stopRecordingUI();
    });

    btnFinishRecord.addEventListener('click', () => {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }
        stopRecordingUI();
    });

    function stopRecordingUI() {
        clearInterval(timerInterval);
        recordingBar.style.display = 'none';
        recordTimerDisplay.textContent = '00:00';
    }

    async function uploadVoiceNote(blob) {
        const formData = new FormData();
        formData.append('file', blob, 'voice_note.webm');
        try {
            const response = await fetch('<?= APP_URL ?>/messages/uploadFile', { method: 'POST', body: formData });
            const data = await response.json();
            if (data.success) {
                let hId = document.getElementById('hiddenAttachmentId') || document.createElement('input');
                hId.type = 'hidden'; hId.id = 'hiddenAttachmentId'; hId.value = data.attachment_id;
                if (!hId.parentElement) messageForm.appendChild(hId);
                let hType = document.getElementById('hiddenMessageType') || document.createElement('input');
                hType.type = 'hidden'; hType.id = 'hiddenMessageType'; hType.value = 'voice_note';
                if (!hType.parentElement) messageForm.appendChild(hType);
                messageForm.dispatchEvent(new Event('submit'));
            } else {
                alert("Voice upload failed: " + (data.error || "Unknown error"));
            }
        } catch (e) { 
            console.error(e); 
            alert("Voice upload error: " + e.message);
        }
    }

    const voiceCallBtn = document.getElementById('voiceCallBtn');
    const videoCallBtn = document.getElementById('videoCallBtn');
    const callModal = document.getElementById('callModal');
    const callAvatarLarge = document.getElementById('callAvatarLarge');
    const callNameLarge = document.getElementById('callNameLarge');
    const callStatusText = document.getElementById('callStatusText');
    const btnDeclineCall = document.getElementById('btnDeclineCall');
    const btnAcceptCall = document.getElementById('btnAcceptCall');
    const btnEndCall = document.getElementById('btnEndCall');

    let agoraClient = null;
    let localTracks = {
        audioTrack: null,
        videoTrack: null
    };
    let remoteUsers = {};

    async function initAgora(type) {
        if (!agoraAppId) {
            alert('Agora App ID is not configured. Please contact the administrator.');
            return;
        }

        const channel = `chat_call_${Math.min(currentUserId, activeContactId)}_${Math.max(currentUserId, activeContactId)}`;
        const token = null; // In production, you should use a token server

        agoraClient = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });

        // Handle remote users
        agoraClient.on("user-published", async (user, mediaType) => {
            await agoraClient.subscribe(user, mediaType);
            if (mediaType === "video") {
                const remoteVideoTrack = user.videoTrack;
                const remotePlayerContainer = document.createElement("div");
                remotePlayerContainer.id = `user-${user.uid}`;
                remotePlayerContainer.className = "w-full h-full absolute inset-0 bg-black";
                document.getElementById('callModal').appendChild(remotePlayerContainer);
                remoteVideoTrack.play(remotePlayerContainer);
            }
            if (mediaType === "audio") {
                user.audioTrack.play();
            }
        });

        try {
            const uid = await agoraClient.join(agoraAppId, channel, token, currentUserId);
            
            // Create and publish local tracks
            localTracks.audioTrack = await AgoraRTC.createMicrophoneAudioTrack();
            if (type === 'video') {
                localTracks.videoTrack = await AgoraRTC.createCameraVideoTrack();
                const localPlayerContainer = document.createElement("div");
                localPlayerContainer.id = "local-player";
                localPlayerContainer.className = "w-32 h-32 absolute bottom-24 right-4 bg-black rounded-xl overflow-hidden border-2 border-white/20 z-10";
                document.getElementById('callModal').appendChild(localPlayerContainer);
                localTracks.videoTrack.play(localPlayerContainer);
                await agoraClient.publish([localTracks.audioTrack, localTracks.videoTrack]);
            } else {
                await agoraClient.publish([localTracks.audioTrack]);
            }

            btnEndCall.classList.remove('hidden');
            callStatusText.textContent = 'In Call...';
        } catch (e) { 
            console.error(e);
            alert('Call connection failed: ' + e.message); 
            callModal.classList.add('hidden');
        }
    }

    voiceCallBtn.addEventListener('click', () => { if(activeContactId) { initAgora('voice'); showCallModal('Voice Call'); } });
    videoCallBtn.addEventListener('click', () => { if(activeContactId) { initAgora('video'); showCallModal('Video Call'); } });

    function showCallModal(type) {
        callAvatarLarge.textContent = chatName.textContent.substring(0, 1).toUpperCase();
        callNameLarge.textContent = chatName.textContent;
        callStatusText.textContent = `${type}...`;
        callModal.classList.remove('hidden');
    }

    btnDeclineCall.addEventListener('click', () => callModal.classList.add('hidden'));
    btnAcceptCall.addEventListener('click', () => {
        btnAcceptCall.classList.add('hidden');
        btnDeclineCall.classList.add('hidden');
        btnEndCall.classList.remove('hidden');
        callStatusText.textContent = 'Connected';
    });
    btnEndCall.addEventListener('click', async () => {
        // Stop and close local tracks
        for (let trackName in localTracks) {
            var track = localTracks[trackName];
            if (track) {
                track.stop();
                track.close();
                localTracks[trackName] = null;
            }
        }

        // Remove video players
        const localPlayer = document.getElementById('local-player');
        if (localPlayer) localPlayer.remove();
        
        // Leave the channel
        if (agoraClient) {
            await agoraClient.leave();
            agoraClient = null;
        }

        callModal.classList.add('hidden');
        btnAcceptCall.classList.remove('hidden');
        btnDeclineCall.classList.remove('hidden');
        btnEndCall.classList.add('hidden');
    });
</script>