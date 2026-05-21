    <!-- Chatbot UI -->
    <div id="chatbot">
        <div id="chat-window">
            <div id="chat-header">
                <span><i class="fas fa-robot"></i> Asisten - SI</span>
                <span style="cursor:pointer" onclick="toggleChat()">✖</span>
            </div>
            <div id="chat-messages">
                <div class="chat-bubble chat-bot">Halo! Saya Asisten - SI. Ada yang bisa saya bantu terkait sistem informasi?</div>
                <!-- Typing Indicator -->
                <div id="typing" class="typing-indicator">
                    <span></span><span></span><span></span>
                </div>
            </div>
            <div id="chat-input-area">
                <input type="text" id="chat-input" placeholder="Ketik pesan..." onkeypress="handleKeyPress(event)">
                <button id="chat-send" onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
        <button class="btn" style="width:100%; border-radius:30px; padding:15px; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);" onclick="toggleChat()"><i class="fas fa-comments"></i> Chat dengan Asisten - SI</button>
    </div>

    <script>
        function toggleChat() {
            var chatWindow = document.getElementById('chat-window');
            if (chatWindow.style.display === 'none' || chatWindow.style.display === '') {
                chatWindow.style.display = 'flex';
                document.getElementById('chat-input').focus();
            } else {
                chatWindow.style.display = 'none';
            }
        }

        function handleKeyPress(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        }

        function appendMessage(msg, sender) {
            var msgDiv = document.createElement('div');
            msgDiv.className = 'chat-bubble ' + (sender === 'bot' ? 'chat-bot' : 'chat-user');
            msgDiv.innerText = msg;
            
            // Insert before typing indicator
            var typing = document.getElementById('typing');
            document.getElementById('chat-messages').insertBefore(msgDiv, typing);
            
            var chatMessages = document.getElementById('chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function sendMessage() {
            var input = document.getElementById('chat-input');
            var msg = input.value.trim();
            if (msg === '') return;
            
            appendMessage(msg, 'user');
            input.value = '';

            // Show typing indicator
            var typing = document.getElementById('typing');
            typing.style.display = 'block';
            var chatMessages = document.getElementById('chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;

            $.post("<?= base_url('chatbot/reply') ?>", {message: msg}, function(data) {
                // Hide typing indicator
                typing.style.display = 'none';
                
                var response = (typeof data === 'object') ? data : JSON.parse(data);
                appendMessage(response.reply, 'bot');
            }).fail(function() {
                typing.style.display = 'none';
                appendMessage("Maaf, terjadi kesalahan koneksi.", "bot");
            });
        }
    </script>
</body>
</html>
