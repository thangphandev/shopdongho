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
    <title>Quản lý tin nhắn</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
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
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Chat với <?php echo htmlspecialchars($selectedUser['tendangnhap']); ?></h5>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#productSuggestModal">
                            <i class="fas fa-box"></i> Gợi ý sản phẩm
                        </button>
                    </div>
                    <div class="card-body chat-body p-0">
                        <div class="chat-messages p-3" id="chatMessages">
                            <?php foreach ($chatHistory as $message): ?>
                                <div class="message <?php echo ($message['role'] == 0) ? 'user-message' : 'admin-message'; ?> mb-3">
                                    <div class="message-content">
                                        <?php 
                                        if (strpos($message['noidungchat'], '<div class="product-list"') !== false) {
                                            $content = preg_replace([
                                                '/(src=")(imageproduct\/)/',
                                                '/(href=")(chi_tiet_san_pham\.php\?id=)/'
                                            ], [
                                                '$1../$2',
                                                '$1../$2'
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

                <!-- Product Suggest Modal -->
                <div class="modal fade" id="productSuggestModal" tabindex="-1" aria-labelledby="productSuggestModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="productSuggestModalLabel">Gợi ý sản phẩm</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="productSearchInput" placeholder="Tìm kiếm sản phẩm...">
                                </div>
                                <div id="productList" class="product-list-container" style="max-height: 400px; overflow-y: auto;">
                                    <!-- Product list will be populated here -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="button" class="btn btn-primary" id="sendProductButton">Gửi sản phẩm</button>
                            </div>
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
    gap: 20px;
}

.message-content .product-card {
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

.message-content .product-card a {
    text-decoration: none;
    color: inherit;
    display: block;
    width: 100%;
}

.message-content .product-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.message-content .product-name {
    margin-top: 10px;
    font-weight: bold;
    font-size: 16px;
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

.product-list-container .product-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #e0e0e0;
}

.product-list-container .product-item input[type="checkbox"] {
    margin-right: 10px;
}

.product-list-container .product-item img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    margin-right: 10px;
}

@media (max-width: 768px) {
    .message-content .product-card {
        width: 100%;
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

    // Xử lý gửi tin nhắn thường
    $('#adminChatForm').on('submit', function(e) {
        e.preventDefault();
        
        const messageInput = $('#messageInput');
        const message = messageInput.val().trim();
        const userId = $('input[name="user_id"]').val();
        
        if (!message) return;

        // Hiển thị tin nhắn ngay lập tức
        const messageContent = message.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');

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
                message: message,
                type: 'text'
            },
            success: function(response) {
                if (response && response.success) {
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
                console.log('Response:', xhr.responseText);
                alert('Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại.');
            }
        });
    });

    // Xử lý phím Enter cho tin nhắn thường
    $('#messageInput').on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            $('#adminChatForm').submit();
        }
    });

    // Tìm kiếm sản phẩm
    let searchTimeout;
    $('#productSearchInput').on('input', function() {
        clearTimeout(searchTimeout);
        const keyword = $(this).val().trim();
        
        searchTimeout = setTimeout(() => {
            if (keyword.length < 2) {
                $('#productList').html('<p>Nhập ít nhất 2 ký tự để tìm kiếm.</p>');
                return;
            }

            $.ajax({
                url: 'search_products.php',
                type: 'POST',
                dataType: 'json',
                data: { keyword: keyword },
                success: function(response) {
                    if (response.success && response.products.length > 0) {
                        let html = '';
                        response.products.forEach(product => {
                            html += `
                                <div class="product-item">
                                    <input type="checkbox" class="product-checkbox" value="${product.idsanpham}" 
                                           data-name="${product.tensanpham}" data-image="${product.image_path}">
                                    <img src="../${product.image_path}" alt="${product.tensanpham}">
                                    <span>${product.tensanpham}</span>
                                </div>
                            `;
                        });
                        $('#productList').html(html);
                    } else {
                        $('#productList').html('<p>Không tìm thấy sản phẩm.</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Lỗi tìm kiếm sản phẩm:', error);
                    $('#productList').html('<p>Có lỗi xảy ra khi tìm kiếm sản phẩm.</p>');
                }
            });
        }, 500);
    });

    // Xử lý gửi sản phẩm
    // Trong $(document).ready(function() { ... })

// Xử lý gửi sản phẩm
$('#sendProductButton').on('click', function() {
    try {
        const selectedProducts = [];
        $('.product-checkbox:checked').each(function() {
            selectedProducts.push({
                id: $(this).val(),
                name: $(this).data('name'),
                image: $(this).data('image')
            });
        });

        if (selectedProducts.length === 0) {
            alert('Vui lòng chọn ít nhất một sản phẩm.');
            return;
        }

        // Tạo cấu trúc HTML cho danh sách sản phẩm (để gửi qua AJAX)
        let productHtml = '<div class="product-list">';
        selectedProducts.forEach(product => {
            productHtml += `
                <div class="product-card">
                    <a href="chi_tiet_san_pham.php?id=${product.id}">
                        <img src="${product.image}" class="product-image">
                        <div class="product-name">${product.name}</div>
                    </a>
                    <button onclick="addToCart('${product.id}')" class="addtocart-btn">Thêm vào giỏ hàng</button>
                </div>
            `;
        });
        productHtml += '</div>';

        // Tạo HTML hiển thị ngay lập tức (thêm ../ vào src và href)
        let displayHtml = productHtml.replace(/(src=")(imageproduct\/)/g, '$1../$2')
                                    .replace(/(href=")(chi_tiet_san_pham\.php\?id=)/g, '$1../$2');

        const userId = $('input[name="user_id"]').val();

        // Hiển thị sản phẩm ngay lập tức
        $('#chatMessages').append(`
            <div class="message admin-message mb-3">
                <div class="message-content">
                    ${displayHtml}
                    <div class="message-time text-muted small">
                        ${new Date().toLocaleString('vi-VN')}
                    </div>
                </div>
            </div>
        `);
        scrollToBottom();

        // Gửi sản phẩm qua AJAX
        $.ajax({
            url: 'admin_send_message.php',
            type: 'POST',
            dataType: 'json',
            data: {
                user_id: userId,
                message: productHtml,
                type: 'product'
            },
            success: function(response) {
                if (response && response.success) {
                    const lastMessage = $('#chatMessages .message:last-child .message-time');
                    if (lastMessage.length && response.time) {
                        lastMessage.text(new Date(response.time).toLocaleString('vi-VN'));
                    }
                } else {
                    console.error('Lỗi:', response ? response.message : 'Không xác định');
                    alert('Không thể gửi sản phẩm. Vui lòng thử lại.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Lỗi AJAX:', error);
                console.log('Response:', xhr.responseText);
                alert('Có lỗi xảy ra khi gửi sản phẩm. Vui lòng thử lại.');
            }
        });

        // Đóng modal và xóa backdrop
        $('#productSuggestModal').modal('hide');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        $('#productSearchInput').val('');
        $('#productList').html('');
    } catch (error) {
        console.error('Lỗi khi gửi sản phẩm:', error);
        alert('Có lỗi xảy ra khi gửi sản phẩm. Vui lòng thử lại.');
        // Đóng modal để khôi phục giao diện
        $('#productSuggestModal').modal('hide');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
    }
});
});
</script>
</body>
</html>