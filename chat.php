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
                    <h2>Chatbot AI</h2>                   
                </div>
                
                <div class="chat-body">
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
                                        <?php echo $message['noidungchat']; ?>
                                        <span class="message-time"><?php echo date('H:i', strtotime($message['thoigian'])); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="chat-input">
                        <form id="chatForm">
                            <textarea id="messageInput" placeholder="Nhập tin nhắn..." required rows="2"></textarea>
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
.product-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.product-card {
    display: flex;
    align-items: center;
    background-color: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 8px;
    text-decoration: none;
    color: #333;
    width: 200px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.product-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 10px;
}

.product-name {
    font-size: 14px;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-page-container {
    padding-top: 100px;
    position: relative;
    z-index: 1;
    margin-top: 30px;
    max-width: 1300px;
    height: 100%;
}

.chat-container {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 50px;
    font-size: 16px;
    position: relative;
    z-index: 10;
    width: 100%;
}

.chat-header {
    background-color: #c8a96a;
    color: #fff;
    padding: 10px;
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
    height: 550px;
}

.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background-color: #f5f5f5;
    height: 100%;
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
    white-space: pre-wrap;
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

.chat-input textarea {
    flex: 1;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 15px;
    outline: none;
    font-size: 16px;
    resize: none;
    line-height: 1.5;
}

.chat-input textarea:focus {
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

.addtocart-btn {
    margin-top: 12px;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    font-size: 14px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-transform: uppercase;
    transition: background-color 0.3s ease;
}

.addtocart-btn:hover {
    background-color: #0056b3;
}


.product-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.product-card {
    width: 240px;
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    background: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.product-card a {
    text-decoration: none;
    color: inherit;
    display: block;
    width: 100%;
}
.product-name {
    margin-top: 10px;
    font-weight: bold;
    font-size: 16px;
}
.product-image {
    width: 100%;
    height: 200px; /* Chiều cao cố định */
    object-fit: cover; /* Giữ tỉ lệ, cắt bớt nếu cần để vừa khung */
    border-radius: 5px;
    background-color: #f9f9f9; /* màu nền phòng khi ảnh quá nhỏ */
}

@keyframes blink {
    50% {
        opacity: 1;
    }
}

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

    // Handle Enter key without Shift
    $('#messageInput').on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            $('#chatForm').submit();
        }
    });

    // Send message
    $('#chatForm').submit(function(e) {
        e.preventDefault();
        
        const messageInput = $('#messageInput');
        const message = messageInput.val().trim();
        
        if (message) {
            // Add user message to UI
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
                        // Add bot response to UI (hiển thị trực tiếp HTML từ API)
                        const currentTime = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        $('#chatMessages').append(`
                            <div class="message shop-message">
                                <div class="message-content">
                                    ${response.botResponse}
                                    <span class="message-time">${currentTime}</span>
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
                                    <span class="message-time">${currentTime}</span>
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
                                <span class="message-time">${currentTime}</span>
                                </div>
                            </div>
                        `);
                        scrollToBottom();
                    }
                });
        }
    });
});
function addToCart(productId) {
    const quantity = 1;

    $.ajax({
        url: 'update_gio_hang.php',
        type: 'POST',
        data: {
            product_id: productId,
            quantity: quantity,
            action: 'add'
        },
        dataType: 'json', // Tell jQuery to parse the response as JSON
        success: function(data) { // Now data is already a JavaScript object
            if (data.success) {
                // Update cart count immediately
                $.ajax({
                    url: 'lay_so_luong_san_pham.php',
                    type: 'GET',
                    dataType: 'json',
                    cache: false,
                    success: function(cartData) {
                        // Update the cart count in the header
                        $('.cart-count').text(cartData.count);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching cart count:", error);
                    }
                });
                
                Swal.fire({
                    position: "top-end",
                    icon: "success",
                    title: "Thêm sản phẩm thành công",
                    showConfirmButton: false,
                    timer: 1200
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Lỗi",
                    text: data.message || 'Thêm vào giỏ hàng thất bại'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error("Error adding to cart:", error);
            Swal.fire({
                icon: "error",
                title: "Lỗi",
                text: "Đã xảy ra lỗi khi thêm sản phẩm vào giỏ hàng"
            });
        }
    });
}
</script>