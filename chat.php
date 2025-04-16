<?php
require_once 'connect.php';
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

// If not logged in, redirect to login page
if (!$isLoggedIn) {
    header('Location: login.php?redirect=chat.php');
    exit();
}

$connect = new Connect();
$user = $connect->getUserById($userId);

// Get chat history if available
$chatHistory = $connect->getChatHistory($userId);

include 'header.php';
?>

<div class="container chat-page-container mt-5 mb-5">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="chat-container">
                <div class="chat-header">
                    <h2>Chat với Shop</h2>
                    <p>Chúng tôi luôn sẵn sàng hỗ trợ bạn</p>
                </div>
                
                <div class="chat-body">
                    <!-- Rest of the chat body remains the same -->
                    <!-- ... existing code ... -->
                    <div class="chat-messages" id="chatMessages">
                        <?php if (empty($chatHistory)): ?>
                            <div class="message shop-message">
                                <div class="message-content">
                                    <p>Xin chào <?php echo htmlspecialchars($user['tendangnhap']); ?>, chúng tôi có thể giúp gì cho bạn về đồng hồ cao cấp?</p>
                                    <span class="message-time"><?php echo date('H:i'); ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($chatHistory as $message): ?>
                                <div class="message <?php echo ($message['role'] == 0) ? 'user-message' : 'shop-message'; ?>">
                                    <div class="message-content">
                                        <p><?php echo htmlspecialchars($message['noidungchat']); ?></p>
                                        <span class="message-time"><?php echo date('H:i', strtotime($message['thoigian'])); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="chat-input">
                        <form id="chatForm">
                            <input type="text" id="messageInput" placeholder="Nhập tin nhắn..." required>
                            <button type="submit" id="sendButton">
                                <i class="fa fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>

.chat-page-container {
    padding-top: 120px; /* Tăng giá trị này để đẩy container xuống dưới header */
    position: relative;
    z-index: 1;
    margin-top: 30px; /* Thêm margin-top để tạo khoảng cách với header */
    max-width: 1000px;
}

.chat-container {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 50px;
    font-size: 16px;
    position: relative;
    z-index: 10; /* Higher than header */
    width: 100%;
}

.chat-header {
    background-color: #c8a96a;
    color: #fff;
    padding: 20px;
    text-align: center;
}

.chat-header h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
}

.chat-header p {
    margin: 5px 0 0;
    opacity: 0.8;
}

.chat-body {
    display: flex;
    flex-direction: column;
    height: 500px;
}

.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background-color: #f5f5f5;
}

.message {
    margin-bottom: 15px;
    display: flex;
}

.user-message {
    justify-content: flex-end;
}

.shop-message {
    justify-content: flex-start;
}

.message-content {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 18px;
    position: relative;
}

.user-message .message-content {
    background-color: #c8a96a;
    color: #fff;
    border-bottom-right-radius: 4px;
}

.shop-message .message-content {
    background-color: #e0e0e0;
    color: #333;
    border-bottom-left-radius: 4px;
}

.message-content p {
    margin: 0;
    word-wrap: break-word;
}

.message-time {
    display: block;
    font-size: 12px;
    margin-top: 5px;
    opacity: 0.7;
    text-align: right;
}

.chat-input {
    padding: 15px;
    background-color: #fff;
    border-top: 1px solid #e0e0e0;
}

.chat-input form {
    display: flex;
}

.chat-input input {
    flex: 1;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 30px;
    outline: none;
    font-size: 16px;
}

.chat-input input:focus {
    border-color: #c8a96a;
}

.chat-input button {
    background-color: #c8a96a;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 45px;
    height: 45px;
    margin-left: 10px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.chat-input button:hover {
    background-color: #b39355;
}

.typing-indicator {
    display: none;
    padding: 10px;
    background-color: #f5f5f5;
    border-radius: 10px;
    margin-bottom: 10px;
}

.typing-indicator span {
    height: 8px;
    width: 8px;
    float: left;
    margin: 0 1px;
    background-color: #9E9EA1;
    display: block;
    border-radius: 50%;
    opacity: 0.4;
}

.typing-indicator span:nth-of-type(1) {
    animation: 1s blink infinite 0.3333s;
}

.typing-indicator span:nth-of-type(2) {
    animation: 1s blink infinite 0.6666s;
}

.typing-indicator span:nth-of-type(3) {
    animation: 1s blink infinite 0.9999s;
}

@keyframes blink {
    50% {
        opacity: 1;
    }
}

/* Responsive styles */
@media (max-width: 768px) {
    .chat-container {
        margin: 15px;
    }
    
    .chat-body {
        height: 400px;
    }
    
    .message-content {
        max-width: 85%;
    }
}
</style>

<script>
$(document).ready(function() {
    // Scroll to bottom of chat
    function scrollToBottom() {
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Initial scroll to bottom
    scrollToBottom();
    
    // Send message
    $('#chatForm').submit(function(e) {
        e.preventDefault();
        
        const messageInput = $('#messageInput');
        const message = messageInput.val().trim();
        
        if (message) {
            // Add message to UI immediately
            const currentTime = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            $('#chatMessages').append(`
                <div class="message user-message">
                    <div class="message-content">
                        <p>${message}</p>
                        <span class="message-time">${currentTime}</span>
                    </div>
                </div>
            `);
            
            // Clear input
            messageInput.val('');
            
            // Scroll to bottom
            scrollToBottom();
            
            // Show typing indicator
            $('#chatMessages').append(`
                <div class="message shop-message typing-indicator" id="typingIndicator">
                    <div class="message-content">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            `);
            $('#typingIndicator').show();
            scrollToBottom();
            
            // Send to server
            $.ajax({
                url: 'send_message.php',
                type: 'POST',
                data: {
                    message: message
                },
                success: function(response) {
                    // Remove typing indicator
                    $('#typingIndicator').remove();
                    
                    if (response.success) {
                        // Add bot response to UI
                        $('#chatMessages').append(`
                            <div class="message shop-message">
                                <div class="message-content">
                                    <p>${response.botResponse}</p>
                                    <span class="message-time">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                                </div>
                            </div>
                        `);
                        scrollToBottom();
                    } else {
                        // Show error message
                        $('#chatMessages').append(`
                            <div class="message shop-message">
                                <div class="message-content">
                                    <p>Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.</p>
                                    <span class="message-time">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                                </div>
                            </div>
                        `);
                        scrollToBottom();
                    }
                },
                error: function() {
                    // Remove typing indicator
                    $('#typingIndicator').remove();
                    
                    // Show error message
                    $('#chatMessages').append(`
                        <div class="message shop-message">
                            <div class="message-content">
                                <p>Xin lỗi, có lỗi xảy ra khi kết nối với máy chủ. Vui lòng thử lại sau.</p>
                                <span class="message-time">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                            </div>
                        </div>
                    `);
                    scrollToBottom();
                }
            });
        }
    });
});
</script>

<?php include 'footer.php'; ?>

