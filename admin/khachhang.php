<?php
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$customers = $connect->getAllCustomers($search);
?>
<!DOCTYPE html>
<head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../js/jquery3.2.1.min.js"></script>

</head>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quản lý khách hàng</h1>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="page" value="customers">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Tìm kiếm theo tên hoặc email..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên đăng nhập</th>
                            <th>Email</th>
                            <th>Ngày tạo</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Không có khách hàng nào</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td><?php echo $customer['idnguoidung']; ?></td>
                                    <td><?php echo htmlspecialchars($customer['tendangnhap']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($customer['ngaytao'])); ?></td>
                                    <td>
                                        <?php if ($customer['trangthai'] == 1): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Khóa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info view-customer" 
                                                data-id="<?php echo $customer['idnguoidung']; ?>"
                                                data-bs-toggle="modal" data-bs-target="#viewCustomerModal">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning toggle-status" 
                                                data-id="<?php echo $customer['idnguoidung']; ?>"
                                                data-status="<?php echo $customer['trangthai']; ?>">
                                            <?php if ($customer['trangthai'] == 1): ?>
                                                <i class="fas fa-lock"></i>
                                            <?php else: ?>
                                                <i class="fas fa-lock-open"></i>
                                            <?php endif; ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Customer Modal -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết khách hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="customerDetails">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.toggle-status').forEach(button => {
    button.addEventListener('click', function() {
        const customerId = this.getAttribute('data-id');
        const currentStatus = this.getAttribute('data-status');
        const newStatus = currentStatus == '1' ? '0' : '1';
        const statusText = newStatus == '1' ? 'mở khóa' : 'khóa';
        
        Swal.fire({
            title: `Xác nhận ${statusText} tài khoản?`,
            text: `Bạn có chắc muốn ${statusText} tài khoản này?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xác nhận',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../admin/khachhang_update.php',
                    type: 'POST',
                    data: {
                        action: 'toggle_status',
                        id: customerId,
                        status: newStatus
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: `Đã ${statusText} tài khoản thành công`,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: data.message || 'Có lỗi xảy ra. Vui lòng thử lại.',
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Có lỗi xảy ra. Vui lòng thử lại.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
});


document.querySelectorAll('.view-customer').forEach(button => {
    button.addEventListener('click', function() {
        const customerId = this.getAttribute('data-id');
        fetch(`chitietkhachhang.php?id=${customerId}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('customerDetails').innerHTML = html;
            });
    });
});
</script>