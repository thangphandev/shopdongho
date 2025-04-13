<?php
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
$search = isset($_GET['search']) ? $_GET['search'] : '';

$strapTypes = $connect->getAllStrapTypes($search);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quản lý loại dây</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStrapTypeModal">
            <i class="fas fa-plus"></i> Thêm loại dây mới
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="page" value="loaiday">
                <div class="col-md-4">
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
                            <th>Tên loại dây</th>
                            <th>Mô tả</th>
                            <th>Ngày tạo</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($strapTypes as $type): ?>
                        <tr>
                            <td><?php echo $type['id_loai_day']; ?></td>
                            <td><?php echo htmlspecialchars($type['ten_loai_day']); ?></td>
                            <td><?php echo htmlspecialchars($type['mo_ta_loai_day']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($type['ngay_tao'])); ?></td>
                            <td>
                                <span class="badge <?php echo $type['trangthai'] ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo $type['trangthai'] ? 'Hoạt động' : 'Không hoạt động'; ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary edit-strap-type" 
                                        data-id="<?php echo $type['id_loai_day']; ?>"
                                        data-name="<?php echo htmlspecialchars($type['ten_loai_day']); ?>"
                                        data-description="<?php echo htmlspecialchars($type['mo_ta_loai_day']); ?>"
                                        data-status="<?php echo $type['trangthai']; ?>"
                                        data-bs-toggle="modal" data-bs-target="#editStrapTypeModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-strap-type" 
                                        data-id="<?php echo $type['id_loai_day']; ?>"
                                        data-name="<?php echo htmlspecialchars($type['ten_loai_day']); ?>">
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

<!-- Add Strap Type Modal -->
<div class="modal fade" id="addStrapTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm loại dây mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addStrapTypeForm" method="POST" action="loaiday_update.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="ten_loai_day" class="form-label">Tên loại dây</label>
                        <input type="text" class="form-control" id="ten_loai_day" name="ten_loai_day" required>
                    </div>
                    <div class="mb-3">
                        <label for="mo_ta_loai_day" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="mo_ta_loai_day" name="mo_ta_loai_day" rows="3"></textarea>
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
                    <button type="submit" class="btn btn-primary">Thêm loại dây</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Strap Type Modal -->
<div class="modal fade" id="editStrapTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa loại dây</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStrapTypeForm" method="POST" action="loaiday_update.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id_loai_day" id="edit_id_loai_day">
                    <div class="mb-3">
                        <label for="edit_ten_loai_day" class="form-label">Tên loại dây</label>
                        <input type="text" class="form-control" id="edit_ten_loai_day" name="ten_loai_day" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_mo_ta_loai_day" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="edit_mo_ta_loai_day" name="mo_ta_loai_day" rows="3"></textarea>
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
document.querySelectorAll('.edit-strap-type').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        const description = this.getAttribute('data-description');
        const status = this.getAttribute('data-status');
        
        document.getElementById('edit_id_loai_day').value = id;
        document.getElementById('edit_ten_loai_day').value = name;
        document.getElementById('edit_mo_ta_loai_day').value = description || '';
        document.getElementById('edit_trangthai').checked = status === '1';
    });
});

document.querySelectorAll('.delete-strap-type').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        
        if (confirm(`Bạn có chắc chắn muốn xóa loại dây "${name}"?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'loaiday_update.php';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'delete';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id_loai_day';
            idInput.value = id;
            
            form.appendChild(actionInput);
            form.appendChild(idInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
});
</script>