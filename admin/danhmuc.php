<?php
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Get all categories
$categories = $connect->getAllCategories();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quản lý danh mục</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus"></i> Thêm danh mục mới
        </button>
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
                            <th>Tên danh mục</th>
                            <th>Ngày tạo</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo $category['iddanhmuc']; ?></td>
                            <td><?php echo htmlspecialchars($category['tendanhmuc']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($category['ngaytao'])); ?></td>
                            <td>
                                <span class="badge <?php echo $category['trangthai'] ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo $category['trangthai'] ? 'Hoạt động' : 'Không hoạt động'; ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary edit-category" 
                                        data-id="<?php echo $category['iddanhmuc']; ?>"
                                        data-name="<?php echo htmlspecialchars($category['tendanhmuc']); ?>"
                                        data-status="<?php echo $category['trangthai']; ?>"
                                        data-bs-toggle="modal" data-bs-target="#editCategoryModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-category" 
                                        data-id="<?php echo $category['iddanhmuc']; ?>"
                                        data-name="<?php echo htmlspecialchars($category['tendanhmuc']); ?>">
                                    <i class="fas fa-trash"></i>
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

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm danh mục mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCategoryForm" method="POST" action="danhmuc_update.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="tendanhmuc" class="form-label">Tên danh mục</label>
                        <input type="text" class="form-control" id="tendanhmuc" name="tendanhmuc" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="trangthai" name="trangthai" checked>
                            <label class="form-check-label" for="trangthai">Hoạt động</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm danh mục</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa danh mục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm" method="POST" action="danhmuc_update.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="iddanhmuc" id="edit_iddanhmuc">
                    <div class="mb-3">
                        <label for="edit_tendanhmuc" class="form-label">Tên danh mục</label>
                        <input type="text" class="form-control" id="edit_tendanhmuc" name="tendanhmuc" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_trangthai" name="trangthai">
                            <label class="form-check-label" for="edit_trangthai">Hoạt động</label>
                        </div>
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

<script>
// Edit category
document.querySelectorAll('.edit-category').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        const status = this.getAttribute('data-status');
        
        document.getElementById('edit_iddanhmuc').value = id;
        document.getElementById('edit_tendanhmuc').value = name;
        document.getElementById('edit_trangthai').checked = status === '1';
    });
});

// Delete category
document.querySelectorAll('.delete-category').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        
        if (confirm(`Bạn có chắc chắn muốn xóa danh mục "${name}"?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'danhmuc_update.php';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'delete';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'iddanhmuc';
            idInput.value = id;
            
            form.appendChild(actionInput);
            form.appendChild(idInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
});
</script>