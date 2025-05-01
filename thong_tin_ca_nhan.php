<?php
require_once 'connect.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$connect = new Connect();
$userId = $_SESSION['user_id'];
$user = $connect->getUserById($userId);
$totalSpent = $connect->getTotalSpentByUser($userId);

// Handle password change
$passwordMessage = '';
$passwordError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validate passwords
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $passwordError = "Vui lòng điền đầy đủ thông tin.";
    } elseif ($newPassword !== $confirmPassword) {
        $passwordError = "Mật khẩu mới không khớp.";
    } elseif (strlen($newPassword) < 6) {
        $passwordError = "Mật khẩu mới phải có ít nhất 6 ký tự.";
    } else {
        // Verify current password
        if ($connect->verifyUserPassword($userId, $currentPassword)) {
            // Update password
            if ($connect->updateUserPassword($userId, $newPassword)) {
                $passwordMessage = "Mật khẩu đã được cập nhật thành công.";
            } else {
                $passwordError = "Có lỗi xảy ra khi cập nhật mật khẩu.";
            }
        } else {
            $passwordError = "Mật khẩu hiện tại không đúng.";
        }
    }
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

<main style="margin-top: 150px; min-height: 70vh;">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Tài khoản của tôi</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link active" href="thong_tin_ca_nhan.php">
                                    <i class="fa fa-user-circle"></i> Thông tin cá nhân
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="don_hang.php">
                                    <i class="fa fa-box"></i> Thông tin đơn hàng
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">
                                    <i class="fa fa-sign-out"></i> Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin cá nhân</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Tên đăng nhập:</div>
                            <div class="col-md-8"><?php echo htmlspecialchars($user['tendangnhap']); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Email:</div>
                            <div class="col-md-8"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Ngày tạo tài khoản:</div>
                            <div class="col-md-8"><?php echo date('d/m/Y H:i', strtotime($user['ngaytao'])); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Tổng số tiền đã mua:</div>
                            <div class="col-md-8"><?php echo number_format($totalSpent, 0, ',', '.'); ?> VNĐ</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Trạng thái:</div>
                            <div class="col-md-8">
                                <?php if ($user['trangthai'] == 1): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Khóa</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Đổi mật khẩu</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($passwordMessage)): ?>
                            <div class="alert alert-success"><?php echo $passwordMessage; ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($passwordError)): ?>
                            <div class="alert alert-danger"><?php echo $passwordError; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Mật khẩu mới</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự.</div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-primary">Đổi mật khẩu</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.nav-link {
    color: #f8f9fa;
    transition: all 0.3s;
}

.nav-link:hover, .nav-link.active {
    color: #ffc107; /* Gold color for hover/active state */
    background-color: #343a40;
}

.card {
    border-radius: 10px;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.2);
    margin-bottom: 20px;
    background-color: #212529; /* Dark background */
    color: #f8f9fa; /* Light text */
    border: 1px solid #343a40;
}

.card-header {
    background-color: #343a40; /* Darker header */
    border-bottom: 1px solid #495057;
}

.badge {
    font-size: 0.875rem;
}

.form-control {
    background-color: #343a40;
    border: 1px solid #495057;
    color: #f8f9fa;
}

.form-control:focus {
    background-color: #343a40;
    color: #f8f9fa;
    border-color: #ffc107;
    box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
}

.form-text {
    color: #adb5bd;
}

.btn-primary {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #212529;
}

.btn-primary:hover {
    background-color: #ffca2c;
    border-color: #ffc720;
    color: #212529;
}

body{
    background: url("images/anh-nen-pp.jpg");

 
 
}

.container {
    background-color:rgba(52, 58, 64, 0);
}

.fw-bold {
    color: #ffc107; /* Gold color for labels */
}
</style>

<?php include 'footer.php'; ?>