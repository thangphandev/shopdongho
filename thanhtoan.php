<?php
require_once 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$connect = new Connect();
$products = [];
$total = 0;

// Validate request type
if (!isset($_GET['type']) || !in_array($_GET['type'], ['direct', 'cart'])) {
    header('Location: index.php');
    exit();
}

// Handle direct product purchase
if ($_GET['type'] === 'direct') {
    $productId = isset($_GET['productId']) ? (int)$_GET['productId'] : 0;
    $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
    
    $product = $connect->getProductDetails($productId);
    if ($product) {
        $products[] = [
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $product['giaban'] * $quantity
        ];
        $total = $product['giaban'] * $quantity;
    }
}
// Handle cart checkout
elseif ($_GET['type'] === 'cart' && isset($_GET['items'])) {
    $items = explode(',', $_GET['items']);
    foreach ($items as $item) {
        list($productId, $quantity) = explode('_', $item);
        $product = $connect->getProductDetails((int)$productId);
        if ($product) {
            $products[] = [
                'product' => $product,
                'quantity' => (int)$quantity,
                'subtotal' => $product['giaban'] * (int)$quantity
            ];
            $total += $product['giaban'] * (int)$quantity;
        }
    }
}

if (empty($products)) {
    header('Location: index.php');
    exit();
}
include 'header.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <script src="https://www.paypal.com/sdk/js?client-id=YOUR_PAYPAL_CLIENT_ID&currency=USD"></script>
</head>
<body>
<div class="main-content">
    <div class="b-text d-flex flex-wrap align-items-center text-center justify-content-center" style="padding-top:150px;">
        <h1 class="fs36 clnau line_tt text-uppercase lora">Thanh toán</h1>
    </div>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <div class="checkout-form">
                    <h3 class="fs22 mb-4">Thông tin giao hàng</h3>
                    <form id="checkoutForm">
                        <div class="form-group mb-3">
                            <label>Họ và tên người nhận</label>
                            <input type="text" class="form-control" name="fullname" placeholder="Họ và tên" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Số điện thoại</label>
                            <input type="tel" class="form-control" name="phone" placeholder="Số điện thoại" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Tỉnh/Thành phố</label>
                            <select class="form-control" id="province" required>
                                <option value="">Chọn Tỉnh/Thành phố</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Quận/Huyện</label>
                            <select class="form-control" id="district" required>
                                <option value="">Chọn Quận/Huyện</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Phường/Xã</label>
                            <select class="form-control" id="ward" required>
                                <option value="">Chọn Phường/Xã</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Địa chỉ cụ thể</label>
                            <input type="text" class="form-control" name="address" placeholder="Địa chỉ cụ thể" required>
                        </div>
                        <div class="form-group mb-4">
                            <label>Phương thức thanh toán</label>
                            <div class="payment-buttons">
                                <div class="d-flex justify-content-between text-center box_detail_btn clwhite mb-3">
                                    <a href="javascript:void(0)" onclick="placeOrder('cod')" 
                                    class="d_detail_btn _cart_buynow d-inline-flex flex-column justify-content-center smooth bgcam">
                                        <span class="fs15 mbold text-uppercase">Thanh toán khi nhận hàng (COD)</span>
                                    </a>
                                    <a href="javascript:void(0)" id="paypal-button" 
                                    class="d_detail_btn d-inline-flex flex-column justify-content-center smooth bgblue">
                                        <span class="fs15 mbold text-uppercase">Thanh toán qua PayPal</span>
                                    </a>
                                </div>
                                <div id="paypal-button-container" class="mt-3" style="display: none;"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="order-summary">
                    <h3 class="fs22 mb-4">Đơn hàng của bạn</h3>
                    <div class="order-items">
                        <?php foreach ($products as $item): ?>
                            <div class="order-item mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="<?= htmlspecialchars($item['product']['path_anh_goc']) ?>" class="img-fluid" style="width: 80px;">
                                    <div class="ms-3">
                                        <h4 class="fs16"><?= htmlspecialchars($item['product']['tensanpham']) ?></h4>
                                        <p class="mb-0">Số lượng: <?= $item['quantity'] ?></p>
                                        <p class="mb-0 clnau"><?= number_format($item['subtotal'], 0, ',', '.') ?>đ</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="order-total mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span class="clnau"><?= number_format($total, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <span class="clnau">30.000đ</span>
                        </div>
                        <div class="d-flex justify-content-between fs18 fw-bold mt-3">
                            <span>Tổng cộng:</span>
                            <span class="clnau"><?= number_format($total + 30000, 0, ',', '.') ?>đ</span>
                        </div>                    
                </div>
            </div>
        </div>
    </div>
