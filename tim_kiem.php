<?php
require_once 'connect.php';
session_start();
$connect = new Connect();
// $cartItems = $connect->getCartItems($_SESSION['user_id']);

// Get search parameters
$keyword = $_GET['key'] ?? '';
$brands = $_GET['brands'] ?? [];
$watch_types = $_GET['watch_types'] ?? [];
$strap_types = $_GET['strap_types'] ?? [];
$gender = $_GET['gender'] ?? [];
$price_ranges = $_GET['price_range'] ?? [];
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (int)$_GET['max_price'] : PHP_INT_MAX;

// Get search results
$searchResults = $connect->searchProducts($keyword, $brands, $watch_types, $strap_types, $gender, $price_ranges, $min_price, $max_price);

// Include header
include 'header.php';
?>


<!DOCTYPE html>
<html itemscope="" itemtype="http://schema.org/WebPage" lang="vi">
    <script src="js/jquery3.2.1.min.js" defer=""></script>
    <script src="js/bootstrap.min.js" defer=""></script>
    <script src="js/jquery.fancybox.min.js" defer=""></script>
    <script src="js/toastr.min.js" defer=""></script>
    <script src="js/social.js" defer=""></script>
    <script src="js/lazyload.min.js" defer=""></script>
    <script src="js/wow.js" defer=""></script>
    <script src="js/tiny-slider.js" defer=""></script>
    <script src="js/script.js" defer=""></script>
    <script src="js/cart.js" defer=""></script>
<div class="main-content">
<div class="b-text d-flex flex-wrap align-items-center text-center arrow_custom justify-content-center" style="padding-top:50px;">
    <h1 class="fs36 clnau line_tt text-uppercase lora">Kết quả tìm kiếm</h1>
</div>

