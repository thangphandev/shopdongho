<?php
require_once 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$connect = new Connect();
$orders = $connect->getUserOrders($_SESSION['user_id']);

include 'header.php';
?>

<!DOCTYPE html>
<html lang="vi">
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
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<body>
    <div class="main-content" style="padding-top: 150px;">
        <div class="container">
            <h1 class="fs36 clnau line_tt text-uppercase lora text-center mb-4">Đơn hàng của tôi</h1>
            
            <?php if ($orders): ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card mb-4">
                        <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h5 class="mb-0">Đơn hàng #<?= $order['iddonhang'] ?></h5>
                                    <small class="text-muted">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['ngaydat'])) ?></small>
                                </div>
                                <div class="order-status">
                                    <span class="badge <?= getStatusClass($order['trangthai']) ?>">
                                        <?= $order['trangthai'] ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if ($order['trangthai'] !== 'Đã hủy'): ?>
                                <div class="order-progress">
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar <?= getProgressBarClass($order['trangthai']) ?>" 
                                            role="progressbar" 
                                            style="width: <?= getOrderProgress($order['trangthai']) ?>%"
                                            aria-valuenow="<?= getOrderProgress($order['trangthai']) ?>" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                            <?= $order['trangthai'] ?>
                                        </div>
                                    </div>
                                    <div class="progress-steps mt-1">
                                        <div class="step <?= getOrderProgress($order['trangthai']) >= 20 ? 'active' : '' ?>">
                                            <div class="step-label">Chờ xác nhận</div>
                                        </div>
                                        <div class="step <?= getOrderProgress($order['trangthai']) >= 40 ? 'active' : '' ?>">
                                            <div class="step-label">Đã xác nhận</div>
                                        </div>
                                        
                                        <div class="step <?= getOrderProgress($order['trangthai']) >= 80 ? 'active' : '' ?>">
                                            <div class="step-label">Đang vận chuyển</div>
                                        </div>
                                        <div class="step <?= getOrderProgress($order['trangthai']) >= 100 ? 'active' : '' ?>">
                                            <div class="step-label">Hoàn thành</div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="order-cancelled">
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-danger" 
                                            role="progressbar" 
                                            style="width: 100%">
                                            Đơn hàng đã bị hủy
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                            
                            <div class="card-body">
                                <div class="order-info mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Người nhận:</strong> <?= htmlspecialchars($order['tennguoidat']) ?></p>
                                            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['sdt']) ?></p>
                                            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['diachigiao']) ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Phương thức thanh toán:</strong> <?= htmlspecialchars($order['phuongthuctt']) ?></p>
                                            <p><strong>Trạng thái thanh toán:</strong> <?= htmlspecialchars($order['trangthai_thanhtoan']) ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="order-items">
                                    <?php
                                    $orderDetails = $connect->getOrderDetails($order['iddonhang']);
                                    $subtotal = 0;
                                    foreach ($orderDetails as $item):
                                        $itemTotal = $item['giaban'] * $item['soluong'];
                                        $subtotal += $itemTotal;
                                    ?>
                                        <div class="order-item mb-3">
                                            <div class="row align-items-center">
                                                <div class="col-2">
                                                <?php if (!empty($item['path_anh_goc'])): ?>
                                                    <a href="chi_tiet_san_pham.php?id=<?= $item['idsanpham'] ?>">
                                                        <img src="<?= htmlspecialchars($item['path_anh_goc']) ?>" 
                                                            class="img-fluid rounded" 
                                                            alt="<?= htmlspecialchars($item['tensanpham']) ?>">
                                                    </a>
                                                <?php else: ?>
                                                    <img src="images/no-image.jpg" 
                                                        class="img-fluid rounded" 
                                                        alt="No Image">
                                                <?php endif; ?>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="text-white mb-1"><?= htmlspecialchars($item['tensanpham']) ?></h6>
                                                    <small class="text-warning">Mã SP: <?= htmlspecialchars($item['idsanpham']) ?></small>
                                                    <div class="product-specs">
                                                        <small class="text-light">
                                                            <?= !empty($item['loaimay']) ? "Loại máy: " . htmlspecialchars($item['ten_loai_may']) . "<br>" : "" ?>
                                                            <?= !empty($item['mausac']) ? "Màu: " . htmlspecialchars($item['mausac']) . "<br>" : "" ?>
                                                            <?= !empty($item['kichthuoc']) ? "Kích thước: " . htmlspecialchars($item['kichthuoc']) : "" ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="col-2 text-center">
                                                    <div class="quantity-box">
                                                        <span class="text-white">Số lượng:</span>
                                                        <p class="mb-0 text-warning fw-bold"><?= $item['soluong'] ?></p>
                                                    </div>
                                                </div>
                                                <div class="col-2 text-center">
                                                    <div class="price-box">
                                                        <span class="text-white">Đơn giá:</span>
                                                        <p class="mb-0 text-warning"><?= number_format($item['giaban'], 0, ',', '.') ?>đ</p>
                                                    </div>
                                                </div>
                                                <div class="col-2 text-end">
                                                    <div class="total-box">
                                                        <span class="text-white">Thành tiền:</span>
                                                        <p class="mb-0 text-warning fw-bold"><?= number_format($itemTotal, 0, ',', '.') ?>đ</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <div class="order-summary mt-4 p-3 bg-dark rounded">
                                        <div class="row">
                                            <div class="col-md-6 offset-md-6">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-white">Tổng tiền hàng:</span>
                                                    <span class="text-warning"><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-white">Phí vận chuyển:</span>
                                                    <span class="text-warning"><?= number_format($order['tongtien'], 0, ',', '.') ?>đ</span>
                                                </div>
                                                <div class="d-flex justify-content-between pt-2 border-top border-secondary">
                                                    <strong class="text-white">Tổng thanh toán:</strong>
                                                    <strong class="text-warning fs-5"><?= number_format($order['tongtien'], 0, ',', '.') ?>đ</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

