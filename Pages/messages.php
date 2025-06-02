<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Messages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-light: #e9ecef;
            --gray-medium: #adb5bd;
            --accent-color: #4895ef;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
        }
        
        #chat-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            height: 100vh;
        }
        
        #senders-sidebar {
            background-color: white;
            border-right: 1px solid var(--gray-light);
            overflow-y: auto;
        }
        
        #senders-header {
            padding: 15px 20px;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        #senders-list {
            list-style: none;
        }
        
        .sender-item {
            padding: 15px 20px;
            border-bottom: 1px solid var(--gray-light);
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .sender-item:hover {
            background-color: var(--gray-light);
        }
        
        .sender-item.active {
            background-color: #e6f0ff;
            border-left: 3px solid var(--primary-color);
        }
        
        .sender-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--accent-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .sender-info {
            flex: 1;
        }
        
        .sender-name {
            font-weight: 600;
            margin-bottom: 3px;
        }
        
        .sender-last-message {
            font-size: 0.85rem;
            color: var(--gray-medium);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .sender-time {
            font-size: 0.75rem;
            color: var(--gray-medium);
        }
        
        .unread-count {
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
        }
        
        #chat-area {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        
        #chat-header {
            padding: 15px 25px;
            background-color: white;
            border-bottom: 1px solid var(--gray-light);
            display: flex;
            align-items: center;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 5;
        }
        
        #current-sender {
            font-weight: 600;
        }
        
        #messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: #f9f9f9;
        }
        
        .message {
            margin-bottom: 15px;
            max-width: 70%;
            padding: 12px 15px;
            border-radius: 15px;
            position: relative;
            word-wrap: break-word;
        }
        
        .message.received {
            background-color: white;
            border: 1px solid var(--gray-light);
            margin-right: auto;
        }
        
        .message.sent {
            background-color: var(--primary-color);
            color: white;
            margin-left: auto;
        }
        
        .message .sender {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .message .time {
            font-size: 0.7rem;
            color: var(--gray-medium);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .message.sent .time {
            color: rgba(255, 255, 255, 0.8);
        }
        
        #message-form {
            padding: 15px;
            background-color: white;
            border-top: 1px solid var(--gray-light);
            display: flex;
            gap: 10px;
        }
        
        #message-input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid var(--gray-light);
            border-radius: 25px;
            outline: none;
            font-size: 1rem;
        }
        
        #message-input:focus {
            border-color: var(--primary-color);
        }
        
        #send-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--gray-medium);
            text-align: center;
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--gray-light);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--gray-medium);
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div id="chat-container">
        <!-- Senders Sidebar -->
        <div id="senders-sidebar">
            <div id="senders-header">
                <i class="fas fa-inbox"></i>
                <h3>My Conversations</h3>
            </div>
            <ul id="senders-list">
                <!-- Dynamically populated with JavaScript -->
                <li class="empty-state">
                    <i class="fas fa-comment-alt" style="font-size: 2rem; margin-bottom: 10px;"></i>
                    <p>No conversations yet</p>
                </li>
            </ul>
        </div>
        
        <!-- Main Chat Area -->
        <div id="chat-area">
            <div id="chat-header">
                <div class="sender-avatar" id="current-sender-avatar">?</div>
                <div>
                    <div id="current-sender">Select a conversation</div>
                    <div id="sender-status" style="font-size: 0.8rem; color: var(--gray-medium);">Online</div>
                </div>
            </div>
            
            <div id="messages">
                <div class="empty-state">
                    <i class="fas fa-comments" style="font-size: 2rem; margin-bottom: 10px;"></i>
                    <p>Select a conversation to view messages</p>
                </div>
            </div>
            
            <div id="message-form" style="display: none;">
                <input type="text" id="message-input" placeholder="Type your message..." autocomplete="off">
                <button id="send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        const userId = <?= $_SESSION['user_id'] ?>;
        const username = "<?= $_SESSION['username'] ?>";
        const ws = new WebSocket('ws://localhost:8080');
        
        let currentSenderId = null;
        let conversations = {};
        
        // Register user with WebSocket server
        ws.onopen = function() {
            ws.send(JSON.stringify({
                type: 'register',
                userId: userId,
                username: username
            }));
            
            // Load conversations (people who have messaged you)
            fetch('api.php?action=get_conversations&user_id=' + userId)
                .then(response => response.json())
                .then(data => {
                    conversations = data.conversations;
                    updateSendersList();
                    
                    if (data.conversations.length > 0) {
                        // Load first conversation by default
                        loadConversation(data.conversations[0].sender_id);
                    }
                });
        };
        
        // Handle incoming messages
        ws.onmessage = function(event) {
            const data = JSON.parse(event.data);
            
            if (data.type === 'message') {
                if (data.to === userId || data.from === userId) {
                    handleNewMessage(data);
                }
            } else if (data.type === 'conversation_update') {
                // Update conversations list when a new message is received
                fetch('api.php?action=get_conversations&user_id=' + userId)
                    .then(response => response.json())
                    .then(data => {
                        conversations = data.conversations;
                        updateSendersList();
                    });
            }
        };
        
        // Update senders list in sidebar
        function updateSendersList() {
            const sendersList = document.getElementById('senders-list');
            
            if (conversations.length === 0) {
                sendersList.innerHTML = `
                    <li class="empty-state">
                        <i class="fas fa-comment-alt" style="font-size: 2rem; margin-bottom: 10px;"></i>
                        <p>No conversations yet</p>
                    </li>
                `;
                return;
            }
            
            sendersList.innerHTML = '';
            
            conversations.forEach(convo => {
                const li = document.createElement('li');
                li.className = 'sender-item' + (currentSenderId === convo.sender_id ? ' active' : '');
                li.dataset.senderId = convo.sender_id;
                
                li.innerHTML = `
                    <div class="sender-avatar">${convo.sender_name.charAt(0).toUpperCase()}</div>
                    <div class="sender-info">
                        <div class="sender-name">${convo.sender_name}</div>
                        <div class="sender-last-message">${convo.last_message || ''}</div>
                    </div>
                    <div class="sender-time">${formatTime(convo.last_message_time)}</div>
                    ${convo.unread_count > 0 ? `<div class="unread-count">${convo.unread_count}</div>` : ''}
                `;
                
                li.addEventListener('click', () => loadConversation(convo.sender_id));
                sendersList.appendChild(li);
            });
        }
        
        // Load conversation with a specific sender
        function loadConversation(senderId) {
            currentSenderId = senderId;
            
            // Update active state in sidebar
            document.querySelectorAll('.sender-item').forEach(item => {
                item.classList.toggle('active', item.dataset.senderId === senderId);
            });
            
            // Get sender info from conversations
            const sender = conversations.find(c => c.sender_id == senderId);
            
            // Update chat header
            document.getElementById('current-sender').textContent = sender.sender_name;
            document.getElementById('current-sender-avatar').textContent = sender.sender_name.charAt(0).toUpperCase();
            
            // Show message form
            document.getElementById('message-form').style.display = 'flex';
            
            // Load messages for this conversation
            fetch(`api.php?action=get_messages&user_id=${userId}&sender_id=${senderId}`)
                .then(response => response.json())
                .then(data => {
                    const messagesDiv = document.getElementById('messages');
                    messagesDiv.innerHTML = '';
                    
                    if (data.messages.length === 0) {
                        messagesDiv.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-comments" style="font-size: 2rem; margin-bottom: 10px;"></i>
                                <p>No messages with ${sender.sender_name} yet</p>
                            </div>
                        `;
                        return;
                    }
                    
                    data.messages.forEach(msg => {
                        displayMessage(msg);
                    });
                    
                    // Mark messages as read
                    if (sender.unread_count > 0) {
                        fetch(`api.php?action=mark_as_read&user_id=${userId}&sender_id=${senderId}`);
                        updateSendersList(); // Refresh unread counts
                    }
                });
        }
        
        // Display a single message
        function displayMessage(msg) {
            const messagesDiv = document.getElementById('messages');
            const messageDiv = document.createElement('div');
            
            const isReceived = msg.from !== userId;
            messageDiv.className = `message ${isReceived ? 'received' : 'sent'}`;
            
            messageDiv.innerHTML = `
                ${isReceived ? `<div class="sender">${msg.fromName}</div>` : ''}
                <div>${msg.content}</div>
                <div class="time">
                    <i class="far fa-clock"></i>
                    ${formatTime(msg.timestamp)}
                </div>
            `;
            
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }
        
        // Handle new incoming message
        function handleNewMessage(msg) {
            // Check if this is part of current conversation
            const isCurrentConversation = 
                (msg.from === currentSenderId && msg.to === userId) || 
                (msg.to === currentSenderId && msg.from === userId);
            
            if (isCurrentConversation) {
                displayMessage(msg);
            }
            
            // Update conversations list
            fetch('api.php?action=get_conversations&user_id=' + userId)
                .then(response => response.json())
                .then(data => {
                    conversations = data.conversations;
                    updateSendersList();
                });
        }
        
        // Send message
        document.getElementById('send-btn').addEventListener('click', sendMessage);
        document.getElementById('message-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') sendMessage();
        });
        
        function sendMessage() {
            const input = document.getElementById('message-input');
            const message = input.value.trim();
            
            if (message && currentSenderId) {
                const messageData = {
                    type: 'message',
                    content: message,
                    to: currentSenderId,
                    from: userId,
                    fromName: username
                };
                
                ws.send(JSON.stringify(messageData));
                
                // Add message to UI immediately (optimistic update)
                displayMessage({
                    ...messageData,
                    timestamp: new Date().toISOString()
                });
                
                input.value = '';
            }
        }
        
        // Helper function to format time
        function formatTime(timestamp) {
            if (!timestamp) return '';
            
            const date = new Date(timestamp);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
    </script>
</body>
</html>