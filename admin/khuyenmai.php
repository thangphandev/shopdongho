<?php
$connect = new Connect();
$search = $_GET['search'] ?? '';
$promotions = $connect->getAllPromotions($search);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add']) || isset($_POST['update'])) {
        $data = [
            'tenkhuyenmai' => $_POST['tenkhuyenmai'],
            'gia_giam' => $_POST['gia_giam'],
            'ngaybatdau' => $_POST['ngaybatdau'],
            'ngayketthuc' => $_POST['ngayketthuc']
        ];
        
        if (isset($_POST['add'])) {
            if ($connect->addPromotion($data)) {
                echo "<div class='alert alert-success'>Thêm khuyến mãi thành công!</div>";
            } else {
                echo "<div class='alert alert-danger'>Có lỗi xảy ra!</div>";
            }
        } else {
            $data['idkhuyenmai'] = $_POST['idkhuyenmai'];
            if ($connect->updatePromotion($data)) {
                echo "<div class='alert alert-success'>Cập nhật khuyến mãi thành công!</div>";
            } else {
                echo "<div class='alert alert-danger'>Có lỗi xảy ra!</div>";
            }
        }
    } elseif (isset($_POST['delete'])) {
        if ($connect->deletePromotion($_POST['idkhuyenmai'])) {
            echo "<div class='alert alert-success'>Xóa khuyến mãi thành công!</div>";
        } else {
            echo "<div class='alert alert-danger'>Có lỗi xảy ra!</div>";
        }
    }
    $promotions = $connect->getAllPromotionsadmin($search);
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Quản lý khuyến mãi</h2>

    <!-- Search and Add button -->
    <div class="row mb-3">
        <div class="col-md-6">
            <form class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Tìm kiếm..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            </form>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                Thêm khuyến mãi
            </button>
        </div>
    </div>

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
                        <button class="btn btn-sm btn-primary" onclick="editPromotion(<?php echo htmlspecialchars(json_encode($promotion)); ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deletePromotion(<?php echo $promotion['idkhuyenmai']; ?>)">
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
                        <label class="form-label">Giá giảm</label>
                        <input type="number" class="form-control" name="gia_giam" required>
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
                        <label class="form-label">Giá giảm</label>
                        <input type="number" class="form-control" name="gia_giam" id="edit_gia_giam" required>
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
                Bạn có chắc chắn muốn xóa khuyến mãi này không?
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
function editPromotion(promotion) {
    document.getElementById('edit_idkhuyenmai').value = promotion.idkhuyenmai;
    document.getElementById('edit_tenkhuyenmai').value = promotion.tenkhuyenmai;
    document.getElementById('edit_gia_giam').value = promotion.gia_giam;
    
    // Format datetime to YYYY-MM-DDTHH:mm
    let ngaybatdau = new Date(promotion.ngaybatdau);
    let ngayketthuc = new Date(promotion.ngayketthuc);
    
    document.getElementById('edit_ngaybatdau').value = ngaybatdau.toISOString().slice(0,16);
    document.getElementById('edit_ngayketthuc').value = ngayketthuc.toISOString().slice(0,16);
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function deletePromotion(id) {
    document.getElementById('delete_idkhuyenmai').value = id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>