<style>
    .progress-steps {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    position: relative;
    padding: 0 10px;
}
.text-muted{
    color: #fff!important;
}

.step {
    text-align: center;
    position: relative;
    flex: 1;
}

.step::before {
    content: '';
    width: 20px;
    height: 20px;
    background: #ddd;
    border-radius: 50%;
    display: block;
    margin: 0 auto 5px;
}

.step.active::before {
    background: #4c98eb;
}

.step-label {
    font-size: 1.5 rem;
    color: #fff;
    white-space: nowrap;
}

.step.active .step-label {
    color: #4c98eb;
}

.progress {
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.1);
}

.progress-bar {
    border-radius: 15px;
    transition: width 0.5s ease-in-out;
}

.order-cancelled .progress-bar {
    background-color: #dc3545 !important;
}

.card-header {
    padding: 1rem;
    background: rgba(0, 0, 0, 0.2);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.order-item {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    margin-bottom: 15px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.quantity-box, .price-box, .total-box {
    background: rgba(0, 0, 0, 0.2);
    padding: 8px;
    border-radius: 6px;
}

.product-specs {
    margin-top: 8px;
    padding: 8px;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 6px;
}

.order-summary {
    background: rgba(0, 0, 0, 0.3) !important;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.text-warning {
    color: #ffd700 !important;
}

.order-item img {
    width: 100%;
    max-width: 100px;
    height: auto;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.1);
}

.fw-bold {
    font-weight: 600 !important;
}

.fs-5 {
    font-size: 1.25rem !important;
}
</style>
                                
                                <div class="order-footer d-flex justify-content-between align-items-center mt-3">
                                    <div class="order-total">
                                        <strong>Tổng tiền: <?= number_format($order['tongtien'], 0, ',', '.') ?>đ</strong>
                                    </div>
                                    <?php if (in_array($order['trangthai'], ['Chờ xác nhận', 'Đã xác nhận'])): ?>
                                        <button class="btn btn-danger" onclick="cancelOrder(<?= $order['iddonhang'] ?>)">
                                            Hủy đơn hàng
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center">
                    <p>Bạn chưa có đơn hàng nào.</p>
                    <a href="index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
    function cancelOrder(orderId) {
    Swal.fire({
        title: "Hủy đơn hàng?",
        text: "Bạn có chắc muốn hủy đơn hàng này?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Hủy đơn hàng",
        cancelButtonText: "Đóng"
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Thành công!",
                text: "Đơn hàng đã được hủy thành công",
                icon: "success",
                timer: 1500,
                showConfirmButton: false
            });

            setTimeout(() => {
                $.ajax({
                    url: 'huy_don_hang.php',
                    type: 'POST',
                    data: { orderId: orderId },
                    success: function(response) {
                        location.reload();
                    },
                    error: function() {
                        Swal.fire({
                            title: "Lỗi!",
                            text: "Có lỗi xảy ra khi hủy đơn hàng",
                            icon: "error"
                        });
                    }
                });
            }, 1500);
        }
    });
}
    </script>

