<?php
// Prevent direct access
if (!defined('ADMIN_PAGE')) {
    define('ADMIN_PAGE', true);
}

// Process actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    // Delete review
    if ($action == 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $connect->deleteReview($id);
        echo "<div class='alert alert-success'>Đã xóa đánh giá thành công!</div>";
    }
    
    // Change review status
    if ($action == 'toggle_status' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $connect->toggleReviewStatus($id);
        echo "<div class='alert alert-success'>Đã cập nhật trạng thái đánh giá!</div>";
    }
}

// Get all reviews
$reviews = $connect->getAllReviews();
?>

<!-- Add DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý đánh giá</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách đánh giá</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Sản phẩm</th>
                            <th>Người dùng</th>
                            <th>Số sao</th>
                            <th>Nội dung</th>
                            <th>Ngày tạo</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?php echo $review['iddanhgia']; ?></td>
                            <td>
                                <a href="../chi_tiet_san_pham.php?id=<?php echo $review['idsanpham']; ?>" target="_blank">
                                    <?php echo $review['tensanpham']; ?>
                                </a>
                            </td>
                            <td><?php echo $review['tendangnhap']; ?></td>
                            <td>
                                <?php 
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $review['sosao']) {
                                        echo '<i class="fas fa-star text-warning"></i>';
                                    } else {
                                        echo '<i class="far fa-star text-warning"></i>';
                                    }
                                }
                                ?>
                            </td>
                            <td><?php echo nl2br(htmlspecialchars($review['noidung'])); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($review['ngaytao'])); ?></td>
                            <td>
                                <?php if ($review['trangthai'] == 1): ?>
                                    <span class="badge bg-success">Hiển thị</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Ẩn</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?page=danhgia&action=toggle_status&id=<?php echo $review['iddanhgia']; ?>" class="btn btn-sm btn-info">
                                    <?php echo $review['trangthai'] == 1 ? 'Ẩn' : 'Hiển thị'; ?>
                                </a>
                                <a href="?page=danhgia&action=delete&id=<?php echo $review['iddanhgia']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                                    Xóa
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if jQuery is loaded
    if (typeof jQuery !== 'undefined') {
        // Initialize DataTable if DataTables plugin is available
        if (typeof jQuery.fn.DataTable !== 'undefined') {
            jQuery('#dataTable').DataTable();
        } else {
            console.error('DataTables plugin is not loaded');
        }
    } else {
        console.error('jQuery is not loaded');
    }
});
</script>