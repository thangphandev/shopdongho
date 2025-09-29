<?php
$connect = new Connect();
$search = $_GET['search'] ?? '';
$promotions = $connect->getAllPromotionsadmin($search);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add']) || isset($_POST['update'])) {
        // Kiểm tra giá giảm không được âm
        $gia_giam = filter_var($_POST['gia_giam'], FILTER_VALIDATE_FLOAT);
        if ($gia_giam === false || $gia_giam < 0) {
            $_SESSION['error_message'] = "Giá giảm không hợp lệ. Vui lòng nhập số dương.";
        } else {
            $data = [
                'tenkhuyenmai' => $_POST['tenkhuyenmai'],
                'gia_giam' => $gia_giam,
                'ngaybatdau' => $_POST['ngaybatdau'],
                'ngayketthuc' => $_POST['ngayketthuc']
            ];
            
            if (isset($_POST['add'])) {
                // Kiểm tra tên khuyến mãi đã tồn tại chưa
                if ($connect->isPromotionNameExists($data['tenkhuyenmai'])) {
                    $_SESSION['error_message'] = "Tên khuyến mãi đã tồn tại!";
                } else {
                    // Thêm khuyến mãi mới
                    $result = $connect->addPromotion($data);
                    if ($result) {
                        $_SESSION['success_message'] = 'Thêm khuyến mãi thành công!';
                    } else {
                        $_SESSION['error_message'] = 'Thêm khuyến mãi thất bại!';
                    }
                }
            } else if (isset($_POST['update'])) {
                // Cập nhật khuyến mãi
                $data['idkhuyenmai'] = $_POST['idkhuyenmai'];
                
                // Kiểm tra tên khuyến mãi đã tồn tại chưa (trừ chính nó)
                if ($connect->isPromotionNameExistsExcept($data['tenkhuyenmai'], $data['idkhuyenmai'])) {
                    $_SESSION['error_message'] = "Tên khuyến mãi đã tồn tại!";
                } else {
                    $result = $connect->updatePromotion($data);
                    if ($result) {
                        $_SESSION['success_message'] = 'Cập nhật khuyến mãi thành công!';
                    } else {
                        $_SESSION['error_message'] = 'Cập nhật khuyến mãi thất bại!';
                    }
                }
            }
        }
    } else if (isset($_POST['delete'])) {
        // Xóa khuyến mãi
        $id = $_POST['idkhuyenmai'];
        $result = $connect->deletePromotion($id);
        if ($result) {
            $_SESSION['success_message'] = 'Xóa khuyến mãi thành công!';
        } else {
            $_SESSION['error_message'] = 'Xóa khuyến mãi thất bại!';
        }
    }
    
    // Refresh danh sách sau khi thực hiện thao tác
    $promotions = $connect->getAllPromotionsadmin($search);
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Quản lý khuyến mãi</h2>

    <!-- Search and Add button -->
    <div class="row mb-3">
        <div class="col-md-6">
            <form method="GET" class="row g-3">
                <input type="hidden" name="page" value="khuyenmai">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Tìm kiếm theo tên..." 
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                Thêm khuyến mãi
            </button>
        </div>
    </div>

    <!-- Hiển thị thông báo -->
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

    <!-- Promotions Table -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên khuyến mãi</th>
                    <th>Giá giảm</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promotions as $promotion): ?>
                <tr>
                    <td><?php echo $promotion['idkhuyenmai']; ?></td>
                    <td><?php echo htmlspecialchars($promotion['tenkhuyenmai']); ?></td>
                    <td><?php echo number_format($promotion['gia_giam'], 0, ',', '.'); ?> VNĐ</td>
                    <td><?php echo date('d/m/Y H:i', strtotime($promotion['ngaybatdau'])); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($promotion['ngayketthuc'])); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($promotion['ngaytao'])); ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-promotion" 
                                data-id="<?php echo $promotion['idkhuyenmai']; ?>"
                                data-name="<?php echo htmlspecialchars($promotion['tenkhuyenmai']); ?>"
                                data-discount="<?php echo $promotion['gia_giam']; ?>"
                                data-start="<?php echo date('Y-m-d\TH:i', strtotime($promotion['ngaybatdau'])); ?>"
                                data-end="<?php echo date('Y-m-d\TH:i', strtotime($promotion['ngayketthuc'])); ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-promotion" 
                                data-id="<?php echo $promotion['idkhuyenmai']; ?>"
                                data-name="<?php echo htmlspecialchars($promotion['tenkhuyenmai']); ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm khuyến mãi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên khuyến mãi</label>
                        <input type="text" class="form-control" name="tenkhuyenmai" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giá giảm (VNĐ)</label>
                        <input type="number" class="form-control" name="gia_giam" min="0" required oninput="validatePositiveNumber(this)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ngày bắt đầu</label>
                        <input type="datetime-local" class="form-control" name="ngaybatdau" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ngày kết thúc</label>
                        <input type="datetime-local" class="form-control" name="ngayketthuc" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" name="add" class="btn btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa khuyến mãi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="idkhuyenmai" id="edit_idkhuyenmai">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên khuyến mãi</label>
                        <input type="text" class="form-control" name="tenkhuyenmai" id="edit_tenkhuyenmai" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giá giảm (VNĐ)</label>
                        <input type="number" class="form-control" name="gia_giam" id="edit_gia_giam" min="0" required oninput="validatePositiveNumber(this)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ngày bắt đầu</label>
                        <input type="datetime-local" class="form-control" name="ngaybatdau" id="edit_ngaybatdau" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ngày kết thúc</label>
                        <input type="datetime-local" class="form-control" name="ngayketthuc" id="edit_ngayketthuc" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" name="update" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa khuyến mãi "<span id="delete_promotion_name"></span>" không?
            </div>
            <div class="modal-footer">
                <form method="POST">
                    <input type="hidden" name="idkhuyenmai" id="delete_idkhuyenmai">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" name="delete" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Xử lý nút sửa
