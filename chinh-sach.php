<?php
require_once 'connect.php';
session_start();
$connect = new connect();

// Kiểm tra kết nối và bảng dữ liệu
try {
    // Lấy thông tin chính sách từ database
    $policy_id = isset($_GET['id']) ? $_GET['id'] : 1; // Mặc định hiển thị chính sách đầu tiên
    $policies = $connect->getAllPolicies(); // Hàm này cần được thêm vào class connect
    $current_policy = $connect->getPolicyById($policy_id); // Hàm này cần được thêm vào class connect
    
    // Nếu không tìm thấy chính sách, hiển thị thông báo
    if (empty($policies)) {
        error_log("Không tìm thấy chính sách nào trong cơ sở dữ liệu");
    }
} catch (Exception $e) {
    error_log("Lỗi khi lấy dữ liệu chính sách: " . $e->getMessage());
}

include 'header.php';
?>
<section class="container py-1" data-src="images/anh-nen-pp.jpg" style="margin-top: 150px;">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
            <h2 class="main-title text-center mb-1 text-uppercase wow fadeInUp delay01"><?php echo htmlspecialchars($current_policy['tieude'] ?? 'Chính sách'); ?></h2>
            </div>
        </div>
    </div>
</section>

<section class="policy-content py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-12">
                <div class="policy-sidebar mb-4">
                    <h3 class="sidebar-title">Danh mục chính sách</h3>
                    <ul class="policy-menu">
                        <?php if (!empty($policies)): ?>
                            <?php foreach ($policies as $policy): ?>
                                <li class="<?php echo ($policy['id'] == $policy_id) ? 'active' : ''; ?>">
                                    <a href="chinh-sach.php?id=<?php echo $policy['id']; ?>">
                                        <?php echo htmlspecialchars($policy['tieude']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><a href="#">Chưa có chính sách</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="col-lg-9 col-md-8 col-12">
                <div class="policy-detail">
                    <h2 class="policy-title"><?php echo htmlspecialchars($current_policy['tieude'] ?? ''); ?></h2>
                    <div class="policy-content">
                        <?php echo $current_policy['noidung'] ?? 'Nội dung đang được cập nhật...'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
    body {
        background-color: #121212;
        color: #ffffff;
    }
    
    .policy-sidebar {
        background-color: #1e1e1e;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
    
    .sidebar-title {
        font-size: 18px;
        color: #ffffff;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #c8a96a;
    }
    
    .policy-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .policy-menu li {
        margin-bottom: 10px;
    }
    
    .policy-menu li a {
        color: #e0e0e0;
        text-decoration: none;
        display: block;
        padding: 10px 12px;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        font-size: 15px;
    }
    
    .policy-menu li.active a,
    .policy-menu li a:hover {
        color: #c8a96a;
        background-color: rgba(200, 169, 106, 0.1);
        border-left-color: #c8a96a;
    }
    
    .policy-detail {
        background-color: #1e1e1e;
        padding: 30px;
        border-radius: 5px;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
    
    .policy-title {
        color: #c8a96a;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #333;
        font-size: 24px;
    }
    
    .policy-content {
        color: #e0e0e0;
        line-height: 1.8;
        font-size: 15px;
    }
    
    .policy-content h3 {
        color: #c8a96a;
        margin: 25px 0 15px;
        font-size: 18px;
    }
    
    .policy-content ul {
        padding-left: 20px;
    }
    
    .policy-content p {
        margin-bottom: 15px;
    }
    
    .policy-banner {
        position: relative;
        padding: 80px 0;
        background-size: cover;
        background-position: center;
    }
    
    .policy-banner:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
    }
    
    .policy-banner .main-title {
        position: relative;
        color: #fff;
        z-index: 1;
        font-size: 30px;
        font-weight: bold;
    }
    
    @media (max-width: 767px) {
        .policy-detail {
            padding: 20px;
        }
        
        .policy-sidebar {
            margin-bottom: 30px;
        }
    }
</style>