<style>
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
    h6{
        font-size: 1.5rem;
    }
    h5 {
        font-size: 1.5rem;
    }
   

    .card {
        background: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-header {
        background: rgba(0, 0, 0, 0.2) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px 15px 0 0;
        padding: 20px;
    }

    .card-body {
        background: rgba(255, 255, 255, 0);
        padding: 20px;
        border-radius: 0 0 15px 15px;
    }

    .order-info {
        background: rgba(0, 0, 0, 0);
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #dbaf56;
        font-size: 1.05rem;
    }

    .order-item {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
        border-radius: 10px;
        margin-bottom: 15px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 15px;
        transition: transform 0.3s ease;
    }

    .order-item:hover {
        transform: translateX(5px);
    }

    .quantity-box, .price-box, .total-box {
        background: rgba(0, 0, 0, 0.3);
        padding: 10px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .product-specs {
        margin-top: 8px;
        padding: 10px;
        background: rgba(0, 0, 0, 0.3);
        border-radius: 8px;
        border: 1px solid #dbaf56
    }

    .order-summary {
        background: rgba(0, 0, 0, 0.4) !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }

    .progress {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 20px;
        transition: width 0.5s ease-in-out;
    }

    .badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 800;
        backdrop-filter: blur(5px);
    }
    .badge-shipping, .badge-pending, .badge-confirmed, .badge-preparing, .badge-shipping, .badge-completed, .badge-cancelled {
        color: #fff;
        font-size: 1rem;
    }

    .badge-pending { background-color: rgba(255, 193, 7, 0.8); }
    .badge-confirmed { background-color: rgba(23, 162, 184, 0.8); }
    .badge-preparing { background-color: rgba(111, 66, 193, 0.8); }
    .badge-shipping { background-color: rgba(0, 110, 255, 0.8); }
    .badge-completed { background-color: rgba(40, 167, 69, 0.8); }
    .badge-cancelled { background-color: rgba(220, 53, 69, 0.8); }

    .order-item img {
        width: 100%;
        max-width: 100px;
        height: auto;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        transition: transform 0.3s ease;
    }

    .order-item img:hover {
        transform: scale(1.05);
    }

    .btn-danger {
        background: rgba(220, 53, 69, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 8px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-danger:hover {
        background: rgba(200, 35, 51, 0.9);
        transform: translateY(-2px);
    }

    .text-white {
        text-shadow: 1px 1px 2px rgb(0, 0, 0);
    }

    .text-warning {
        color:rgb(250, 225, 85) !important;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }



    .empty-orders {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 30px;
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .empty-orders p {
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 20px;
    }

    .btn-primary {
        background: rgba(0, 123, 255, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 10px 25px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: rgba(0, 98, 204, 0.9);
        transform: translateY(-2px);
    }
</style>
</body>
</html>

<?php
function getStatusClass($status) {
    switch ($status) {
        case 'Chờ xác nhận': return 'badge-pending';
        case 'Đã xác nhận': return 'badge-confirmed';
        
        case 'Đang vận chuyển': return 'badge-shipping';
        case 'Hoàn thành': return 'badge-completed';
        case 'Đã hủy': return 'badge-cancelled';
        default: return 'badge-secondary';
    }
}   
function getOrderProgress($status) {
    $stages = [
        'Chờ xác nhận' => 25,
        'Đã xác nhận' => 50,
        
        'Đang vận chuyển' => 75,
        'Hoàn thành' => 100,
        'Đã hủy' => 0
    ];
    return $stages[$status] ?? 0;
}

function getProgressBarClass($status) {
    if ($status === 'Đã hủy') return 'bg-danger';
    if ($status === 'Hoàn thành') return 'bg-success';
    return 'bg-primary';
}
?>