<?php
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
$search = isset($_GET['search']) ? $_GET['search'] : '';

$suppliers = $connect->getAllSuppliers($search);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quản lý nhà cung cấp</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
            <i class="fas fa-plus"></i> Thêm nhà cung cấp mới
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="page" value="nhacungcap">
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
                            <th>Tên nhà cung cấp</th>
                            <th>Địa chỉ</th>
                            <th>Số điện thoại</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td><?php echo $supplier['idnhacungcap']; ?></td>
                            <td><?php echo htmlspecialchars($supplier['tennhacungcap']); ?></td>
                            <td><?php echo htmlspecialchars($supplier['diachi']); ?></td>
                            <td><?php echo htmlspecialchars($supplier['sdt']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($supplier['ngaytao'])); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary edit-supplier" 
                                        data-id="<?php echo $supplier['idnhacungcap']; ?>"
                                        data-name="<?php echo htmlspecialchars($supplier['tennhacungcap']); ?>"
                                        data-address="<?php echo htmlspecialchars($supplier['diachi']); ?>"
                                        data-phone="<?php echo htmlspecialchars($supplier['sdt']); ?>"
                                        data-bs-toggle="modal" data-bs-target="#editSupplierModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-supplier" 
                                        data-id="<?php echo $supplier['idnhacungcap']; ?>"
                                        data-name="<?php echo htmlspecialchars($supplier['tennhacungcap']); ?>">
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

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm nhà cung cấp mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSupplierForm" method="POST" action="nhacungcap_update.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="tennhacungcap" class="form-label">Tên nhà cung cấp</label>
                        <input type="text" class="form-control" id="tennhacungcap" name="tennhacungcap" required>
                    </div>
                    <div class="mb-3">
                        <label for="diachi" class="form-label">Địa chỉ</label>
                        <textarea class="form-control" id="diachi" name="diachi" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_sdt" class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" id="edit_sdt" name="sdt" required 
                            pattern="^0[0-9]{9}$" 
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10)"
                            title="Số điện thoại phải bắt đầu bằng số 0 và có 10 chữ số">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm nhà cung cấp</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa nhà cung cấp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSupplierForm" method="POST" action="nhacungcap_update.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="idnhacungcap" id="edit_idnhacungcap">
                    <div class="mb-3">
                        <label for="edit_tennhacungcap" class="form-label">Tên nhà cung cấp</label>
                        <input type="text" class="form-control" id="edit_tennhacungcap" name="tennhacungcap" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_diachi" class="form-label">Địa chỉ</label>
                        <textarea class="form-control" id="edit_diachi" name="diachi" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_sdt" class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" id="edit_sdt" name="sdt" required 
                               pattern="[0-9]{10,11}" title="Số điện thoại phải từ 10-11 số">
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
document.querySelectorAll('.edit-supplier').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        const address = this.getAttribute('data-address');
        const phone = this.getAttribute('data-phone');
        
        document.getElementById('edit_idnhacungcap').value = id;
        document.getElementById('edit_tennhacungcap').value = name;
        document.getElementById('edit_diachi').value = address;
        document.getElementById('edit_sdt').value = phone;
    });
});
function validatePhoneNumber(input) {
    let phone = input.value;
    
    // Remove any non-digit characters
    phone = phone.replace(/\D/g, '');
    
    // Ensure it starts with 0
    if (phone.length > 0 && phone[0] !== '0') {
        phone = '0' + phone;
    }
    
    // Limit to 10 digits
    phone = phone.substring(0, 10);
    
    // Update input value
    input.value = phone;
    
    // Validate format
    if (!/^0[0-9]{9}$/.test(phone)) {
        input.setCustomValidity('Số điện thoại phải bắt đầu bằng số 0 và có 10 chữ số');
    } else {
        input.setCustomValidity('');
    }
}

// Add event listeners to phone inputs
document.getElementById('sdt').addEventListener('input', function() {
    validatePhoneNumber(this);
});

document.getElementById('edit_sdt').addEventListener('input', function() {
    validatePhoneNumber(this);
});

function validatePhone(input) {
    // Remove any non-digit characters
    let phone = input.value.replace(/\D/g, '');
    
    // Ensure exactly 10 digits
    if (phone.length !== 10) {
        input.setCustomValidity('Số điện thoại phải có 10 chữ số!');
    } else {
        input.setCustomValidity('');
    }
}

// Add to your existing form elements
document.querySelectorAll('input[name="sdt"]').forEach(input => {
    input.addEventListener('input', function() {
        validatePhone(this);
    });
});

document.querySelectorAll('.delete-supplier').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        
        if (confirm(`Bạn có chắc chắn muốn xóa nhà cung cấp "${name}"?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'nhacungcap_update.php';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'delete';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'idnhacungcap';
            idInput.value = id;
            
            form.appendChild(actionInput);
            form.appendChild(idInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
});
</script>