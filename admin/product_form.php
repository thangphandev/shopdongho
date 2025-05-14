<?php
// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Get all categories
$categories = $connect->getAllCategories();

// Get all watch types
$watchTypes = $connect->getAllWatchTypes();

// Get all strap types
$strapTypes = $connect->getAllStrapTypes();

// Get all promotions
$promotions = $connect->getAllPromotions();

// Get all suppliers
$suppliers = $connect->getAllSuppliers();

// Get all warranty policies
$warrantyPolicies = $connect->getAllWarrantyPolicies();

// Initialize product data
$product = [
    'idsanpham' => '',
    'tensanpham' => '',
    'mota' => '',
    'giaban' => '',
    'gianhap' => '',
    'soluong' => '',
    'iddanhmuc' => '',
    'loaiday' => '',
    'loaimay' => '',
    'gioitinh' => '',
    'path_anh_goc' => '',
    'trangthai' => 1,
    'idkhuyenmai' => null,
    'bosuutap' => '',
    'chatlieuvo' => '',
    'matkinh' => '',
    'mausac' => '',
    'kichthuoc' => '',
    'doday' => '',
    'chongnuoc' => '',
    'tinhnangdacbiet' => '',
    'chinhsachbaohanh' => '',
    'idnhacungcap' => ''
];

// Check if editing existing product
$isEditing = false;
if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $productData = $connect->getProductById($productId);
    
    if ($productData) {
        $product = $productData;
        $isEditing = true;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $productData = [        
        'tensanpham' => $_POST['tensanpham'],
        'mota' => $_POST['mota'] ?? '',
        'giaban' => $_POST['giaban'],
        'gianhap' => $_POST['gianhap'],
        'soluong' => $_POST['soluong'],
        'iddanhmuc' => $_POST['iddanhmuc'],
        'loaiday' => $_POST['loaiday'] ?? null,
        'loaimay' => $_POST['loaimay'] ?? null,
        'gioitinh' => $_POST['gioitinh'] ?? null,           
        'trangthai' => isset($_POST['trangthai']) && $_POST['trangthai'] === 'on' ? 1 : 0, // Fixed checkbox handling
        'idkhuyenmai' => !empty($_POST['idkhuyenmai']) ? $_POST['idkhuyenmai'] : null,
        'chinhsachbaohanh' => !empty($_POST['chinhsachbaohanh']) ? $_POST['chinhsachbaohanh'] : null,
        'bosuutap' => $_POST['bosuutap'] ?? '',
        'chatlieuvo' => $_POST['chatlieuvo'] ?? '',
        'matkinh' => $_POST['matkinh'] ?? '',
        'mausac' => $_POST['mausac'] ?? '',
        'kichthuoc' => $_POST['kichthuoc'] ?? '',
        'doday' => $_POST['doday'] ?? '',
        'chongnuoc' => $_POST['chongnuoc'] ?? '',
        'tinhnangdacbiet' => $_POST['tinhnangdacbiet'] ?? '',
        'idnhacungcap' => $_POST['idnhacungcap']
    ];

    $targetDir = "../imageproduct/";
    $targetPath = "imageproduct/";
    // Define allowed file types
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    
    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Handle main product image
    if (!empty($_FILES['product_image']['name'])) {
        $fileName = basename($_FILES["product_image"]["name"]);
        $targetFilePath = $targetDir . time() . '_' . $fileName;
        $dbFilePath = $targetPath . time() . '_' . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFilePath)) {
                $productData['path_anh_goc'] = $dbFilePath;
            } else {
                $_SESSION['error_message'] = "Có lỗi khi tải ảnh chính lên.";
            }
        } else {
            $_SESSION['error_message'] = "Chỉ cho phép tải lên các file JPG, JPEG, PNG & GIF.";
        }
    } else if ($isEditing) {
        $productData['path_anh_goc'] = $product['path_anh_goc'];
    }
    
    // Handle additional images
    $additionalImages = [];
    if (!empty($_FILES['additional_images']['name'][0])) {
        foreach ($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
            $fileName = basename($_FILES['additional_images']['name'][$key]);
            $targetFilePath = $targetDir . time() . '_additional_' . $fileName;
            $dbFilePath = $targetPath . time() . '_additional_' . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            
            if (in_array($fileType, $allowTypes)) {
                if (move_uploaded_file($tmp_name, $targetFilePath)) {
                    $additionalImages[] = $dbFilePath;
                }
            }
        }
    }
    
    if ($isEditing) {
        // Add debug logging
        error_log("Updating product with ID: " . $productId);
        error_log("Product Data: " . print_r($productData, true));
        
        // Update product details
        if ($connect->updateProduct($productId, $productData)) {
            // Add new additional images without deleting existing ones
            if (!empty($additionalImages)) {
                $connect->addProductImages($productId, $additionalImages);
            }
            $_SESSION['success_message'] = "Sản phẩm đã được cập nhật thành công!";
            header('Location: admin.php?page=products');
            exit;
        } else {
            $_SESSION['error_message'] = "Không thể cập nhật sản phẩm. Vui lòng thử lại!";
        }
    } else {
        // Add new product
        $newProductId = $connect->addProduct($productData);
        if ($newProductId) {
            // Add additional images if any
            if (!empty($additionalImages)) {
                $connect->addProductImages($newProductId, $additionalImages);
            }
            $_SESSION['success_message'] = "Sản phẩm đã được thêm thành công!";
            header('Location: admin.php?page=products');
            exit;
        } else {
            $_SESSION['error_message'] = "Không thể thêm sản phẩm!";
        }
    }
}
?>

