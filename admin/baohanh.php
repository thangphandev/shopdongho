<?php
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
$connect = new Connect();
$search = $_GET['search'] ?? '';
$warranties = $connect->getAllWarrantiesAdmin($search);
?>

<div class="container-fluid">
    <h2 class="mb-4">Quản lý chính sách bảo hành</h2>

    <!-- Search and Add button -->
    <div class="row mb-3">
        <div class="col-md-6">
            <form method="GET" class="row g-3">
                <input type="hidden" name="page" value="baohanh">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Tìm kiếm theo tên chính sách..." 
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
                <i class="fas fa-plus"></i> Thêm chính sách bảo hành
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

    <!-- Warranties Table -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên chính sách</th>
                    <th>Nội dung chính sách</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($warranties as $warranty): ?>
                <tr>
                    <td><?php echo $warranty['id_chinh_sach']; ?></td>
                    <td><?php echo htmlspecialchars($warranty['ten_chinh_sách']); ?></td>
                    <td>
                        <?php 
                            $content = htmlspecialchars($warranty['noi_dung_chinh_sach']);
                            echo (strlen($content) > 100) ? substr($content, 0, 100) . '...' : $content;
                        ?>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($warranty['ngay_tao'])); ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-warranty" 
                                data-id="<?php echo $warranty['id_chinh_sach']; ?>"
                                data-name="<?php echo htmlspecialchars($warranty['ten_chinh_sách']); ?>"
                                data-content="<?php echo htmlspecialchars($warranty['noi_dung_chinh_sach']); ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-warranty" 
                                data-id="<?php echo $warranty['id_chinh_sach']; ?>"
                                data-name="<?php echo htmlspecialchars($warranty['ten_chinh_sách']); ?>">
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm chính sách bảo hành</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="baohanh_update.php">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên chính sách</label>
                        <input type="text" class="form-control" name="ten_chinh_sach" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nội dung chính sách</label>
                        <textarea class="form-control" name="noi_dung_chinh_sach" rows="10" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa chính sách bảo hành</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="baohanh_update.php">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_chinh_sach" id="edit_id_chinh_sach">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên chính sách</label>
                        <input type="text" class="form-control" name="ten_chinh_sach" id="edit_ten_chinh_sach" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nội dung chính sách</label>
                        <textarea class="form-control" name="noi_dung_chinh_sach" id="edit_noi_dung_chinh_sach" rows="10" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
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
                Bạn có chắc chắn muốn xóa chính sách bảo hành "<span id="delete_warranty_name"></span>" không?
            </div>
            <div class="modal-footer">
                <form method="POST" action="baohanh_update.php">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id_chinh_sach" id="delete_id_chinh_sach">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Xử lý nút sửa
document.querySelectorAll('.edit-warranty').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        const content = this.getAttribute('data-content');
        
        document.getElementById('edit_id_chinh_sach').value = id;
        document.getElementById('edit_ten_chinh_sach').value = name;
        document.getElementById('edit_noi_dung_chinh_sach').value = content;
        
        new bootstrap.Modal(document.getElementById('editModal')).show();
    });
});

// Xử lý nút xóa
document.querySelectorAll('.delete-warranty').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        
        document.getElementById('delete_id_chinh_sach').value = id;
        document.getElementById('delete_warranty_name').textContent = name;
        
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});

// Thêm trình soạn thảo văn bản phong phú (nếu cần)
document.addEventListener('DOMContentLoaded', function() {
    if (typeof ClassicEditor !== 'undefined') {
        ClassicEditor
            .create(document.querySelector('textarea[name="noi_dung_chinh_sach"]'))
            .catch(error => {
                console.error(error);
            });
            
        ClassicEditor
            .create(document.querySelector('#edit_noi_dung_chinh_sach'))
            .catch(error => {
                console.error(error);
            });
    }
});
</script>