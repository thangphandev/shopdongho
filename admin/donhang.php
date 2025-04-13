<?php
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$orders = $connect->getAllOrdersAdmin($search, $status);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quản lý đơn hàng</h1>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="page" value="donhang">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Tìm kiếm theo tên người đặt hoặc SĐT..." 
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="Chờ xác nhận" <?php echo $status == 'Chờ xác nhận' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                        <option value="Đã xác nhận" <?php echo $status == 'Đã xác nhận' ? 'selected' : ''; ?>>Đã xác nhận</option>
                        <option value="Đang vận chuyển" <?php echo $status == 'Đang vận chuyển' ? 'selected' : ''; ?>>Đang vận chuyển</option>
                        <option value="Hoàn thành" <?php echo $status == 'Hoàn thành' ? 'selected' : ''; ?>>Hoàn thành</option>
                        <option value="Đã hủy" <?php echo $status == 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
                echo $_SESSION['success_message']; 
                unset($_SESSION['success_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
                echo $_SESSION['error_message']; 
                unset($_SESSION['error_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người đặt</th>
                            <th>SĐT</th>
                            <th>Tổng tiền</th>
                            <th>Ngày đặt</th>
                            <th>Trạng thái</th>
                            <th>PT thanh toán</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['iddonhang']; ?></td>
                            <td><?php echo htmlspecialchars($order['tennguoidat']); ?></td>
                            <td><?php echo htmlspecialchars($order['sdt']); ?></td>
                            <td><?php echo number_format($order['tongtien'], 0, ',', '.'); ?>đ</td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['ngaydat'])); ?></td>
                            <td>
                                <span class="badge <?php echo getBadgeClass($order['trangthai']); ?>">
                                    <?php echo $order['trangthai']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($order['phuongthuctt']); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info view-order" 
                                        data-id="<?php echo $order['iddonhang']; ?>"
                                        data-bs-toggle="modal" data-bs-target="#viewOrderModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-primary edit-order-status" 
                                        data-id="<?php echo $order['iddonhang']; ?>"
                                        data-status="<?php echo htmlspecialchars($order['trangthai']); ?>"
                                        data-bs-toggle="modal" data-bs-target="#editOrderStatusModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Order Modal -->
<div class="modal fade" id="viewOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetails">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Edit Order Status Modal -->
<div class="modal fade" id="editOrderStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cập nhật trạng thái đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editOrderStatusForm" method="POST" action="donhang_update.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="iddonhang" id="edit_iddonhang">
                    <div class="mb-3">
                        <label for="edit_trangthai" class="form-label">Trạng thái</label>
                        <select class="form-select" id="edit_trangthai" name="trangthai" required>
                            <option value="Chờ xác nhận">Chờ xác nhận</option>
                            <option value="Đã xác nhận">Đã xác nhận</option>
                            <option value="Đang vận chuyển">Đang vận chuyển</option>
                            <option value="Hoàn thành">Hoàn thành</option>
                            <option value="Đã hủy">Đã hủy</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
function getBadgeClass($status) {
    switch ($status) {
        case 'Chờ xác nhận':
            return 'bg-warning'; // #ffc107
        case 'Đã xác nhận':
            return 'bg-info'; // #17a2b8
        case 'Đang vận chuyển':
            return 'bg-primary'; // #007bff
        case 'Hoàn thành':
            return 'bg-success'; // #28a745
        case 'Đã hủy':
            return 'bg-danger'; // #dc3545
        default:
            return 'bg-secondary';
    }
}
?>

<script>
document.querySelectorAll('.view-order').forEach(button => {
    button.addEventListener('click', function() {
        const orderId = this.getAttribute('data-id');
        fetch(`chitietdonhang.php?id=${orderId}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('orderDetails').innerHTML = html;
            });
    });
});

document.querySelectorAll('.edit-order-status').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const status = this.getAttribute('data-status');
        
        document.getElementById('edit_iddonhang').value = id;
        document.getElementById('edit_trangthai').value = status;
    });
});
</script>