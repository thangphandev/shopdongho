<?php
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}

$connect = new Connect();
$chatUsers = $connect->getChatUsers();
$selectedUserId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$chatHistory = $selectedUserId ? $connect->getChatHistory($selectedUserId) : null;
$selectedUser = $selectedUserId ? $connect->getUserById($selectedUserId) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    
</body>
</html>
<div class="container-fluid">
    <div class="row">
        <!-- User List Sidebar -->
        <div class="col-md-4 col-lg-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Danh sách người dùng</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush user-list">
                        <?php foreach ($chatUsers as $user): ?>
                            <a href="?page=tinnhan&user_id=<?php echo $user['idnguoidung']; ?>" 
                               class="list-group-item list-group-item-action <?php echo ($selectedUserId == $user['idnguoidung']) ? 'active' : ''; ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($user['tendangnhap']); ?></h6>
                                        <small class="text-muted">ID: <?php echo $user['idnguoidung']; ?></small>
                                    </div>
                                    <small class="text-muted"><?php echo date('d/m/Y', strtotime($user['last_message'])); ?></small>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-md-8 col-lg-9">
            <?php if ($selectedUserId && $selectedUser): ?>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Chat với <?php echo htmlspecialchars($selectedUser['tendangnhap']); ?></h5>
                    </div>
                    <div class="card-body chat-body p-0">
                        <div class="chat-messages p-3" id="chatMessages">
                        <?php foreach ($chatHistory as $message): ?>
                            <div class="message <?php echo ($message['role'] == 0) ? 'user-message' : 'admin-message'; ?> mb-3">
                                <div class="message-content">
                                    <?php 
                                    if (strpos($message['noidungchat'], '<div class="product-list"') !== false) {
                                        // Fix image paths and product links
                                        $content = preg_replace([
                                            '/(src=")(imageproduct\/)/',
                                            '/(href=")(chi_tiet_san_pham\.php\?id=)/'
                                        ], [
                                            '$1../$2',
                                            '$1/saveweb/admin/admin.php?page=product_form&id='
                                        ], $message['noidungchat']);
                                        echo $content;
                                    } else {
                                        echo nl2br(htmlspecialchars($message['noidungchat']));
                                    }
                                    ?>
                                    <div class="message-time text-muted small">
                                        <?php echo date('d/m/Y H:i', strtotime($message['thoigian'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                        <div class="chat-input p-3 border-top">
                            <form id="adminChatForm">
                                <input type="hidden" name="user_id" value="<?php echo $selectedUserId; ?>">
                                <div class="input-group">
                                    <textarea class="form-control" id="messageInput" rows="2" placeholder="Nhập tin nhắn..."></textarea>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Chọn một người dùng để xem cuộc trò chuyện</h5>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.user-list {
    max-height: 600px;
    overflow-y: auto;
}

.chat-body {
    height: 600px;
    display: flex;
    flex-direction: column;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    background-color: #f8f9fa;
}

.message {
    display: flex;
    margin-bottom: 15px;
}

.user-message {
    justify-content: flex-start;
}

.admin-message {
    justify-content: flex-end;
}

.message-content {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 15px;
    position: relative;
}

.user-message .message-content {
    background-color: #e9ecef;
    color: #212529;
}

.admin-message .message-content {
    background-color: #007bff;
    color: white;
}

.message-time {
    margin-top: 5px;
    font-size: 12px;
}

.chat-input {
    background-color: #fff;
}

.message-content .product-list {
    margin-top: 10px;
    margin-bottom: 10px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    max-width: 600px;
}

.message-content .product-card {
    flex: 0 1 calc(50% - 5px);
    min-width: 200px;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 8px;
    text-decoration: none;
    color: inherit;
    display: flex;
    align-items: center;
    transition: transform 0.2s, box-shadow 0.2s;
}

.message-content .product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.message-content .product-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 10px;
}

.message-content .product-name {
    font-size: 14px;
    font-weight: 500;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Adjust message content for products */
.message-content {
    max-width: 85%;
}

.shop-message .message-content,
.admin-message .message-content {
    background-color: white;
    color: #333;
}

@media (max-width: 768px) {
    .message-content .product-card {
        flex: 0 1 100%;
    }
}
</style>

<script>
$(document).ready(function() {
    // Kiểm tra jQuery
    if (typeof $ === 'undefined') {
        console.error('jQuery không được tải!');
        return;
    }

    // Cuộn xuống cuối đoạn chat
    function scrollToBottom() {
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }
    
    // Cuộn xuống khi trang được tải
    scrollToBottom();

    // Xử lý gửi tin nhắn
    $('#adminChatForm').on('submit', function(e) {
        e.preventDefault();
        
        const messageInput = $('#messageInput');
        const message = messageInput.val().trim();
        const userId = $('input[name="user_id"]').val();
        
        if (!message) return;

        // Hiển thị tin nhắn ngay lập tức
        let messageContent = message;
        if (message.includes('<div class="product-list"')) {
            messageContent = message.replace(
                /(src=")(imageproduct\/)/g, 
                '$1../$2'
            ).replace(
                /(href=")(chi_tiet_san_pham\.php\?id=)/g,
                '$1/saveweb/admin/admin.php?page=product_form&id='
            );
        } else {
            messageContent = message.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
        }

        // Thêm tin nhắn vào giao diện
        $('#chatMessages').append(`
            <div class="message admin-message mb-3">
                <div class="message-content">
                    ${messageContent}
                    <div class="message-time text-muted small">
                        ${new Date().toLocaleString('vi-VN')}
                    </div>
                </div>
            </div>
        `);
        
        // Xóa nội dung input và cuộn xuống
        messageInput.val('');
        scrollToBottom();

        // Gửi tin nhắn qua AJAX
        $.ajax({
            url: 'admin_send_message.php',
            type: 'POST',
            dataType: 'json',
            data: {
                user_id: userId,
                message: message
            },
            success: function(response) {
                if (response && response.success) {
                    // Cập nhật thời gian tin nhắn từ server
                    const lastMessage = $('#chatMessages .message:last-child .message-time');
                    if (lastMessage.length && response.time) {
                        lastMessage.text(new Date(response.time).toLocaleString('vi-VN'));
                    }
                } else {
                    console.error('Lỗi:', response ? response.message : 'Không xác định');
                    alert('Không thể gửi tin nhắn. Vui lòng thử lại.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Lỗi AJAX:', error);
                console.log('Response:', xhr.responseText); // Thêm log để debug
                alert('Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại.');
            }
        });
    });

    // Xử lý phím Enter
    $('#messageInput').on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            $('#adminChatForm').submit();
        }
    });
});
</script>