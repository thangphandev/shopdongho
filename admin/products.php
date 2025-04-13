<?php
// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once '../connect.php';

// Get search parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Get all products
$products = $connect->getAllProducts($search, $category, $sort, $order);

// Get all categories for filter
$categories = $connect->getAllCategories();

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete product
    if (isset($_POST['delete_product'])) {
        $productId = $_POST['product_id'];
        if ($connect->deleteProduct($productId)) {
            $_SESSION['success_message'] = "Sản phẩm đã được xóa thành công!";
        } else {
            $_SESSION['error_message'] = "Không thể xóa sản phẩm!";
        }
        header('Location: admin.php?page=products');
        exit;
    }
    
    // Update product status
    if (isset($_POST['update_status'])) {
        $productId = $_POST['product_id'];
        $status = $_POST['status'];
        $product = $connect->getProductById($productId);
        $product['trangthai'] = $status;
        if ($connect->updateProduct($productId, $product)) {
            $_SESSION['success_message'] = "Trạng thái sản phẩm đã được cập nhật!";
        } else {
            $_SESSION['error_message'] = "Không thể cập nhật trạng thái sản phẩm!";
        }
        header('Location: admin.php?page=products');
        exit;
    }
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quản lý sản phẩm</h1>
        <a href="admin.php?page=product_form" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm sản phẩm mới
        </a>
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
    
    <!-- Filter and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <input type="hidden" name="page" value="products">
                
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['iddanhmuc']; ?>" <?php echo ($category == $cat['iddanhmuc']) ? 'selected' : ''; ?>>
                                <?php echo $cat['tendanhmuc']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <select name="sort" class="form-select">
                        <option value="">Sắp xếp mặc định</option>
                        <option value="tensanpham" <?php echo ($sort == 'tensanpham') ? 'selected' : ''; ?>>Tên sản phẩm</option>
                        <option value="giaban" <?php echo ($sort == 'giaban') ? 'selected' : ''; ?>>Giá bán</option>
                        <option value="soluong" <?php echo ($sort == 'soluong') ? 'selected' : ''; ?>>Số lượng</option>
                        <option value="ngaytao" <?php echo ($sort == 'ngaytao') ? 'selected' : ''; ?>>Ngày tạo</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <select name="order" class="form-select">
                        <option value="ASC" <?php echo ($order == 'ASC') ? 'selected' : ''; ?>>Tăng dần</option>
                        <option value="DESC" <?php echo ($order == 'DESC') ? 'selected' : ''; ?>>Giảm dần</option>
                    </select>
                </div>
                
                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary">Lọc</button>
                    <a href="admin.php?page=products" class="btn btn-secondary">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Products Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá bán</th>
                            <th>Tồn kho</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Không có sản phẩm nào</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['idsanpham']; ?></td>
                                    <td>
                                        <?php if (!empty($product['path_anh_goc'])): ?>
                                            <img src="../<?php echo $product['path_anh_goc']; ?>" alt="<?php echo $product['tensanpham']; ?>" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light text-center" style="width: 50px; height: 50px; line-height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $product['tensanpham']; ?></td>
                                    <td><?php echo $product['tendanhmuc']; ?></td>
                                    <td><?php echo number_format($product['giaban'], 0, ',', '.'); ?> ₫</td>
                                    <td>
                                        <?php if ($product['soluong'] <= 0): ?>
                                            <span class="badge bg-danger">Hết hàng</span>
                                        <?php elseif ($product['soluong'] <= 5): ?>
                                            <span class="badge bg-warning"><?php echo $product['soluong']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?php echo $product['soluong']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?php echo $product['idsanpham']; ?>">
                                            <input type="hidden" name="status" value="<?php echo $product['trangthai'] ? '0' : '1'; ?>">
                                            <button type="submit" name="update_status" class="btn btn-sm <?php echo $product['trangthai'] ? 'btn-success' : 'btn-secondary'; ?>">
                                                <?php echo $product['trangthai'] ? 'Hiện' : 'Ẩn'; ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="admin.php?page=product_form&id=<?php echo $product['idsanpham']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $product['idsanpham']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal<?php echo $product['idsanpham']; ?>" tabadmin="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Bạn có chắc chắn muốn xóa sản phẩm <strong><?php echo $product['tensanpham']; ?></strong>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                        <form method="POST" action="">
                                                            <input type="hidden" name="product_id" value="<?php echo $product['idsanpham']; ?>">
                                                            <button type="submit" name="delete_product" class="btn btn-danger">Xóa</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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