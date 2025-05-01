<?php
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$staffs = $connect->getAllStaff($search);
?>

<!DOCTYPE html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quản lý nhân viên</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
            <i class="fas fa-plus"></i> Thêm nhân viên
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="page" value="nhanvien">
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
                        <?php if (empty($staffs)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Không có nhân viên nào</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($staffs as $staff): ?>
                                <tr>
                                    <td><?php echo $staff['idnguoidung']; ?></td>
                                    <td><?php echo htmlspecialchars($staff['tendangnhap']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($staff['ngaytao'])); ?></td>
                                    <td>
                                        <?php if ($staff['trangthai'] == 1): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Khóa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info edit-staff" 
                                                data-id="<?php echo $staff['idnguoidung']; ?>"
                                                data-bs-toggle="modal" data-bs-target="#editStaffModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning toggle-status" 
                                                data-id="<?php echo $staff['idnguoidung']; ?>"
                                                data-status="<?php echo $staff['trangthai']; ?>">
                                            <?php if ($staff['trangthai'] == 1): ?>
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

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm nhân viên mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addStaffForm">
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" name="tendangnhap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" name="matkhau" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="saveNewStaff">Lưu</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa nhân viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editStaffForm">
                    <input type="hidden" name="id">
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" name="tendangnhap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
                        <input type="password" class="form-control" name="matkhau">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quyền truy cập</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[sanpham]" id="perm_sanpham">
                                    <label class="form-check-label" for="perm_sanpham">Sản phẩm</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[danhmuc]" id="perm_danhmuc">
                                    <label class="form-check-label" for="perm_danhmuc">Danh mục</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[loaimay]" id="perm_loaimay">
                                    <label class="form-check-label" for="perm_loaimay">Loại máy</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[loaiday]" id="perm_loaiday">
                                    <label class="form-check-label" for="perm_loaiday">Loại dây</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[nhacungcap]" id="perm_nhacungcap">
                                    <label class="form-check-label" for="perm_nhacungcap">Nhà cung cấp</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[donhang]" id="perm_donhang">
                                    <label class="form-check-label" for="perm_donhang">Đơn hàng</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[khachhang]" id="perm_khachhang">
                                    <label class="form-check-label" for="perm_khachhang">Khách hàng</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[nhanvien]" id="perm_nhanvien">
                                    <label class="form-check-label" for="perm_nhanvien">Nhân viên</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[danhgia]" id="perm_danhgia">
                                    <label class="form-check-label" for="perm_danhgia">Đánh giá</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[khuyenmai]" id="perm_khuyenmai">
                                    <label class="form-check-label" for="perm_khuyennmai">Khuyến mãi</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[tinnhan]" id="perm_tinnhan">
                                    <label class="form-check-label" for="perm_tinnhan">Tin nhắn</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[baocao]" id="perm_baocao">
                                    <label class="form-check-label" for="perm_baocao">Báo cáo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="saveStaffChanges">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>

<script>
// Add new staff
$('#saveNewStaff').click(function() {
    const formData = new FormData($('#addStaffForm')[0]);
    formData.append('action', 'add');
    
    $.ajax({
        url: 'nhanvien_update.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Thành công!',
                    text: 'Đã thêm nhân viên mới',
                    icon: 'success'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Lỗi!',
                    text: response.message || 'Có lỗi xảy ra',
                    icon: 'error'
                });
            }
        }
    });
});

// Load staff data for editing
$('.edit-staff').click(function() {
    const staffId = $(this).data('id');
    $.ajax({
        url: 'nhanvien_update.php',
        type: 'POST',
        data: {
            action: 'get',
            id: staffId
        },
        success: function(response) {
            if (response.success) {
                const form = $('#editStaffForm');
                form.find('[name="id"]').val(response.data.idnguoidung);
                form.find('[name="tendangnhap"]').val(response.data.tendangnhap);
                form.find('[name="email"]').val(response.data.email);

                // Load permissions
                $.ajax({
                    url: 'nhanvien_update.php',
                    type: 'POST',
                    data: {
                        action: 'get_permissions',
                        id: staffId
                    },
                    success: function(permResponse) {
                        if (permResponse.success) {
                            const perms = permResponse.data;
                            form.find('[name="permissions[sanpham]"]').prop('checked', perms.sanpham == 1);
                            form.find('[name="permissions[danhmuc]"]').prop('checked', perms.danhmuc == 1);
                            form.find('[name="permissions[loaimay]"]').prop('checked', perms.loaimay == 1);
                            form.find('[name="permissions[loaiday]"]').prop('checked', perms.loaiday == 1);
                            form.find('[name="permissions[nhacungcap]"]').prop('checked', perms.nhacungcap == 1);
                            form.find('[name="permissions[donhang]"]').prop('checked', perms.donhang == 1);
                            form.find('[name="permissions[khachhang]"]').prop('checked', perms.khachhang == 1);
                            form.find('[name="permissions[nhanvien]"]').prop('checked', perms.nhanvien == 1);
                            form.find('[name="permissions[danhgia]"]').prop('checked', perms.danhgia == 1);
                            form.find('[name="permissions[tinnhan]"]').prop('checked', perms.tinnhan == 1);
                            form.find('[name="permissions[tinnhan]"]').prop('checked', perms.khuyenmai == 1);
                            form.find('[name="permissions[baocao]"]').prop('checked', perms.baocao == 1);
                        }
                    }
                });
            }
        }
    });
});

// Save staff changes
$('#saveStaffChanges').click(function() {
    const formData = new FormData($('#editStaffForm')[0]);
    formData.append('action', 'edit');
    
    $.ajax({
        url: 'nhanvien_update.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Thành công!',
                    text: 'Đã cập nhật thông tin và quyền nhân viên',
                    icon: 'success'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Lỗi!',
                    text: response.message || 'Có lỗi xảy ra',
                    icon: 'error'
                });
            }
        }
    });
});

// Toggle staff status
$('.toggle-status').click(function() {
    const staffId = $(this).data('id');
    const currentStatus = $(this).data('status');
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
                url: 'nhanvien_update.php',
                type: 'POST',
                data: {
                    action: 'toggle_status',
                    id: staffId,
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
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
                            text: response.message || 'Có lỗi xảy ra',
                            icon: 'error'
                        });
                    }
                }
            });
        }
    });
});
</script>