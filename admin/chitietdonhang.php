<?php
require_once '../connect.php';
$connect = new Connect();

if (isset($_GET['id'])) {
    $orderId = $_GET['id'];
    $order = $connect->getOrderByIdAdmin($orderId);
    $orderDetails = $connect->getOrderDetailsAdmin($orderId);
    
    if ($order) {
?>
        <div class="row mb-3">
            <div class="col-md-6">
                <h6>Thông tin đơn hàng</h6>
                <p>Mã đơn hàng: #<?php echo $order['iddonhang']; ?></p>
                <p>Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order['ngaydat'])); ?></p>
                <p>Trạng thái: <?php echo $order['trangthai']; ?></p>
                <p>Phương thức thanh toán: <?php echo $order['phuongthuctt']; ?></p>
            </div>
            <div class="col-md-6">
                <h6>Thông tin người nhận</h6>
                <p>Tên: <?php echo htmlspecialchars($order['tennguoidat']); ?></p>
                <p>SĐT: <?php echo htmlspecialchars($order['sdt']); ?></p>
                <p>Địa chỉ: <?php echo htmlspecialchars($order['diachigiao']); ?></p>
            </div>
        </div>
        
        <h6>Chi tiết đơn hàng</h6>
        <table class="table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderDetails as $item): ?>
                <tr>
                <td>
                        <a href="admin.php?page=product_form&id=<?php echo $item['idsanpham']; ?>">
                            <?php echo htmlspecialchars($item['tensanpham']); ?>
                        </a>
                    </td>
                    <td><?php echo number_format($item['giaban'], 0, ',', '.'); ?>đ</td>
                    <td><?php echo $item['soluong']; ?></td>
                    <td><?php echo number_format($item['giaban'] * $item['soluong'], 0, ',', '.'); ?>đ</td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-end"><strong>Tổng tiền:</strong></td>
                    <td><strong><?php echo number_format($order['tongtien'], 0, ',', '.'); ?>đ</strong></td>
                </tr>
            </tbody>
        </table>
<?php
    }
}
?>