<style>
.form-label {
    font-weight: bold;
}
.input-group-text {
    background-color: #f8f9fa;
}
.card-body {
    padding: 1.5rem;
}
.mb-3 {
    margin-bottom: 1rem !important;
}
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $isEditing ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới'; ?></h1>
        <a href="admin.php?page=products" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
    
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
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row">
                    <!-- Left Column: Basic and Detailed Information -->
                    <div class="col-md-8">
                        <!-- Basic Information -->
                        <h5 class="mb-3">Thông tin cơ bản</h5>
                        <div class="mb-3">
                            <label for="tensanpham" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tensanpham" name="tensanpham" value="<?php echo htmlspecialchars($product['tensanpham']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mota" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="mota" name="mota" rows="4"><?php echo htmlspecialchars($product['mota']); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="giaban" class="form-label">Giá bán <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="giaban" name="giaban" value="<?php echo $product['giaban']; ?>" min="0" oninput="validatePositiveNumber(this)" required>
                                        <span class="input-group-text">₫</span>
                                    </div>
                                    <div id="giaban-error" class="invalid-feedback" style="display: none;">
                                        Giá bán phải lớn hơn giá nhập
                                    </div>
                                    <div id="giaban-negative-error" class="invalid-feedback" style="display: none;">
                                        Giá bán không được là số âm
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gianhap" class="form-label">Giá nhập <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="gianhap" name="gianhap" value="<?php echo $product['gianhap']; ?>" min="0" oninput="validatePositiveNumber(this)" required>
                                        <span class="input-group-text">₫</span>
                                    </div>
                                    <div id="gianhap-negative-error" class="invalid-feedback" style="display: none;">
                                        Giá nhập không được là số âm
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="soluong" class="form-label">Số lượng <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="soluong" name="soluong" value="<?php echo $product['soluong']; ?>" min="0" onkeypress="return event.charCode >= 48"  oninput="validity.valid||(value='');" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="iddanhmuc" class="form-label">Danh mục <span class="text-danger">*</span></label>
                                    <select class="form-select" id="iddanhmuc" name="iddanhmuc" required>
                                        <option value="">Chọn danh mục</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['iddanhmuc']; ?>" <?php echo ($product['iddanhmuc'] == $category['iddanhmuc']) ? 'selected' : ''; ?>>
                                                <?php echo $category['tendanhmuc']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Product Specifications -->
                        <h5 class="mb-3 mt-4">Thông số sản phẩm</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="loaimay" class="form-label">Loại máy</label>
                                    <select class="form-select" id="loaimay" name="loaimay">
                                        <option value="">Chọn loại máy</option>
                                        <?php foreach ($watchTypes as $type): ?>
                                            <option value="<?php echo $type['id_loai_may']; ?>" <?php echo ($product['loaimay'] == $type['id_loai_may']) ? 'selected' : ''; ?>>
                                                <?php echo $type['ten_loai_may']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="loaiday" class="form-label">Loại dây</label>
                                    <select class="form-select" id="loaiday" name="loaiday">
                                        <option value="">Chọn loại dây</option>
                                        <?php foreach ($strapTypes as $type): ?>
                                            <option value="<?php echo $type['id_loai_day']; ?>" <?php echo ($product['loaiday'] == $type['id_loai_day']) ? 'selected' : ''; ?>>
                                                <?php echo $type['ten_loai_day']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="gioitinh" class="form-label">Giới tính</label>
                                    <select class="form-select" id="gioitinh" name="gioitinh">
                                        <option value="">Chọn giới tính</option>
                                        <option value="Nam" <?php echo ($product['gioitinh'] == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                                        <option value="Nữ" <?php echo ($product['gioitinh'] == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                                        <option value="Unisex" <?php echo ($product['gioitinh'] == 'Unisex') ? 'selected' : ''; ?>>Unisex</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bosuutap" class="form-label">Bộ sưu tập</label>
                                    <input type="text" class="form-control" id="bosuutap" name="bosuutap" value="<?php echo htmlspecialchars($product['bosuutap']); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="chatlieuvo" class="form-label">Chất liệu vỏ</label>
                                    <input type="text" class="form-control" id="chatlieuvo" name="chatlieuvo" value="<?php echo htmlspecialchars($product['chatlieuvo']); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="matkinh" class="form-label">Mặt kính</label>
                                    <input type="text" class="form-control" id="matkinh" name="matkinh" value="<?php echo htmlspecialchars($product['matkinh']); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mausac" class="form-label">Màu sắc</label>
                                    <input type="text" class="form-control" id="mausac" name="mausac" value="<?php echo htmlspecialchars($product['mausac']); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="kichthuoc" class="form-label">Kích thước</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="kichthuoc" name="kichthuoc" value="<?php echo htmlspecialchars($product['kichthuoc']); ?>" oninput="validatePositiveNumber(this)">
                                        <span class="input-group-text">mm</span>
                                    </div>
                                    <div id="kichthuoc-negative-error" class="invalid-feedback" style="display: none;">
                                        Kích thước không được là số âm  
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="doday" class="form-label">Độ dày</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="doday" name="doday" value="<?php echo htmlspecialchars($product['doday']); ?>" oninput="validatePositiveNumber(this)">
                                        <span class="input-group-text">mm</span>
                                    </div>
                                    <div id="doday-negative-error" class="invalid-feedback" style="display: none;">
                                        Độ dày không được là số âm
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="chongnuoc" class="form-label">Chống nước</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="chongnuoc" name="chongnuoc" value="<?php echo htmlspecialchars($product['chongnuoc']); ?>" oninput="validatePositiveNumber(this)">
                                        <span class="input-group-text">ATM</span>
                                    </div>
                                    <div id="chongnuoc-negative-error" class="invalid-feedback" style="display: none;">
                                        Chống nước không được là số âm
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tinhnangdacbiet" class="form-label">Tính năng đặc biệt</label>
                            <textarea class="form-control" id="tinhnangdacbiet" name="tinhnangdacbiet" rows="3"><?php echo htmlspecialchars($product['tinhnangdacbiet']); ?></textarea>
                        </div>

                        <!-- Additional Information -->
                        <h5 class="mb-3 mt-4">Thông tin bổ sung</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="idkhuyenmai" class="form-label">Khuyến mãi</label>
                                    <select class="form-select" id="idkhuyenmai" name="idkhuyenmai">
                                        <option value="">Không áp dụng khuyến mãi</option>
                                        <?php foreach ($promotions as $promotion): ?>
                                            <option value="<?php echo $promotion['idkhuyenmai']; ?>" 
                                                    <?php echo ($product['idkhuyenmai'] == $promotion['idkhuyenmai']) ? 'selected' : ''; ?>
                                                    <?php echo (strtotime($promotion['ngayketthuc']) < time()) ? 'disabled' : ''; ?>>
                                                <?php 
                                                    echo $promotion['tenkhuyenmai'] . ' (' . 
                                                         date('d/m/Y', strtotime($promotion['ngaybatdau'])) . ' - ' . 
                                                         date('d/m/Y', strtotime($promotion['ngayketthuc'])) . ')'; 
                                                ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="chinhsachbaohanh" class="form-label">Chính sách bảo hành</label>
                                    <select class="form-select" id="chinhsachbaohanh" name="chinhsachbaohanh">
                                        <option value="">Chọn chính sách bảo hành</option>
                                        <?php foreach ($warrantyPolicies as $policy): ?>
                                            <option value="<?php echo $policy['id_chinh_sach']; ?>" 
                                                    <?php echo ($product['chinhsachbaohanh'] == $policy['id_chinh_sach']) ? 'selected' : ''; ?>>
                                                <?php echo $policy['ten_chinh_sách']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="idnhacungcap" class="form-label">Nhà cung cấp <span class="text-danger">*</span></label>
                            <select class="form-select" id="idnhacungcap" name="idnhacungcap" required>
                                <option value="">Chọn nhà cung cấp</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?php echo $supplier['idnhacungcap']; ?>" <?php echo ($product['idnhacungcap'] == $supplier['idnhacungcap']) ? 'selected' : ''; ?>>
                                        <?php echo $supplier['tennhacungcap']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Right Column: Images and Publishing -->
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Hình ảnh sản phẩm</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="product_image" class="form-label">Ảnh chính sản phẩm</label>
                                    <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*" <?php echo (!$isEditing) ?>>
                                </div>
                                <?php if (!empty($product['path_anh_goc'])): ?>
                                    <div class="current-image">
                                        <p class="mb-2">Ảnh chính hiện tại:</p>
                                        <img src="../<?php echo $product['path_anh_goc']; ?>" alt="Current product image" class="img-thumbnail mb-2" style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3 mt-4">
                                    <label for="additional_images" class="form-label">Ảnh phụ sản phẩm</label>
                                    <input type="file" class="form-control" id="additional_images" name="additional_images[]" accept="image/*" multiple>
                                    <small class="text-muted d-block mt-1">Có thể chọn nhiều ảnh cùng lúc</small>
                                </div>
                                <div id="additional-images-preview" class="row mt-2"></div>
                                <?php if (!empty($product['additional_images'])): ?>
                                    <div class="current-additional-images">
                                        <p class="mb-2">Ảnh phụ hiện tại:</p>
                                        <div class="row">
                                            <?php foreach ($product['additional_images'] as $image): ?>
                                                <div class="col-md-4 mb-2">
                                                    <div class="position-relative">
                                                        <img src="../<?php echo $image['duongdan']; ?>" alt="Additional product image" class="img-thumbnail" style="max-width: 100px;">
                                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-image" data-image-id="<?php echo $image['idhinhanh']; ?>">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <small class="text-muted">Hỗ trợ: JPG, JPEG, PNG, GIF</small>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Xuất bản</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="trangthai" name="trangthai" <?php echo $product['trangthai'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="trangthai">Hiển thị sản phẩm</label>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <?php echo $isEditing ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm'; ?>
                                    </button>
                                    <a href="admin.php?page=products" class="btn btn-outline-secondary">Hủy</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Image Script -->
<script>
document.getElementById('additional_images').addEventListener('change', function(e) {
    const previewContainer = document.getElementById('additional-images-preview');
    previewContainer.innerHTML = ''; // Clear existing previews
    
    if (e.target.files) {
        Array.from(e.target.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-4 mb-2 position-relative';
                col.innerHTML = `
                    <div class="image-container">
                        <img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 100px;">
                        <button type="button" class="btn btn-danger btn-sm delete-preview" data-index="${index}" style="position: absolute; top: 0; right: 0;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                previewContainer.appendChild(col);
                
                // Add click event for delete button
                col.querySelector('.delete-preview').addEventListener('click', function() {
                    col.remove();
                    // Remove file from input
                    const dt = new DataTransfer();
                    const input = document.getElementById('additional_images');
                    const { files } = input;
                    
                    for (let i = 0; i < files.length; i++) {
                        if (i !== parseInt(this.dataset.index)) {
                            dt.items.add(files[i]);
                        }
                    }
                    
                    input.files = dt.files;
                });
            }
            reader.readAsDataURL(file);
        });
    }
});
function validatePrices() {
    const giaban = parseFloat(document.getElementById('giaban').value) || 0;
    const gianhap = parseFloat(document.getElementById('gianhap').value) || 0;
    const giabanError = document.getElementById('giaban-error');
    const submitButton = document.querySelector('button[type="submit"]');

    if (giaban <= gianhap && giaban !== 0) {
        giabanError.style.display = 'block';
        document.getElementById('giaban').classList.add('is-invalid');
        submitButton.disabled = true;
    } else {
        giabanError.style.display = 'none';
        document.getElementById('giaban').classList.remove('is-invalid');
        submitButton.disabled = false;
    }
}

// Add form validation before submit
document.querySelector('form').addEventListener('submit', function(e) {
    const giaban = parseFloat(document.getElementById('giaban').value) || 0;
    const gianhap = parseFloat(document.getElementById('gianhap').value) || 0;

    if (giaban <= gianhap) {
        e.preventDefault();
        document.getElementById('giaban-error').style.display = 'block';
        document.getElementById('giaban').classList.add('is-invalid');
    }
});
// Delete existing images
document.querySelectorAll('.delete-image').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const imageId = this.getAttribute('data-image-id');
        const container = this.closest('.col-md-4'); // Changed to match the actual container class
        
        if (confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
            fetch('delete_image.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ imageId: imageId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.remove();
                    // Optional: Show success message
                    alert('Xóa ảnh thành công!');
                } else {
                    alert(data.message || 'Không thể xóa ảnh. Vui lòng thử lại.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Không thể kết nối đến máy chủ. Vui lòng thử lại sau.');
            });
        }
    });
});

document.getElementById('product_image').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const currentImage = document.querySelector('.current-image');
            if (currentImage) {
                currentImage.innerHTML = `
                    <p class="mb-2">Xem trước:</p>
                    <img src="${e.target.result}" alt="Preview" class="img-thumbnail mb-2" style="max-width: 200px;">
                `;
            } else {
                const previewContainer = document.createElement('div');
                previewContainer.className = 'current-image';
                previewContainer.innerHTML = `
                    <p class="mb-2">Xem trước:</p>
                    <img src="${e.target.result}" alt="Preview" class="img-thumbnail mb-2" style="max-width: 200px;">
                `;
                document.getElementById('product_image').parentNode.appendChild(previewContainer);
            }
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});

document.getElementById('product_image').addEventListener('change', function(e) {
    // ... existing code ...
});

// Thêm hàm kiểm tra số dương
function validatePositiveNumber(input) {
    // Xóa các ký tự không phải số và dấu thập phân
    let value = input.value;
    
    // Kiểm tra nếu giá trị chứa dấu trừ
    if (value.includes('-')) {
        // Hiển thị thông báo lỗi cho giá trị âm
        const errorId = input.id + '-negative-error';
        document.getElementById(errorId).style.display = 'block';
        input.classList.add('is-invalid');
        
        // Xóa dấu trừ
        value = value.replace(/-/g, '');
        input.value = value;
    } else {
        // Ẩn thông báo lỗi nếu không có dấu trừ
        const errorId = input.id + '-negative-error';
        if (document.getElementById(errorId)) {
            document.getElementById(errorId).style.display = 'none';
            input.classList.remove('is-invalid');
        }
    }
    
    // Chỉ cho phép số và dấu thập phân
    input.value = value.replace(/[^\d.]/g, '');
    
    // Kiểm tra giá nếu cả hai trường đều có giá trị
    if (input.id === 'giaban' || input.id === 'gianhap') {
        validatePrices();
    }
}

// Cập nhật hàm validatePrices để hoạt động với hệ thống xác thực mới
function validatePrices() {
    const giaban = parseFloat(document.getElementById('giaban').value) || 0;
    const gianhap = parseFloat(document.getElementById('gianhap').value) || 0;
    const giabanError = document.getElementById('giaban-error');
    const submitButton = document.querySelector('button[type="submit"]');

    if (giaban <= gianhap && giaban !== 0) {
        giabanError.style.display = 'block';
        document.getElementById('giaban').classList.add('is-invalid');
        submitButton.disabled = true;
    } else {
        giabanError.style.display = 'none';
        if (!document.getElementById('giaban').value.includes('-')) {
            document.getElementById('giaban').classList.remove('is-invalid');
        }
        submitButton.disabled = false;
    }
}



</script>