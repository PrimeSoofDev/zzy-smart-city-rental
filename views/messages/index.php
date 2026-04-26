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
        <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50" id="chatThread">
            <div class="flex items-center justify-center h-full text-gray-400">
                <p>Loading messages...</p>
            </div>
        </div>

        <!-- Message Input -->
        <div class="p-4 bg-white border-t border-gray-100">
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
                        <button type="button" id="btnRecordVoice" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 flex items-center gap-3">
                            <i class="fas fa-microphone text-red-500"></i> Record Voice Note
                        </button>
                    </div>
                </div>

                <input type="text" id="messageInput" name="message" autocomplete="off"
                       placeholder="Type your message here..."
                       class="flex-1 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition-colors outline-none">

                <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-xl text-sm px-5 py-3 text-center transition-colors shadow-md flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> <span class="hidden sm:inline">Send</span>
                </button>
            </form>
        </div>

        <!-- Voice Recording Overlay -->
        <div id="voiceRecorder" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-3xl p-6 w-full max-w-sm shadow-2xl text-center">
                <div class="mb-6">
                    <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                        <i class="fas fa-microphone text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Recording Voice Note</h3>
                    <p class="text-sm text-gray-500" id="recordTimer">00:00</p>
                </div>
                <div class="flex justify-center gap-4">
                    <button type="button" id="btnCancelRecord" class="px-6 py-2 rounded-full text-gray-500 font-semibold hover:bg-gray-100 transition-colors">Cancel</button>
                    <button type="button" id="btnStopRecord" class="px-6 py-2 rounded-full bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors">Stop & Send</button>
                </div>
            </div>
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

        messages.forEach(msg => {
            const isMe = msg.sender_id == currentUserId;
            const dateObj = new Date(msg.created_at);
            const timeStr = dateObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            const dateStr = dateObj.toLocaleDateString();

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

            let contentHtml = '';
            if (msg.type === 'file') {
                if (msg.file_type && msg.file_type.startsWith('image/')) {
                    contentHtml = `<img src="${msg.file_path}" class="max-w-full rounded-lg mb-1 cursor-pointer hover:opacity-90 transition-opacity" onclick="window.open('${msg.file_path}', '_blank')">`;
                } else {
                    contentHtml = `
                        <div class="flex items-center gap-3 p-2 bg-gray-50 rounded-lg border border-gray-100 cursor-pointer hover:bg-gray-100 transition-colors" onclick="window.open('${msg.file_path}', '_blank')">
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
                    <div class="flex items-center gap-3 p-2 bg-blue-50 rounded-lg border border-blue-100">
                        <audio controls class="h-8 w-full max-w-[200px]">
                            <source src="${msg.file_path}" type="audio/webm">
                            Your browser does not support the audio element.
                        </audio>
                    </div>`;
            } else {
                contentHtml = `<p class="text-sm break-words whitespace-pre-wrap">${escapeHtml(msg.message || '')}</p>`;
            }

            if (isMe) {
                html += `
                    <div class="flex justify-end mb-4 animate-fade-in">
                        <div class="bg-blue-600 text-white rounded-2xl rounded-tr-sm py-2.5 px-4 max-w-[75%] shadow-sm">
                            ${contentHtml}
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
                            ${contentHtml}
                            <p class="text-[10px] text-gray-400 text-left mt-1.5">${timeStr}</p>
                        </div>
                    </div>
                `;
            }
        });

        if (chatThread.innerHTML !== html) {
            const isScrolledToBottom = chatThread.scrollHeight - chatThread.clientHeight <= chatThread.scrollTop + 50;
            chatThread.innerHTML = html;
            if (scrollToBottom || isScrolledToBottom) {
                chatThread.scrollTop = chatThread.scrollHeight;
            }
        }
    }

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

    const attachmentMenuBtn = document.getElementById('attachmentMenuBtn');
    const attachmentMenu = document.getElementById('attachmentMenu');
    const btnUploadFile = document.getElementById('btnUploadFile');
    const btnRecordVoice = document.getElementById('btnRecordVoice');
    const voiceRecorder = document.getElementById('voiceRecorder');
    const recordTimer = document.getElementById('recordTimer');
    const btnCancelRecord = document.getElementById('btnCancelRecord');
    const btnStopRecord = document.getElementById('btnStopRecord');

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
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (error) { alert('Upload failed.'); }
        };
        input.click();
    });

    let mediaRecorder, audioChunks = [], timerInterval, startTime;
    btnRecordVoice.addEventListener('click', async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];
            mediaRecorder.ondataavailable = (e) => audioChunks.push(e.data);
            mediaRecorder.onstop = async () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                await uploadVoiceNote(audioBlob);
            };
            mediaRecorder.start();
            startTime = Date.now();
            voiceRecorder.classList.remove('hidden');
            attachmentMenu.classList.add('hidden');
            timerInterval = setInterval(() => {
                const elapsed = Math.floor((Date.now() - startTime) / 1000);
                recordTimer.textContent = `${Math.floor(elapsed/60).toString().padStart(2,'0')}:${(elapsed%60).toString().padStart(2,'0')}`;
            }, 1000);
        } catch (e) { alert('Mic access denied.'); }
    });

    btnCancelRecord.addEventListener('click', () => {
        if (mediaRecorder) mediaRecorder.stop();
        clearInterval(timerInterval);
        voiceRecorder.classList.add('hidden');
    });

    btnStopRecord.addEventListener('click', () => {
        if (mediaRecorder) mediaRecorder.stop();
        clearInterval(timerInterval);
        voiceRecorder.classList.add('hidden');
    });

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
            }
        } catch (e) { console.error(e); }
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