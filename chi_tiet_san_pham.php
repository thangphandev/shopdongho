<?php
require_once 'connect.php';
session_start();
$connect = new connect();

// Get product ID from URL
$productId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$productId) {
    header('Location: index.php');
    exit;
}

// Get product details and images
$product = $connect->getProductDetails($productId);
$productImages = $connect->getAllProductImages($productId);

if (!$product) {
    header('Location: index.php');
    exit;
}

include 'header.php';
?>
<!DOCTYPE html>
<html itemscope="" itemtype="http://schema.org/WebPage" lang="vi">
<script src="js/jquery3.2.1.min.js" defer=""></script>
<script src="js/bootstrap.min.js" defer=""></script>
<script src="js/lazyload.min.js" defer=""></script>
<script src="js/wow.js" defer=""></script>
<script src="js/tiny-slider.js" defer=""></script>
<script src="js/script.js" defer=""></script>

<body class="scrollstyle1">
    <div class="main" style="padding-top:150px; ">
        <div class="breadcrumb-container">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="index.php">Trang chủ</a></li>
                    <li><a href="#"><?php echo htmlspecialchars($product['tendanhmuc']); ?></a></li>
                    <li class="active"><?php echo htmlspecialchars($product['tensanpham']); ?></li>
                </ul>
            </div>
        </div>

        <div class="product-detail">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-12 order-2 order-lg-1">
                        <div class="product-info">
                            <h1 class="lora text-uppercase clnau"><?php echo htmlspecialchars($product['tensanpham']); ?></h1>
                            <div class="product-meta">
                                <p class="mb-4 clc1 detail_summary d-flex flex-wrap">
                                    <span class="d-md-inline-block pr-3 d-block mb-md-0 mb-2">
                                        Mã SP: <?php echo htmlspecialchars($product['masanpham'] ?? $product['idsanpham']); ?>
                                    </span>
                                    <span class="d-inline-block px-3 bdl">
                                        Số lượng: <?php echo htmlspecialchars($product['soluong']); ?>
                                    </span>
                                </p>
                            </div>

                            <div class="product-description clc1 mb-3">
                                <?php echo nl2br(htmlspecialchars($product['mota'] ?? '')); ?>
                            </div>

                            <div class="price-box">
                            <?php if ($product['giaban'] > 0): ?>
                                    <?php if (!empty($product['gia_giam'])): ?>
                                        <p class="mbold fs24 clnau mb-2 text-decoration-line-through">
                                            Giá gốc: <?php echo number_format($product['giaban'], 0, ',', '.'); ?> VNĐ
                                        </p>
                                        <p class="mbold fs30 clnau mb-2">
                                            Giá khuyến mãi: <?php echo number_format($product['giaban'] - $product['gia_giam'], 0, ',', '.'); ?> VNĐ
                                        </p>
                                        <p class="fs18 text-danger mb-2">
                                            (Giảm: <?php echo number_format($product['gia_giam'], 0, ',', '.'); ?> VNĐ)
                                        </p>
                                    <?php else: ?>
                                        <p class="mbold fs30 clnau mb-2">Giá: <?php echo number_format($product['giaban'], 0, ',', '.'); ?> VNĐ</p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="mbold fs30 clnau mb-2">Giá Boss Luxury: Liên hệ</p>
                                <?php endif; ?>
                                <p class="clc1 mb-2">
                                    (Giá trên website mang tính chất tham khảo có thể thay đổi theo thời điểm, quý khách
                                    vui lòng liên hệ HOTLINE Hà Nội: 0889.60.60.60 - Hoặc Sài Gòn: 0888.06.06.06 để được
                                    báo giá tốt nhất)
                                </p>
                            </div>

                            <div class="d_box_number mb-3">
                                <button class="minus" onclick="updateQuantity('minus')">-</button>
                                <input type="number" min="1" value="1" id="product-quantity" class="quantity" readonly>
                                <button class="plus" onclick="updateQuantity('plus')">+</button>
                            </div>
                                <div class="d-flex flex-wrap text-center box_detail_btn clwhite mb-3">
                                <a href="javascript:void(0)" onclick="muangay(<?= $product['idsanpham'] ?>)" 
                                class="d_detail_btn _cart_buynow d-inline-flex flex-column justify-content-center smooth marr40 bgcam h-100">
                                    <span class="fs15 mbold text-uppercase">Mua hàng</span>
                                </a>
                                <a href="javascript:void(0)" onclick="addToCart(<?= $product['idsanpham'] ?>)" class="d_detail_btn d-inline-flex flex-column justify-content-center smooth bgblue h-100">
                                    <span class="fs15 mbold text-uppercase">Thêm vào giỏ hàng</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-12 text-center order-1 order-lg-2 mb-4 mb-lg-0">
                        <div class="detail-images">
                            <div class="main-image">
                                <img src="<?php echo htmlspecialchars($product['image_path']); ?>"
                                    alt="<?php echo htmlspecialchars($product['tensanpham']); ?>"
                                    class="img-fluid">
                            </div>
                            <?php if ($productImages): ?>
                                <div class="thumbnail-images">
                                    <?php foreach ($productImages as $image): ?>
                                        <div class="thumb-item">
                                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>"
                                                alt="<?php echo htmlspecialchars($product['tensanpham']); ?>"
                                                class="img-fluid">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mt-5">
            <h3 class="fs22 mb-4 text-uppercase clnau wow fadeInDown">Thông số kỹ thuật</h3>
            <div class="row">
                <div class="col-12">
                    <div class="s-content fs16 clc1">
                        <p><span style="font-size: 12pt;">Bộ sưu tập: <?php echo htmlspecialchars($product['bosuutap']); ?></span></p>
                        <p><span style="font-size: 12pt;">Xuất xứ: <?php echo htmlspecialchars($product['xuatxu'] ?? 'Thụy Sỹ'); ?></span></p>
                        <p><span style="font-size: 12pt;">Dòng: <?php echo htmlspecialchars($product['tendanhmuc']); ?></span></p>
                        <p><span style="font-size: 12pt;">Loại máy: <?php echo htmlspecialchars($product['loaimay']); ?></span></p>
                        <p><span style="font-size: 12pt;">Kích thước: <?php echo htmlspecialchars($product['kichthuoc']); ?></span></p>
                        <p><span style="font-size: 12pt;">Độ dày: <?php echo htmlspecialchars($product['doday']); ?></span></p>
                        <p><span style="font-size: 12pt;">Mặt kính: <?php echo htmlspecialchars($product['matkinh']); ?></span></p>
                        <p><span style="font-size: 12pt;">Chất liệu vỏ: <?php echo htmlspecialchars($product['chatlieuvo']); ?></span></p>
                        <p><span style="font-size: 12pt;">Dây đeo: <?php echo htmlspecialchars($product['loaiday']); ?></span></p>
                        <p><span style="font-size: 12pt;">Màu sắc: <?php echo htmlspecialchars($product['mausac']); ?></span></p>
                        <p><span style="font-size: 12pt;">Chống nước: <?php echo htmlspecialchars($product['chongnuoc']); ?></span></p>
                        <p><span style="font-size: 12pt;">Tính năng đặc biệt: <?php echo htmlspecialchars($product['tinhnangdacbiet']); ?></span></p>
                        <p><span style="font-size: 12pt;">Chính sách bảo hành: <?php echo htmlspecialchars($product['chinhsachbaohanh']); ?></span></p>
                    </div>
                </div>
            </div>
            <h3 class="fs22 mb-4 text-uppercase clnau wow fadeInDown mt-5">Đánh giá chi tiết</h3>
            <div class="s-content clc1 fs16">
                <?php echo nl2br(htmlspecialchars($product['mota'] ?? '')); ?>
            </div>

            <h3 class="fs22 mb-4 text-uppercase clnau wow fadeInDown mt-5">Tại sao chọn Boss Luxury</h3>
                                                
            <?php
            $relatedProducts = $connect->getRelatedProducts($product['iddanhmuc'], $product['idsanpham'], 8);            
            if (!empty($relatedProducts)):
            ?>
            <section class="h_pro_cate py-5 bgfixed rela lazy" data-src="images/anh-nen-richard-mille.jpg">
                <h2 class="main-title text-center mb-5 text-uppercase wow fadeInUp delay01">
                    <a href="danh-muc.php?id=<?php echo htmlspecialchars($product['iddanhmuc']); ?>" 
                       class="smooth hvf3">Sản phẩm cùng danh mục</a>
                </h2>
                <div class="d_box_slider rela wow fadeInDown delay03">
                    <div class="container">
                        <div class="slick_spacing arrow_custom text-center tiny-slider" 
                             data-axis="horizontal" 
                             data-controls="false" 
                             data-slide_by="page" 
                             data-lazyload="true" 
                             data-items="1" 
                             data-mouse_drag="true" 
                             data-autoplay="true" 
                             data-autoplay_button_output="false" 
                             data-speed="400" 
                             data-nav="false" 
                             data-responsive='{"1":{"items":"2"},"480":{"items":"2"},"768":{"items":"2"},"991":{"items":"4"},"1900":{"items":"4"}}'>
                            <?php foreach ($relatedProducts as $relatedProduct): ?>
                                <div class="d_pro_item text-center pro_mw">
                                    <a href="chi_tiet_san_pham.php?id=<?php echo htmlspecialchars($relatedProduct['idsanpham']); ?>"
                                        title="<?php echo htmlspecialchars($relatedProduct['tensanpham']); ?>"
                                        class="smooth c-img marb-24">
                                        <img class="contain tns-lazy-img"
                                            loading="lazy"
                                            data-src="<?php echo htmlspecialchars($relatedProduct['path_anh_goc']); ?>"
                                            alt="<?php echo htmlspecialchars($relatedProduct['tensanpham']); ?>"
                                            title="<?php echo htmlspecialchars($relatedProduct['tensanpham']); ?>">
                                    </a>
                                    <p class="clc1 text-uppercase fs15">MSP: <?php echo htmlspecialchars($relatedProduct['idsanpham']); ?></p>
                                    <h3>
                                        <a href="chi_tiet_san_pham.php?id=<?php echo htmlspecialchars($relatedProduct['idsanpham']); ?>"
                                            title="<?php echo htmlspecialchars($relatedProduct['tensanpham']); ?>"
                                            class="smooth hvnau fs15"><?php echo htmlspecialchars($relatedProduct['tensanpham']); ?></a>
                                    </h3>
                                    <?php if ($relatedProduct['giaban'] > 0): ?>
                                        <p class="clnau fs16"><?php echo number_format($relatedProduct['giaban'], 0, ',', '.'); ?> VNĐ</p>
                                    <?php else: ?>
                                        <p class="clnau fs16">Liên hệ</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <section class="h_pro_cate py-5 bgfixed rela lazy" data-src="images/anh-nen-richard-mille.jpg">
            <h2 class="main-title text-center mb-5 text-uppercase wow fadeInUp delay01">
                <a href="https://localhost:8088/savewweb/san-pham-co-san" title="ĐỒNG HỒ CÓ SẴN" class="smooth hvf3">Các sản phẩm tương tự</a>
            </h2>
            <div class="d_box_slider rela wow fadeInDown delay03">
                <div class="container">
                    <div class="slick_spacing arrow_custom text-center tiny-slider" data-axis="horizontal" data-controls="false" data-slide_by="page" data-lazyload="true" data-items="1" data-mouse_drag="true" data-autoplay="true" data-autoplay_button_output="false" data-speed="400" data-nav="false" data-responsive='{"1":{"items":"2"},"480":{"items":"2"},"768":{"items":"2"},"991":{"items":"4"},"1900":{"items":"4"}}'>
                        <?php  $relatedProducts = $connect->getRelatedProducts($product['iddanhmuc'], $product['idsanpham'], 8);  
                        if (!empty($relatedProducts)): foreach ($relatedProducts as $product): ?>
                                <div class="d_pro_item text-center pro_mw">
                                    <a href="chi_tiet_san_pham.php?id=<?php echo htmlspecialchars($product['idsanpham']); ?>"
                                        title="<?php echo htmlspecialchars($product['tensanpham']); ?>"
                                        class="smooth c-img marb-24">
                                        <img class="contain tns-lazy-img"
                                            loading="lazy"
                                            data-src="<?php echo htmlspecialchars($product['path_anh_goc'] ?? 'images/no-image.jpg'); ?>"
                                            alt="<?php echo htmlspecialchars($product['tensanpham']); ?>"
                                            title="<?php echo htmlspecialchars($product['tensanpham']); ?>">
                                        
                                    </a>
                                    <p class="clc1 text-uppercase fs15">MSP: <?php echo htmlspecialchars($product['idsanpham']); ?></p>
                                    <h3>
                                        <a href="chi_tiet_san_pham.php?id=<?php echo htmlspecialchars($product['idsanpham']); ?>"
                                            title="<?php echo htmlspecialchars($product['tensanpham']); ?>"
                                            class="smooth hvnau fs15"><?php echo htmlspecialchars($product['tensanpham']); ?></a>
                                    </h3>
                                    <?php if ($product['giaban'] > 0): ?>
                                        <p class="clnau fs16"><?php echo number_format($product['giaban'], 0, ',', '.'); ?> VNĐ</p>
                                    <?php else: ?>
                                        <p class="clnau fs16">Liên hệ</p>
                                    <?php endif; ?>
                                </div>
                        <?php endforeach;
                        endif; ?>
                    </div>
                </div>
            </div>
        </section>

        </div>

    </div>