document.querySelectorAll('.edit-promotion').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        const discount = this.getAttribute('data-discount');
        const start = this.getAttribute('data-start');
        const end = this.getAttribute('data-end');
        
        document.getElementById('edit_idkhuyenmai').value = id;
        document.getElementById('edit_tenkhuyenmai').value = name;
        document.getElementById('edit_gia_giam').value = discount;
        document.getElementById('edit_ngaybatdau').value = start;
        document.getElementById('edit_ngayketthuc').value = end;
        
        new bootstrap.Modal(document.getElementById('editModal')).show();
    });
});

// Xử lý nút xóa
document.querySelectorAll('.delete-promotion').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        
        document.getElementById('delete_idkhuyenmai').value = id;
        document.getElementById('delete_promotion_name').textContent = name;
        
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});

// Hàm kiểm tra số dương
function validatePositiveNumber(input) {
    // Loại bỏ dấu trừ nếu có
    if (input.value.includes('-')) {
        input.value = input.value.replace(/-/g, '');
    }
    
    // Đảm bảo giá trị không âm
    if (parseFloat(input.value) < 0) {
        input.value = 0;
    }
}

// Áp dụng kiểm tra cho tất cả input số
document.addEventListener('DOMContentLoaded', function() {
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        // Ngăn chặn việc nhập dấu trừ
        input.addEventListener('keydown', function(e) {
            if (e.key === '-' || e.keyCode === 189) {
                e.preventDefault();
            }
        });
        
        // Ngăn chặn paste dấu trừ
        input.addEventListener('paste', function(e) {
            const clipboardData = e.clipboardData || window.clipboardData;
            const pastedText = clipboardData.getData('text');
            if (pastedText.includes('-')) {
                e.preventDefault();
            }
        });
    });
});
</script>