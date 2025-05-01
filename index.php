<?php
require_once 'connect.php';
session_start();
$connect = new connect();
$newProducts = $connect->getNewProducts(8); // Lấy 8 sản phẩm mới nhất
$inStockProducts = $connect->getProductsByStock();
$categories = $connect->getCategories(); // Get all categories

// Get products for each category
$categoryProducts = [];
foreach ($categories as $category) {
    $categoryProducts[$category['iddanhmuc']] = $connect->getProductsByCategory($category['iddanhmuc']);
}

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

<body class="scrollstyle1">

    <section class="banner-slider wow fadeIn" data-wow-duration="2s">
        <div class="slider-container">
            <div class="tiny-slider" data-axis="horizontal" data-controls="true" data-slide_by="page" data-items="1" data-mouse_drag="true" data-autoplay="true" data-autoplay_button_output="false" data-speed="500" data-nav="true" data-lazyload="true">
                <div class="item">
                    <img data-src="images/anh-slide-1.jpg" alt="Boss Luxury Watch Banner 1" class="img-responsive tns-lazy-img">
                    <div class="slider-caption">
                        <h2>Bộ Sưu Tập Đồng Hồ Cao Cấp</h2>
                        <p>Khám phá những mẫu đồng hồ đẳng cấp từ các thương hiệu hàng đầu thế giới</p>
                        <a href="san-pham" class="btn-slider">Khám phá ngay</a>
                    </div>
                </div>
                <div class="item">
                    <img data-src="images/anh-slide-3.jpg" alt="Boss Luxury Watch Banner 2" class="img-responsive tns-lazy-img">
                    <div class="slider-caption">
                        <h2>Đồng Hồ Chính Hãng 100%</h2>
                        <p>Cam kết về chất lượng và nguồn gốc xuất xứ rõ ràng</p>
                        <a href="hang_co_san.php" class="btn-slider">Xem bộ sưu tập</a>
                    </div>
                </div>
                <div class="item">
                    <img data-src="images/anh-slide-4.jpg" alt="Boss Luxury Watch Banner 3" class="img-responsive tns-lazy-img">
                    <div class="slider-caption">
                        <h2>Dịch Vụ Trao Đổi & Ký Gửi</h2>
                        <p>Giải pháp mua bán, trao đổi đồng hồ cao cấp uy tín hàng đầu Việt Nam</p>
                        <a href="dich-vu" class="btn-slider">Tìm hiểu thêm</a>
                    </div>
                </div>
                <div class="item">
                    <img data-src="images/anh-slide-1.jpg" alt="Boss Luxury Watch Banner 1" class="img-responsive tns-lazy-img">
                    <div class="slider-caption">
                        <h2>Bộ Sưu Tập Đồng Hồ Cao Cấp</h2>
                        <p>Khám phá những mẫu đồng hồ đẳng cấp từ các thương hiệu hàng đầu thế giới</p>
                        <a href="san-pham" class="btn-slider">Khám phá ngay</a>
                    </div>
                </div>
                <div class="item">
                    <img data-src="images/anh-slide-3.jpg" alt="Boss Luxury Watch Banner 2" class="img-responsive tns-lazy-img">
                    <div class="slider-caption">
                        <h2>Đồng Hồ Chính Hãng 100%</h2>
                        <p>Cam kết về chất lượng và nguồn gốc xuất xứ rõ ràng</p>
                        <a href="san-pham-co-san" class="btn-slider">Xem bộ sưu tập</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Phần sản phẩm có sẵn -->
    <section class="h_pro_cate py-5 bgfixed rela lazy" data-src="images/anh-nen-pp.jpg">
        <h2 class="main-title text-center mb-5 text-uppercase wow fadeInUp delay01">
            <a href="https://localhost:8088/savewweb/san-pham-moi" title="ĐỒNG HỒ MỚI NHẤT" class="smooth hvf3">ĐỒNG HỒ MỚI NHẤT</a>
        </h2>
        <div class="d_box_slider rela wow fadeInDown delay03">
            <div class="container">
                <div class="slick_spacing arrow_custom text-center tiny-slider" data-axis="horizontal" data-controls="fasle" data-slide_by="page" data-lazyload="true" data-items="1" data-mouse_drag="true" data-autoplay="true" data-autoplay_button_output="false" data-speed="400" data-nav="false" data-responsive='{"1":{"items":"2"},"480":{"items":"2"},"768":{"items":"2"},"991":{"items":"4"},"1900":{"items":"4"}}'>
                    <?php if ($newProducts): foreach ($newProducts as $product): ?>
                            <div class="d_pro_item text-center pro_mw">
                                <a href="chi_tiet_san_pham.php?id=<?php echo htmlspecialchars($product['idsanpham']); ?>"
                                    title="<?php echo htmlspecialchars($product['tensanpham']); ?>"
                                    class="smooth c-img marb-24">
                                    <img class="contain tns-lazy-img"
                                        loading="lazy"
                                        data-src="<?php echo htmlspecialchars($product['path_anh_goc'] ?? 'images/no-image.jpg'); ?>"
                                        alt="<?php echo htmlspecialchars($product['tensanpham']); ?>"
                                        title="<?php echo htmlspecialchars($product['tensanpham']); ?>">
                                    <div class="status status--new">New</div>
                                    <?php if (!empty($product['gia_giam'])): ?>
                                        <div class="status status--discount">-<?php echo number_format($product['gia_giam'], 0, ',', '.'); ?>đ</div>
                                    <?php endif; ?>
                                </a>
                                <p class="clc1 text-uppercase fs15">MSP: <?php echo htmlspecialchars($product['idsanpham']); ?></p>
                                <h3>
                                    <a href="chi_tiet_san_pham.php?id=<?php echo htmlspecialchars($product['idsanpham']); ?>"
                                        title="<?php echo htmlspecialchars($product['tensanpham']); ?>"
                                        class="smooth hvnau fs15"><?php echo htmlspecialchars($product['tensanpham']); ?></a>
                                </h3>
                                <?php if ($product['giaban'] > 0): ?>
                                    <?php if (!empty($product['gia_giam'])): ?>
                                        <p class="clnau fs16 text-decoration-line-through"><?php echo number_format($product['giaban'], 0, ',', '.'); ?>đ</p>
                                        <p class="clnau fs18 fw-bold"><?php echo number_format($product['giaban'] - $product['gia_giam'], 0, ',', '.'); ?>đ</p>
                                    <?php else: ?>
                                        <p class="clnau fs16"><?php echo number_format($product['giaban'], 0, ',', '.'); ?>đ</p>
                                    <?php endif; ?>
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
    <?php if (!empty($inStockProducts)): ?>
        <section class="h_pro_cate py-5 bgfixed rela lazy" data-src="images/anh-nen-richard-mille.jpg">
            <h2 class="main-title text-center mb-5 text-uppercase wow fadeInUp delay01">
                <a href="hang_co_san.php" title="ĐỒNG HỒ CÓ SẴN" class="smooth hvf3">ĐỒNG HỒ CÓ SẴN</a>
            </h2>
            <div class="d_box_slider rela wow fadeInDown delay03">
                <div class="container">
                    <div class="slick_spacing arrow_custom text-center tiny-slider" data-axis="horizontal" data-controls="false" data-slide_by="page" data-lazyload="true" data-items="1" data-mouse_drag="true" data-autoplay="true" data-autoplay_button_output="false" data-speed="400" data-nav="false" data-responsive='{"1":{"items":"2"},"480":{"items":"2"},"768":{"items":"2"},"991":{"items":"4"},"1900":{"items":"4"}}'>
                        <?php if ($inStockProducts): foreach ($inStockProducts as $product): ?>
                                <div class="d_pro_item text-center pro_mw">
                                    <a href="chi_tiet_san_pham.php?id=<?php echo htmlspecialchars($product['idsanpham']); ?>"
                                        title="<?php echo htmlspecialchars($product['tensanpham']); ?>"
                                        class="smooth c-img marb-24">
                                        <img class="contain tns-lazy-img"
                                            loading="lazy"
                                            data-src="<?php echo htmlspecialchars($product['path_anh_goc'] ?? 'images/no-image.jpg'); ?>"
                                            alt="<?php echo htmlspecialchars($product['tensanpham']); ?>"
                                            title="<?php echo htmlspecialchars($product['tensanpham']); ?>">
                                        <div class="status status--available">Có sẵn</div>
                                        <?php if (!empty($product['gia_giam'])): ?>
                                        <div class="status status--discount">-<?php echo number_format($product['gia_giam'], 0, ',', '.'); ?>đ</div>
                                    <?php endif; ?>
                                    </a>
                                    <p class="clc1 text-uppercase fs15">MSP: <?php echo htmlspecialchars($product['idsanpham']); ?></p>
                                    <h3>
                                        <a href="chi_tiet_san_pham.php?id=<?php echo htmlspecialchars($product['idsanpham']); ?>"
                                            title="<?php echo htmlspecialchars($product['tensanpham']); ?>"
                                            class="smooth hvnau fs15"><?php echo htmlspecialchars($product['tensanpham']); ?></a>
                                    </h3>
                                    <?php if ($product['giaban'] > 0): ?>
                                        <?php if (!empty($product['gia_giam'])): ?>
                                            <p class="clnau fs16 text-decoration-line-through"><?php echo number_format($product['giaban'], 0, ',', '.'); ?>đ</p>
                                            <p class="clnau fs18 fw-bold"><?php echo number_format($product['giaban'] - $product['gia_giam'], 0, ',', '.'); ?>đ</p>
                                        <?php else: ?>
                                            <p class="clnau fs16"><?php echo number_format($product['giaban'], 0, ',', '.'); ?>đ</p>
                                        <?php endif; ?>
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
    <?php endif; ?>
    <?php foreach ($categories as $category):
        $products = $categoryProducts[$category['iddanhmuc']];
        if (!empty($products)):
    ?>
            <section class="h_pro_cate py-5 bgfixed rela lazy" data-src="images/anh-nen-richard-mille.jpg">
                <h2 class="main-title text-center mb-5 text-uppercase wow fadeInUp delay01">
                    <a href="danh-muc/<?php echo htmlspecialchars($category['iddanhmuc']); ?>"
                        title="<?php echo htmlspecialchars($category['tendanhmuc']); ?>"
                        class="smooth hvf3"><?php echo htmlspecialchars($category['tendanhmuc']); ?></a>
                </h2>
                <div class="d_box_slider rela wow fadeInDown delay03">
                    <div class="container">
                        <div class="slick_spacing arrow_custom text-center tiny-slider" data-axis="horizontal" data-controls="false" data-slide_by="page" data-lazyload="true" data-items="1" data-mouse_drag="true" data-autoplay="true" data-autoplay_button_output="false" data-speed="400" data-nav="false" data-responsive='{"1":{"items":"2"},"480":{"items":"2"},"768":{"items":"2"},"991":{"items":"4"},"1900":{"items":"4"}}'>
                            <?php foreach ($products as $product): ?>
                                <div class="d_pro_item text-center pro_mw">
                                    <a href="chi_tiet_san_pham.php?id=<?php echo htmlspecialchars($product['idsanpham']); ?>"
                                        title="<?php echo htmlspecialchars($product['tensanpham']); ?>"
                                        class="smooth c-img marb-24">
                                        <img class="contain tns-lazy-img"
                                            loading="lazy"
                                            data-src="<?php echo htmlspecialchars($product['path_anh_goc'] ?? 'images/no-image.jpg'); ?>"
                                            alt="<?php echo htmlspecialchars($product['tensanpham']); ?>"
                                            title="<?php echo htmlspecialchars($product['tensanpham']); ?>">
                                            <?php if (!empty($product['gia_giam'])): ?>
                                                <div class="status status--discount">-<?php echo number_format($product['gia_giam'], 0, ',', '.'); ?>đ</div>
                                            <?php endif; ?>
                                    </a>
                                    <p class="clc1 text-uppercase fs15">MSP: <?php echo htmlspecialchars($product['idsanpham']); ?></p>
                                    <h3>
                                        <a href="chi_tiet_san_pham.php?id=<?php echo htmlspecialchars($product['idsanpham']); ?>"
                                            title="<?php echo htmlspecialchars($product['tensanpham']); ?>"
                                            class="smooth hvnau fs15"><?php echo htmlspecialchars($product['tensanpham']); ?></a>
                                    </h3>
                                    <?php if ($product['giaban'] > 0): ?>
                                        <?php if (!empty($product['gia_giam'])): ?>
                                            <p class="clnau fs16 text-decoration-line-through"><?php echo number_format($product['giaban'], 0, ',', '.'); ?>đ</p>
                                            <p class="clnau fs18 fw-bold"><?php echo number_format($product['giaban'] - $product['gia_giam'], 0, ',', '.'); ?>đ</p>
                                        <?php else: ?>
                                            <p class="clnau fs16"><?php echo number_format($product['giaban'], 0, ',', '.'); ?>đ</p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="clnau fs16">Liên hệ</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </section>
    <?php endif;
    endforeach; ?>



    <!-- Phần tin tức -->
    <section class="h-news pt-1 bg10 pb-md-0 pb-4">
        <p class="main-title text-center my-5 text-uppercase wow fadeInDown">Tin tức mới</p>
        <div class="news_and_events rela zindex1 wow slideInUp delay03">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-12">
                        <div class="news_cas single-item tiny-slider" id="pro-img" data-axis="horizontal" data-controls="true" data-nav_position="bottom" data-nav_as_thumbnails="false" data-slide_by="page" data-lazyload="true" data-items="1" data-mouse_drag="true" data-autoplay="true" data-autoplay_button_output="false" data-speed="400" data-nav="true">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer và các phần khác giữ nguyên hoặc thêm dữ liệu động nếu cần -->
    <!-- <footer class="bg10 py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-lg-6 col-md-6 col-12 order-1 mb-4 wow fadeInUp">
                    <span class="fs18 lora clnau text-uppercase">Hộ Kinh Doanh Nguyễn Quốc Thành</span>
                    <p>MSHKD: 8401387172-001 do UBND Quận Hoàn Kiếm cấp ngày 16 tháng 08 năm 2024</p>
                    <p><i class="fa fa-map-marker"></i> Hà Nội: 60 Ngô Quyền, P. Hàng Bài, Q. Hoàn Kiếm, TP. Hà Nội</p>
                    <p>Hotline Tư Vấn: <a href="tel:0889.60.60.60" rel="nofollow" title="0889.60.60.60" class="smooth hvnau">0889.60.60.60</a></p>
                    <p>Email: <a href="mailto:bossluxury.vn@gmail.com" rel="nofollow" title="bossluxury.vn@gmail.com" class="smooth hvnau">bossluxury.vn@gmail.com</a></p>
                </div>
               
            </div>
        </div>
    </footer> -->
    <?php
    // Include footer
    include 'footer.php';
    ?>

    <!-- Scripts -->
    
    <script>
        // Banner slider initialization
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
    </script>
    <script>
        $(document).ready(function() {
    console.log('Header script loaded');
    
    // Add click handler for search button
    $('.d_seach_btn').on('click', function(e) {
        e.preventDefault();
        $('.search-popup').fadeIn(300);
        $('body').css('overflow', 'hidden');
        $('.quick-search input').focus();
    });

    // Close popup handlers
    $('.close-search').on('click', function() {
        console.log('Close button clicked');
        $('.search-popup').fadeOut(300);
        $('body').css('overflow', '');
    });

    // Handle brand selection
    $('.col_brands a').on('click', function(e) {
        e.preventDefault();
        console.log('Brand selected:', $(this).data('brand'));
        $('.col_brands a').removeClass('active');
        $(this).addClass('active');
        $('#selected_brand').val($(this).data('brand'));
    });

    // Handle price range selection
    $('.col_price a').on('click', function(e) {
        e.preventDefault();
        console.log('Price range selected:', $(this).data('price'));
        $('.col_price a').removeClass('active');
        $(this).addClass('active');
        $('#selected_price').val($(this).data('price'));
    });

    
});
    </script>
</body>
<style>
    .status--discount {
    background-color: #ff4444;
    color: white;
    position: absolute;
    top: 30px;
    right: 0;
    padding: 5px 10px;
    border-radius: 3px 0 0 3px;
}

.text-decoration-line-through {
    text-decoration: line-through;
    color: #999;
}

.d_pro_item {
 
    .social-links {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .social-link {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #666;
        transition: color 0.3s ease;
        text-decoration: none;
    }

    .social-link:hover {
        color: #c8a96a;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 10px;
    }

    .footer-links a {
        color: #666;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-links a:hover {
        color: #c8a96a;
    }

    .footer-title {
        position: relative;
        padding-bottom: 10px;
    }

    .footer-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 2px;
        background-color: #c8a96a;
    }
}
</style>

</html>