</div>
    <?php include 'footer.php'; ?>
</body>
    

    <script>
        // Load provinces
        fetch('https://provinces.open-api.vn/api/p/')
            .then(response => response.json())
            .then(data => {
                const provinceSelect = document.getElementById('province');
                data.forEach(province => {
                    provinceSelect.innerHTML += `<option value="${province.code}">${province.name}</option>`;
                });
            });

        // Load districts when province changes
        document.getElementById('province').addEventListener('change', function() {
            const provinceCode = this.value;
            fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
                .then(response => response.json())
                .then(data => {
                    const districtSelect = document.getElementById('district');
                    districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                    data.districts.forEach(district => {
                        districtSelect.innerHTML += `<option value="${district.code}">${district.name}</option>`;
                    });
                });
        });

        
        document.getElementById('paypal-button').addEventListener('click', function() {
        const paypalContainer = document.getElementById('paypal-button-container');
        paypalContainer.style.display = paypalContainer.style.display === 'none' ? 'block' : 'none';
    });

    // Remove the old payment method radio buttons toggle code
    // Update PayPal button container styling
    paypal.Buttons({
        style: {
            layout: 'vertical',
            color: 'blue',
            shape: 'rect',
            label: 'paypal'
        },
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?= ($total + 30000) / 25000 ?>' // Convert VND to USD
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                placeOrder('paypal');
            });
        }
    }).render('#paypal-button-container');



        // Load wards when district changes
        document.getElementById('district').addEventListener('change', function() {
            const districtCode = this.value;
            fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
                .then(response => response.json())
                .then(data => {
                    const wardSelect = document.getElementById('ward');
                    wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                    data.wards.forEach(ward => {
                        wardSelect.innerHTML += `<option value="${ward.code}">${ward.name}</option>`;
                    });
                });
        });

        // Toggle payment methods
        document.querySelectorAll('input[name="payment_method"]').forEach(input => {
            input.addEventListener('change', function() {
                const paypalContainer = document.getElementById('paypal-button-container');
                const orderButton = document.querySelector('.btn-thanhtoan');
                if (this.value === 'paypal') {
                    paypalContainer.style.display = 'block';
                    orderButton.style.display = 'none';
                } else {
                    paypalContainer.style.display = 'none';
                    orderButton.style.display = 'block';
                }
            });
        });

        // PayPal integration
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?= ($total + 30000) / 23000 ?>' // Convert VND to USD
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    placeOrder('paypal');
                });
            }
        }).render('#paypal-button-container');

        function placeOrder(paymentMethod = 'cod') {
            // Collect form data and submit order
            const formData = new FormData(document.getElementById('checkoutForm'));
            formData.append('payment_method', paymentMethod);
            
            fetch('process_order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: "Thành công!",
                        text: "Đơn hàng của bạn đã được đặt thành công",
                        icon: "success"
                    }).then(() => {
                        window.location.href = 'order_success.php';
                    });
                }
            });
        }
    </script>

    <style>
        body {
        background-image: url('images/anh-nen-pp.jpg');
        background-repeat: no-repeat;
        /* background-position: center; */
        /* min-height: 100vh; */
        
    }
    
        footer {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    footer .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

        .checkout-form {
            background:rgba(29, 29, 29, 0.59);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom:20px ;
        }

        .order-summary {
            background:rgba(29, 29, 29, 0.59);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .form-control {
            height: 45px;
            border: 1px solid #ebebeb;
        }

        .form-control:focus {
            border-color: #dbaf56;
            box-shadow: none;
        }

        .btn-thanhtoan {
            height: 45px;
            font-weight: 500;
        }

        .order-item {
            border-bottom: 1px solid #ebebeb;
            padding-bottom: 15px;
        }

        .payment-methods {
            border: 1px solid #ebebeb;
            padding: 15px;
            border-radius: 5px;
        }
    </style>


</html>