</body>


<?php include 'footer.php'; ?>

<style>
    .text-decoration-line-through {
    text-decoration: line-through;
}
    
    .d_box_number {
        display: flex;
        align-items: left;
        justify-content: left;
        gap: 5px;
    }

    .d_box_number button {
        width: 30px;
        height: 36px;
        background: #fff;
        border: 1px solid #ebebeb;
        cursor: pointer;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .d_box_number button:hover {
        background: #f5f5f5;
    }

    .d_box_number input[type="number"] {
        width: 60px;
        height: 36px;
        text-align: center;
        border: 1px solid #ebebeb;
        background: #fff;
        font-size: 14px;
        font-weight: 500;
        padding: 0 5px;
    }

    .d_box_number input[type="number"]::-webkit-inner-spin-button,
    .d_box_number input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .d_box_number input[type="number"] {
        -moz-appearance: textfield;
    }


    .product-detail {
        padding: 40px 0;
    }

    .breadcrumb {
        background: none;
        padding: 20px 0;
    }

    .main-image {
        margin-bottom: 20px;
    }

    .main-image img {
        width: 100%;
        height: auto;
    }

    .thumbnail-images {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .thumb-item {
        width: 80px;
        cursor: pointer;
    }


    .product-info h1 {
        font-size: 24px;
        margin-bottom: 20px;
        color: #c8a96a;
        font-weight: bold;
    }

    .product-meta {
        margin-bottom: 20px;
    }

    .price-box {
        margin: 30px 0;
    }

    .price {
        font-size: 20px;
        color: #c8a96a;
        font-weight: bold;
    }

    .contact-info {
        font-size: 14px;
        color: #666;
        margin-top: 10px;
    }

    .product-actions {
        display: flex;
        gap: 20px;
        margin-top: 30px;
    }

    .btn-buy,
    .btn-contact {
        padding: 12px 30px;
        text-transform: uppercase;
        font-weight: bold;
        border-radius: 5px;
    }

    .btn-buy {
        background: #c8a96a;
        color: white;
    }

    .btn-contact {
        background: #007bff;
        color: white;
    }


    .nav-tabs {
        border-bottom: 2px solid #c8a96a;
    }

    .nav-tabs>li.active>a {
        color: #c8a96a;
        border-bottom: 2px solid #c8a96a;
    }

    .tab-content {
        padding: 30px 0;
    }

    .product-tabs {
        margin-top: 50px;
    }

    .nav-tabs {
        border-bottom: 2px solid #c8a96a;
        margin-bottom: 20px;
    }

    .quantity-selector {
        margin: 20px 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .qty-btn {
        padding: 5px 12px;
        background: #f5f5f5;
        border: none;
        cursor: pointer;
    }

    #quantity {
        width: 60px;
        text-align: center;
        border: none;
        border-left: 1px solid #ddd;
        border-right: 1px solid #ddd;
        padding: 5px;
    }

    .thumbnail-images {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .thumb-item {
        width: 100px;
        height: 100px;
        overflow: hidden;
    }

    .thumb-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .main-image {
        margin-bottom: 20px;
        width: 100%;
        height: 500px;
        /* Fixed height for main image */
        overflow: hidden;
    }

    .main-image img {
        width: 100%;
        height: 100%;
    }

    .thumbnail-images {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        flex-wrap: wrap;
        /* Allow wrapping for multiple thumbnails */
    }

    .thumb-item {
        width: 120px;
        height: 120px;
        overflow: hidden;
        cursor: pointer;
        transition: border-color 0.3s;
    }

    .thumb-item:hover {
        border-color: #c8a96a;
    }

    .thumb-item img {
        width: 100%;
        height: 100%;

    }

    .main {
        background-image: url('images/anh-nen-pp.jpg');
        /* Update path to your background image */
        background-repeat: repeat;
        background-position: center;
        position: relative;
    }

    .main::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 0;
    }

    .main>* {
        position: relative;
        z-index: 1;
    }
</style>


<script>
    document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelector('.banner-slider .tiny-slider')) {
                var bannerSlider = tns({
                    container: '.banner-slider .tiny-slider',
                    items: 1,
                    slideBy: 'page',
                    autoplay: true,
                    autoplayButtonOutput: false,
                    speed: 400,
                    nav: true,
                    controlsText: [
                        '<i class="fa fa-angle-left" aria-hidden="true"></i>',
                        '<i class="fa fa-angle-right" aria-hidden="true"></i>'
                    ],
                    lazyload: true,
                    mouseDrag: true,
                    responsive: {
                        0: {
                            items: 1
                        }
                    }
                });
            }
        });



    function muangay(productId) {
        const quantity = document.getElementById('product-quantity').value;
        window.location.href = `thanhtoan.php?type=direct&productId=${productId}&quantity=${quantity}`;
    }



    document.addEventListener('DOMContentLoaded', function() {
        // Thumbnail click handler
        const thumbItems = document.querySelectorAll('.thumb-item img');
        const mainImage = document.querySelector('.main-image img');

        thumbItems.forEach(item => {
            item.addEventListener('click', function() {
                mainImage.src = this.src;
            });
        });
    });
    $(document).ready(function() {
        // Initialize Bootstrap tabs
        $('a[data-toggle="tab"]').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        // Existing thumbnail click handler...

        // Quantity controls
        const quantityInput = document.getElementById('quantity');
        const minusBtn = document.querySelector('.qty-btn.minus');
        const plusBtn = document.querySelector('.qty-btn.plus');

        if (minusBtn && plusBtn && quantityInput) {
            minusBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });

            plusBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                const maxValue = parseInt(quantityInput.getAttribute('max'));
                if (currentValue < maxValue) {
                    quantityInput.value = currentValue + 1;
                }
            });

            quantityInput.addEventListener('change', function() {
                const value = parseInt(this.value);
                const max = parseInt(this.getAttribute('max'));
                if (value < 1) this.value = 1;
                if (value > max) this.value = max;
            });
        }
    });

    function updateQuantity(action) {
        const quantityInput = document.getElementById('product-quantity');
        let currentQuantity = parseInt(quantityInput.value) || 1;

        if (action === 'plus') {
            currentQuantity++;
        } else if (action === 'minus' && currentQuantity > 1) {
            currentQuantity--;
        }

        quantityInput.value = currentQuantity;
    }

    function addToCart(productId) {
        const quantity = parseInt(document.getElementById('product-quantity').value) || 1;

        $.ajax({
            url: 'update_gio_hang.php',
            type: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                action: 'add'
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    // Update cart count immediately
                    $.ajax({
                        url: 'lay_so_luong_san_pham.php',
                        type: 'GET',
                        success: function(countResponse) {
                            const cartData = JSON.parse(countResponse);
                            $('.cart-count').text(cartData.count);
                        }
                    });
                    
                    Swal.fire({
                        position: "top-end",
                        icon: "success",
                        title: "Thêm sản phẩm thành công",
                        showConfirmButton: false,
                        timer: 1200
                    });
                } else {
                    alert(data.message || 'Thêm vào giỏ hàng thất bại');
                }
            }
        });
    }
</script>