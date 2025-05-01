<?php
session_start();
require_once '../connect.php';

$connect = new Connect();

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}

$page = $_GET['page'] ?? 'dashboard';
$role = $_SESSION['admin_role'];
$userId = $_SESSION['admin_id'];

// Lấy quyền truy cập nếu role = 1
$permissions = ($role == 1) ? $connect->getPermissions($userId) : [];

// Danh sách các trang yêu cầu quyền
$permissionPages = [
    'products' => 'sanpham',
    'danhmuc' => 'danhmuc',
    'loaimay' => 'loaimay',
    'loaiday' => 'loaiday',
    'nhacungcap' => 'nhacungcap',
    'donhang' => 'donhang',
    'khachhang' => 'khachhang',
    'nhanvien' => 'nhanvien',
    'danhgia' => 'danhgia',
    'tinnhan' => 'tinnhan',
    'thongke' => 'baocao',
    'khuyenmai' => 'khuyenmai',
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý cửa hàng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content flex-grow-1 p-4">
            <?php
            switch ($page) {
                case 'dashboard':
                    include '../admin/dashboard.php';
                    break;
                case 'products':
                case 'product_form':
                    if ($role == 2 || ($role == 1 && $permissions['sanpham'] == 1)) {
                        include '../admin/' . ($page == 'products' ? 'products.php' : 'product_form.php');
                    } else {
                        echo "<div class='alert alert-danger'>Bạn không có quyền truy cập!</div>";
                    }
                    break;
                case 'danhmuc':
                    if ($role == 2 || ($role == 1 && $permissions['danhmuc'] == 1)) {
                        include '../admin/danhmuc.php';
                    } else {
                        echo "<div class='alert alert-danger'>Bạn không có quyền truy cập!</div>";
                    }
                    break;
                case 'loaimay':
                    if ($role == 2 || ($role == 1 && $permissions['loaimay'] == 1)) {
                        include '../admin/loaimay.php';
                    } else {
                        echo "<div class='alert alert-danger'>Bạn không có quyền truy cập!</div>";
                    }
                    break;
                case 'loaiday':
                    if ($role == 2 || ($role == 1 && $permissions['loaiday'] == 1)) {
                        include '../admin/loaiday.php';
                    } else {
                        echo "<div class='alert alert-danger'>Bạn không có quyền truy cập!</div>";
                    }
                    break;
                case 'nhacungcap':
                    if ($role == 2 || ($role == 1 && $permissions['nhacungcap'] == 1)) {
                        include '../admin/nhacungcap.php';
                    } else {
                        echo "<div class='alert alert-danger'>Bạn không có quyền truy cập!</div>";
                    }
                    break;
                case 'nhanvien':
                    if ($role == 2 || ($role == 1 && $permissions['nhanvien'] == 1)) {
                        include '../admin/nhanvien.php';
                    } else {
                        echo "<div class='alert alert-danger'>Bạn không có quyền truy cập!</div>";
                    }
                    break;
                case 'khachhang':
                    if ($role == 2 || ($role == 1 && $permissions['khachhang'] == 1)) {
                        include '../admin/khachhang.php';
                    } else {
                        echo "<div class='alert alert-danger'>Bạn không có quyền truy cập!</div>";
                    }
                    break;
                case 'donhang':
                case 'chitietdonhang':
                    if ($role == 2 || ($role == 1 && $permissions['donhang'] == 1)) {
                        include '../admin/' . ($page == 'donhang' ? 'donhang.php' : 'chitietdonhang.php');
                    } else {
                        echo "<div class='alert alert-danger'>Bạn không có quyền truy cập!</div>";
                    }
                    break;
                case 'danhgia':
                    if ($role == 2 || ($role == 1 && $permissions['danhgia'] == 1)) {
                        include '../admin/danhgia.php';
                    } else {
                        echo "<div class='alert alert-danger'>Bạn không có quyền truy cập!</div>";
                    }
                    break;
                case 'tinnhan':
                    if ($role == 2 || ($role == 1 && $permissions['tinnhan'] == 1)) {
                        include '../admin/tinnhan.php';
                    } else {
                        echo "<div class='alert alert-danger'>Bạn không có quyền truy cập!</div>";
                    }
                    break;
                case 'khuyenmai':
                    if ($role == 2 || ($role == 1 && $permissions['khuyenmai'] == 1)) {
                        include '../admin/khuyenmai.php';
                    } else {
                        echo "<div class='alert alert-danger'>Bạn không có quyền truy cập!</div>";
                    }
                    break;
                case 'thongke':
                    if ($role == 2 || ($role == 1 && $permissions['baocao'] == 1)) {
                        include '../admin/thongke.php';
                    } else {
                        echo "<div class='alert alert-danger'>Bạn không có quyền truy cập!</div>";
                    }
                    break;
                default:
                    echo "<div class='alert alert-warning'>Trang không tồn tại!</div>";
            }
            ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Toggle sidebar
            $('.toggle-sidebar').click(function() {
                $('.sidebar').toggleClass('collapsed');
                $('.main-content').toggleClass('expanded');
            });

            // Highlight active menu item
            const currentPage = '<?php echo $page; ?>';
            $(`.nav-link[href="?page=${currentPage}"]`).addClass('active');
        });
    </script>
</body>
</html>