<?php
require_once '../connect.php';
$connect = new Connect();

if (isset($_GET['id'])) {
    $customerId = $_GET['id'];
    $customer = $connect->getCustomerDetails($customerId);
    
    if ($customer) {
?>
        <div class="row mb-3">
            <div class="col-md-6">
                <h6>Thông tin tài khoản</h6>
                <p>ID: <?php echo $customer['idnguoidung']; ?></p>
                <p>Tên đăng nhập: <?php echo htmlspecialchars($customer['tendangnhap']); ?></p>
                <p>Email: <?php echo htmlspecialchars($customer['email']); ?></p>
                <p>Ngày tạo: <?php echo date('d/m/Y H:i', strtotime($customer['ngaytao'])); ?></p>
                <p>Trạng thái: 
                    <?php if ($customer['trangthai'] == 1): ?>
                        <span class="badge bg-success">Hoạt động</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Khóa</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-md-6">
                <h6>Thống kê đơn hàng</h6>
                <p>Tổng số đơn hàng: <?php echo $customer['total_orders']; ?></p>
                <p>Đơn hàng thành công: <?php echo $customer['completed_orders']; ?></p>
                <p>Đơn hàng đã hủy: <?php echo $customer['cancelled_orders']; ?></p>
                <p>Tổng chi tiêu: <?php echo number_format($customer['total_spent'], 0, ',', '.'); ?>đ</p>
            </div>
        </div>
        
        <h6>Lịch sử đơn hàng</h6>
        <table class="table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customer['orders'] as $order): ?>
                <tr>
                    <td>                  
                            #<?php echo $order['iddonhang']; ?>
                        
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($order['ngaydat'])); ?></td>
                    <td><?php echo number_format($order['tongtien'], 0, ',', '.'); ?>đ</td>
                    <td>
                        <span class="badge <?php echo getBadgeClass($order['trangthai']); ?>">
                            <?php echo $order['trangthai']; ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
<?php
    }
}

function getBadgeClass($status) {
    switch ($status) {
        case 'Chờ xác nhận':
            return 'bg-warning';
        case 'Đã xác nhận':
            return 'bg-info';
        case 'Đang vận chuyển':
            return 'bg-primary';
        case 'Hoàn thành':
            return 'bg-success';
        case 'Đã hủy':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}
?>
<!-- Order Details Modal -->
<div class="modal fade" id="viewOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
        </div>
    </div>
</div>