<div class="container py-5">
    <div class="row">
        <!-- Filter sidebar -->
        <div class="col-lg-3">
            <div class="filter-sidebar">
                <form method="GET" action="tim_kiem.php" class="filter-form">
                    <!-- Keyword -->
                    <div class="filter-section mb-4">
                        <h4 class="filter-title">Từ khóa</h4>
                        <input type="text" name="key" class="form-control" placeholder="Nhập từ khóa" value="<?= htmlspecialchars($keyword) ?>">
                    </div>
                    
                    <!-- Brands -->
                    <div class="filter-section mb-4">
                        <h4 class="filter-title">Thương hiệu</h4>
                        <div class="filter-content scrollable-filter">
                            <?php
                            $allBrands = $connect->getAllBrands();
                            foreach ($allBrands as $brand):
                            ?>
                            <label class="custom-checkbox d-block mb-2">
                                <input type="checkbox" name="brands[]" value="<?= $brand['iddanhmuc'] ?>"
                                    <?= in_array($brand['iddanhmuc'], $brands) ? 'checked' : '' ?>>
                                <span class="checkmark"></span>
                                <span class="label-text"><?= htmlspecialchars($brand['tendanhmuc']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Watch Types -->
                    <div class="filter-section mb-4">
                        <h4 class="filter-title">Loại máy</h4>
                        <div class="filter-content scrollable-filter">
                            <?php
                            $allWatchTypes = $connect->getAllWatchTypes();
                            foreach ($allWatchTypes as $type):
                            ?>
                            <label class="custom-checkbox d-block mb-2">
                                <input type="checkbox" name="watch_types[]" value="<?= $type['id_loai_may'] ?>"
                                    <?= in_array($type['id_loai_may'], $watch_types) ? 'checked' : '' ?>>
                                <span class="checkmark"></span>
                                <span class="label-text"><?= htmlspecialchars($type['ten_loai_may']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Strap Types -->
                    <div class="filter-section mb-4">
                        <h4 class="filter-title">Loại dây</h4>
                        <div class="filter-content scrollable-filter">
                            <?php
                            $allStrapTypes = $connect->getStrapTypes();
                            foreach ($allStrapTypes as $type):
                            ?>
                            <label class="custom-checkbox d-block mb-2">
                                <input type="checkbox" name="strap_types[]" value="<?= $type['id_loai_day'] ?>"
                                    <?= in_array($type['id_loai_day'], $strap_types) ? 'checked' : '' ?>>
                                <span class="checkmark"></span>
                                <span class="label-text"><?= htmlspecialchars($type['ten_loai_day']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Gender -->
                    <div class="filter-section mb-4">
                        <h4 class="filter-title">Giới tính</h4>
                        <div class="filter-content">
                            <?php
                            $genders = ['nam' => 'Nam', 'nu' => 'Nữ', 'unisex' => 'Unisex'];
                            foreach ($genders as $value => $label):
                            ?>
                            <label class="custom-checkbox d-block mb-2">
                                <input type="checkbox" name="gender[]" value="<?= $value ?>"
                                    <?= in_array($value, $gender) ? 'checked' : '' ?>>
                                <span class="checkmark"></span>
                                <span class="label-text"><?= $label ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Price Ranges -->
                    <div class="filter-section mb-4">
                        <h4 class="filter-title">Phân khúc giá</h4>
                        <div class="filter-content">
                            <?php
                            $priceRanges = [
                                '10000000-200000000' => 'Dưới 200 triệu VNĐ',
                                '200000000-500000000' => 'Từ 200 - 500 triệu VNĐ',
                                '500000000-1000000000' => 'Từ 500 triệu - 1 tỷ VNĐ',
                                '1000000000-2000000000' => 'Từ 1 - 2 tỷ VNĐ',
                                '2000000000-5000000000' => 'Từ 2 tỷ - 5 tỷ VNĐ'
                            ];
                            foreach ($priceRanges as $value => $label):
                            ?>
                            <label class="custom-checkbox d-block mb-2">
                                <input type="checkbox" name="price_range[]" value="<?= $value ?>"
                                    <?= in_array($value, $price_ranges) ? 'checked' : '' ?>>
                                <span class="checkmark"></span>
                                <span class="label-text"><?= $label ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Price Slider -->
                    <div class="filter-section mb-4">
                        <h4 class="filter-title">Khoảng giá</h4>
                        <div class="price-range-wrapper">
                            <div class="price-input">
                                <div class="field">
                                    <span>Min</span>
                                    <input type="number" name="min_price" class="input-min" value="<?= $min_price ?>">
                                </div>
                                <div class="separator">-</div>
                                <div class="field">
                                    <span>Max</span>
                                    <input type="number" name="max_price" class="input-max" value="<?= $max_price === PHP_INT_MAX ? 5000000000 : $max_price ?>">
                                </div>
                            </div>
                            <div class="slider">
                                <div class="progress"></div>
                            </div>
                            <div class="range-input">
                                <input type="range" class="range-min" min="0" max="5000000000" value="<?= $min_price ?>" step="1000000">
                                <input type="range" class="range-max" min="0" max="5000000000" value="<?= $max_price === PHP_INT_MAX ? 5000000000 : $max_price ?>" step="1000000">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Áp dụng</button>
                </form>
            </div>
        </div>

        <!-- Search results -->
        <div class="col-lg-9">
            <div class="search-results">
                <?php if (empty($searchResults)): ?>
                    <div class="no-results text-center py-5">
                        <i class="fa fa-search fa-3x mb-3"></i>
                        <h3>Không tìm thấy sản phẩm</h3>
                        <p>Vui lòng thử lại với từ khóa khác</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($searchResults as $product): ?>
                            <div class="col-md-4 mb-4">
                                <div class="d_pro_item text-center pro_mw">
                                    <a href="chi_tiet_san_pham.php?id=<?= htmlspecialchars($product['idsanpham']) ?>"
                                       title="<?= htmlspecialchars($product['tensanpham']) ?>"
                                       class="smooth c-img marb-24">
                                        <img class="contain lazy"
                                             loading="lazy"
                                             data-src="<?= htmlspecialchars($product['path_anh_goc'] ?? 'images/no-image.jpg') ?>"
                                             src="<?= htmlspecialchars($product['path_anh_goc'] ?? 'images/no-image.jpg') ?>"
                                             alt="<?= htmlspecialchars($product['tensanpham']) ?>"
                                             title="<?= htmlspecialchars($product['tensanpham']) ?>">
                                        <?php 
                                        // Giả định sản phẩm "mới" dựa trên ngày thêm (nếu có trường ngaythem)
                                        $isNew = !empty($product['ngaythem']) && (time() - strtotime($product['ngaythem']) < 7 * 24 * 3600);
                                        if ($isNew): ?>
                                            <div class="status status--new">New</div>
                                        <?php endif; ?>
                                        <?php if (!empty($product['gia_giam']) && 
                                                  (empty($product['ngaybatdau']) || strtotime($product['ngaybatdau']) <= time()) && 
                                                  (empty($product['ngayketthuc']) || strtotime($product['ngayketthuc']) >= time())): ?>
                                            <div class="status status--discount">-<?= number_format($product['gia_giam'], 0, ',', '.') ?>đ</div>
                                        <?php endif; ?>
                                    </a>
                                    <p class="clc1 text-uppercase fs15">MSP: <?= htmlspecialchars($product['idsanpham']) ?></p>
                                    <h3>
                                        <a href="chi_tiet_san_pham.php?id=<?= htmlspecialchars($product['idsanpham']) ?>"
                                           title="<?= htmlspecialchars($product['tensanpham']) ?>"
                                           class="smooth hvnau fs15"><?= htmlspecialchars($product['tensanpham']) ?></a>
                                    </h3>
                                    <?php if (!empty($product['giaban'])): ?>
                                        <?php if (!empty($product['gia_giam']) && 
                                                  (empty($product['ngaybatdau']) || strtotime($product['ngaybatdau']) <= time()) && 
                                                  (empty($product['ngayketthuc']) || strtotime($product['ngayketthuc']) >= time())): ?>
                                            <p class="clnau fs16 text-decoration-line-through"><?= number_format($product['giaban'], 0, ',', '.') ?>đ</p>
                                            <p class="clnau fs18 fw-bold"><?= number_format($product['giaban'] - $product['gia_giam'], 0, ',', '.') ?>đ</p>
                                        <?php else: ?>
                                            <p class="clnau fs16"><?= number_format($product['giaban'], 0, ',', '.') ?>đ</p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="clnau fs16">Liên hệ</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>
<style>
/* Filter Sidebar Styles */
.main-content {
    background-image: url('images/anh-nen-pp.jpg');
        background-color: rgba(31, 61, 90, 0.7);
        background-blend-mode: overlay;
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        min-height: 100vh;
        padding: 150px 0 50px;
        position: relative;
        color:#dbaf56;
}
.filter-sidebar {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 20px;
    border-radius: 8px;
}

.filter-title {
    color: #dbaf56;
    font-size: 18px;
    margin-bottom: 15px;
    border-bottom: 1px solid #dbaf56;
    padding-bottom: 10px;
}

.scrollable-filter {
    max-height: 200px;
    overflow-y: auto;
    padding-right: 10px;
    scrollbar-width: thin;
    scrollbar-color: #dbaf56 rgba(255, 255, 255, 0.1);
}

.scrollable-filter::-webkit-scrollbar {
    width: 5px;
}

.scrollable-filter::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

.scrollable-filter::-webkit-scrollbar-thumb {
    background-color: #dbaf56;
    border-radius: 10px;
}

/* Product Card Styles (from sample) */
.d_pro_item {
    position: relative;
    padding: 15px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    transition: all 0.3s ease;
    height: 100%;
}

.d_pro_item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.c-img {
    position: relative;
    display: block;
    overflow: hidden;
}

.c-img img {
    width: 100%;
    height: 200px;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.d_pro_item:hover .c-img img {
    transform: scale(1.05);
}

.status {
    position: absolute;
    top: 10px;
    padding: 5px 10px;
    color: white;
    font-size: 12px;
    border-radius: 3px 0 0 3px;
}

.status--new {
    background-color: #28a745;
    left: 0;
}

.status--discount {
    background-color: #ff4444;
    right: 0;
}

.clc1 {
    color: #666;
    margin-bottom: 5px;
}

.hvnau:hover {
    color: #dbaf56;
}

.clnau {
    color: #dbaf56;
}

.fs15 {
    font-size: 15px;
}

.fs16 {
    font-size: 16px;
}

.fs18 {
    font-size: 18px;
}

.fw-bold {
    font-weight: bold;
}

.text-decoration-line-through {
    text-decoration: line-through;
    color: #999;
}

.marb-24 {
    margin-bottom: 24px;
}

/* Custom Checkbox Styles */
.custom-checkbox {
    position: relative;
    padding-left: 30px;
    cursor: pointer;
    color: #fff;
    margin-bottom: 10px;
}

.custom-checkbox input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 20px;
    width: 20px;
    background-color: rgba(255, 255, 255, 0.1);
    border: 1px solid #dbaf56;
    border-radius: 3px;
}

.custom-checkbox input:checked ~ .checkmark {
    background-color: #dbaf56;
}

.checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

.custom-checkbox input:checked ~ .checkmark:after {
    display: block;
}

.custom-checkbox .checkmark:after {
    left: 7px;
    top: 3px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

/* Price Range Slider Styles */
.price-range-wrapper {
    padding: 15px 0;
}

.price-input {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.price-input .field {
    display: flex;
    align-items: center;
    height: 45px;
    width: 100%;
    margin-right: 15px;
}

.field span {
    color: #fff;
    margin-right: 10px;
}

.field input {
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 4px;
    padding: 0 15px;
    color: #fff;
}

.separator {
    color: #fff;
    margin: 0 10px;
}

.slider {
    height: 5px;
    position: relative;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 5px;
}

.slider .progress {
    height: 100%;
    position: absolute;
    border-radius: 5px;
    background: #dbaf56;
}

.range-input {
    position: relative;
    margin-top: -5px;
}

.range-input input {
    position: absolute;
    width: 100%;
    height: 5px;
    top: -5px;
    background: none;
    pointer-events: none;
    -webkit-appearance: none;
}

.range-input input::-webkit-slider-thumb {
    height: 17px;
    width: 17px;
    border-radius: 50%;
    background: #dbaf56;
    pointer-events: auto;
    -webkit-appearance: none;
    cursor: pointer;
}

.range-input input::-moz-range-thumb {
    height: 17px;
    width: 17px;
    border: none;
    border-radius: 50%;
    background: #dbaf56;
    pointer-events: auto;
    -moz-appearance: none;
    cursor: pointer;
}

/* No Results Styles */
.no-results {
    color: #dbaf56;
}

/* Button Styles */
.btn-primary {
    background-color: #dbaf56;
    border-color: #dbaf56;
    color: #fff;
    font-weight: bold;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #c69c43;
    border-color: #c69c43;
}
</style>

<script>
$(document).ready(function() {
    const rangeInput = $(".range-input input");
    const priceInput = $(".price-input input");
    const range = $(".slider .progress");
    let priceGap = 100000000;

    // Initialize progress bar position
    let minVal = parseInt(rangeInput.first().val());
    let maxVal = parseInt(rangeInput.last().val());
    range.css({
        left: (minVal / 5000000000) * 100 + "%",
        right: 100 - (maxVal / 5000000000) * 100 + "%"
    });

    priceInput.each(function() {
        $(this).on("input", function(e) {
            let minPrice = parseInt($(".price-input .input-min").val()) || 0;
            let maxPrice = parseInt($(".price-input .input-max").val()) || 5000000000;

            if (minPrice < 0) minPrice = 0;
            if (maxPrice > 5000000000) maxPrice = 5000000000;
            if (minPrice > maxPrice - priceGap) {
                if ($(this).hasClass("input-min")) {
                    minPrice = maxPrice - priceGap;
                } else {
                    maxPrice = minPrice + priceGap;
                }
            }

            $(".price-input .input-min").val(minPrice);
            $(".price-input .input-max").val(maxPrice);
            $(".range-input .range-min").val(minPrice);
            $(".range-input .range-max").val(maxPrice);

            range.css({
                left: (minPrice / 5000000000) * 100 + "%",
                right: 100 - (maxPrice / 5000000000) * 100 + "%"
            });
        });
    });

    rangeInput.each(function() {
        $(this).on("input", function(e) {
            let minVal = parseInt(rangeInput.first().val());
            let maxVal = parseInt(rangeInput.last().val());

            if ((maxVal - minVal) < priceGap) {
                if ($(this).hasClass("range-min")) {
                    rangeInput.first().val(maxVal - priceGap);
                } else {
                    rangeInput.last().val(minVal + priceGap);
                }
            } else {
                $(".price-input .input-min").val(minVal);
                $(".price-input .input-max").val(maxVal);
                range.css({
                    left: (minVal / 5000000000) * 100 + "%",
                    right: 100 - (maxVal / 5000000000) * 100 + "%"
                });
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?>