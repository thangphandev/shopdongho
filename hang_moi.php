<?php
require_once 'connect.php';
session_start();
$connect = new Connect();
$newProducts = $connect->getNewProducts();

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
    

<main style="margin-top: 150px;">
    <section class="container py-4">
        <h2 class="main-title text-center mb-5 text-uppercase wow fadeInUp delay01">ĐỒNG HỒ MỚI NHẤT</h2>
        <p class="text-center mb-5">Những sản phẩm đồng hồ cao cấp mới nhất tại cửa hàng, được cập nhật trong 10 ngày gần đây.</p>
    </section>

    <section class="h_pro_cate py-5 bgfixed rela lazy" data-src="images/anh-nen-pp.jpg">
        <div class="d_box_slider rela wow fadeInDown delay03">
            <div class="container">
                <div class="row">
                    <?php if (!empty($newProducts)): foreach ($newProducts as $product): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                            <div class="d_pro_item text-center pro_mw">
                                <a href="chi_tiet_san_pham.php?id=<?php echo htmlspecialchars($product['idsanpham']); ?>"
                                    title="<?php echo htmlspecialchars($product['tensanpham'] ?? $product['tensp']); ?>"
                                    class="smooth c-img marb-24">
                                    <img class="contain"
                                        loading="lazy"
                                        src="<?php echo htmlspecialchars($product['path_anh_goc'] ?? $product['hinhanh'] ?? 'images/no-image.jpg'); ?>"
                                        alt="<?php echo htmlspecialchars($product['tensanpham'] ?? $product['tensp']); ?>"
                                        title="<?php echo htmlspecialchars($product['tensanpham'] ?? $product['tensp']); ?>">
                                    <div class="status status--new">New</div>
                                    <?php if (!empty($product['gia_giam'])): ?>
                                    <div class="status status--discount">-<?php echo number_format($product['gia_giam'], 0, ',', '.'); ?>đ</div>
                                    <?php endif; ?>
                                </a>
                                <!-- <p class="clc1 text-uppercase fs15">MSP: <?php echo htmlspecialchars($product['idsanpham']); ?></p> -->
                                <h3>
                                    <a href="chi_tiet_san_pham.php?id=<?php echo htmlspecialchars($product['idsanpham']); ?>"
                                        title="<?php echo htmlspecialchars($product['tensanpham'] ?? $product['tensp']); ?>"
                                        class="smooth hvnau fs15"><?php echo htmlspecialchars($product['tensanpham'] ?? $product['tensp']); ?></a>
                                </h3>
                                <?php if (($product['giaban'] ?? $product['gia']) > 0): ?>
                                    <?php if (!empty($product['gia_giam'])): ?>
                                        <p class="clnau fs16 text-decoration-line-through"><?php echo number_format($product['giaban'] ?? $product['gia'], 0, ',', '.'); ?>đ</p>
                                        <p class="clnau fs18 fw-bold"><?php echo number_format(($product['giaban'] ?? $product['gia']) - $product['gia_giam'], 0, ',', '.'); ?>đ</p>
                                    <?php else: ?>
                                        <p class="clnau fs16"><?php echo number_format($product['giaban'] ?? $product['gia'], 0, ',', '.'); ?> VNĐ</p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="clnau fs16">Liên hệ</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <div class="col-12 text-center">
                            <div class="alert alert-info">
                                Hiện tại không có sản phẩm mới nào. Vui lòng quay lại sau.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.status--new {
   
    color: white;
    position: absolute;
    top: 0;
    right: 0;
    padding: 5px 10px;
    border-radius: 3px 0 0 3px;
}

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
    padding: 15px;
    transition: all 0.3s ease;
    height: 100%;
}

.d_pro_item:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateY(-5px);
}

.d_pro_item .c-img {
    position: relative;
    display: block;
    height: 250px;
    overflow: hidden;
}

.d_pro_item img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.5s ease;
}

.d_pro_item:hover img {
    transform: scale(1.05);
}
</style>

<?php include 'footer.php'; ?>