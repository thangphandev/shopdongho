<?php
require_once 'connect.php';
session_start();
$connect = new connect();

// Kiểm tra kết nối và bảng dữ liệu
try {
    // Lấy thông tin giới thiệu từ database
    $section_id = isset($_GET['id']) ? $_GET['id'] : 1; // Mặc định hiển thị mục có id = 1
    $sections = $connect->getAllIntroSections(); // Lấy tất cả các mục
    $current_section = $connect->getIntroSectionByCode($section_id); // Lấy mục hiện tại theo ID
    
    // Nếu không tìm thấy mục, hiển thị mục đầu tiên
    if (empty($current_section) && !empty($sections)) {
        $current_section = $sections[0];
        $section_id = $sections[0]['id'];
    }
    
    // Nếu không tìm thấy mục nào, hiển thị thông báo
    if (empty($sections)) {
        error_log("Không tìm thấy mục giới thiệu nào trong cơ sở dữ liệu");
    }
} catch (Exception $e) {
    error_log("Lỗi khi lấy dữ liệu giới thiệu: " . $e->getMessage());
}

include 'header.php';
?>
<!DOCTYPE html>
<html itemscope="" itemtype="http://schema.org/WebPage" lang="vi">
<script src="js/jquery3.2.1.min.js" defer=""></script>
    <script src="js/bootstrap.min.js" defer=""></script>
    <script src="js/jquery.fancybox.min.js" defer=""></script>
    <script src="js/toastr.min.js" defer=""></script>
    <script src="js/social.js" defer=""></script>
    <script src="js/lazyload.min.js" defer=""></script>
    <script src="js/wow.js" defer=""></script>
    <script src="js/tiny-slider.js" defer=""></script>
    <script src="js/script.js" defer=""></script>
    <script src="js/cart.js" defer=""></script>
<body class="scrollstyle1">
<section class="container py-1" data-src="images/anh-nen-pp.jpg" style="margin-top: 150px;">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
            <h2 class="main-title text-center mb-1 text-uppercase wow fadeInUp delay01"><?php echo htmlspecialchars($current_section['tieude'] ?? 'Giới thiệu'); ?></h2>
            </div>
        </div>
    </div>
</section>

<section class="intro-content py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-12">
                <div class="intro-sidebar mb-4">
                    <h3 class="sidebar-title">Danh mục</h3>
                    <ul class="intro-menu">
                        <?php if (!empty($sections)): ?>
                            <?php foreach ($sections as $section): ?>
                                <li class="<?php echo ($section['id'] == $section_id) ? 'active' : ''; ?>">
                                    <a href="gioi-thieu.php?id=<?php echo $section['id']; ?>">
                                        <?php echo htmlspecialchars($section['tieude']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><a href="#">Chưa có mục giới thiệu</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="col-lg-9 col-md-8 col-12">
                <div class="intro-detail">
                    <h2 class="intro-title"><?php echo htmlspecialchars($current_section['tieude'] ?? ''); ?></h2>
                    <div class="intro-content">
                        <?php echo $current_section['noidung'] ?? 'Nội dung đang được cập nhật...'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
    // Include footer
    include 'footer.php';
    ?>
<style>
    body {
        background-color: #121212;
        color: #ffffff;
    }
    
    .intro-sidebar {
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
    
    .intro-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .intro-menu li {
        margin-bottom: 10px;
    }
    
    .intro-menu li a {
        color: #e0e0e0;
        text-decoration: none;
        display: block;
        padding: 10px 12px;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        font-size: 15px;
    }
    
    .intro-menu li.active a,
    .intro-menu li a:hover {
        color: #c8a96a;
        background-color: rgba(200, 169, 106, 0.1);
        border-left-color: #c8a96a;
    }
    
    .intro-detail {
        background-color: #1e1e1e;
        padding: 30px;
        border-radius: 5px;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
    
    .intro-title {
        color: #c8a96a;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #333;
        font-size: 24px;
    }
    
    .intro-content {
        color: #e0e0e0;
        line-height: 1.8;
        font-size: 15px;
    }
    
    .intro-content h3 {
        color: #c8a96a;
        margin: 25px 0 15px;
        font-size: 18px;
    }
    
    .intro-content h4 {
        color: #e0e0e0;
        margin: 20px 0 10px;
        font-size: 16px;
    }
    
    .intro-content ul {
        padding-left: 20px;
    }
    
    .intro-content p {
        margin-bottom: 15px;
    }
    
    .news-item {
        border-bottom: 1px solid #333;
        padding-bottom: 15px;
    }
    
    .news-item h4 {
        color: #c8a96a;
    }
    
    .text-muted {
        color: #999;
    }
    
    .faq-item {
        margin-bottom: 20px;
    }
    
    .faq-item h4 {
        color: #c8a96a;
    }
    
    @media (max-width: 767px) {
        .intro-detail {
            padding: 20px;
        }
        
        .intro-sidebar {
            margin-bottom: 30px;
        }
    }
</style>

