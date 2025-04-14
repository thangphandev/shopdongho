<?php
// Get statistics
$currentMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$currentYear = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Get statistics
$totalProducts = count($connect->getAllProducts());
$totalCategories = count($connect->getAllCategories());
$totalOrders = count($connect->getAllOrders($currentMonth, $currentYear));
$totalUsers = count($connect->getAllUsers());

// Get recent orders
$recentOrders = array_slice($connect->getAllOrders($currentMonth, $currentYear), 0, 5);

// Get top selling products and low stock products
$topSellingProducts = $connect->getTopSellingProducts(5, $currentMonth, $currentYear);
$lowStockProducts = $connect->getLowStockProducts(5, 5);

// Get order counts by status
$orderCounts = $connect->getOrderCountsByStatus($currentMonth, $currentYear);
$choXacNhanCount = $orderCounts['Chờ xác nhận'] ?? 0;
$daXacNhanCount = $orderCounts['Đã xác nhận'] ?? 0;
$dangVanChuyenCount = $orderCounts['Đang vận chuyển'] ?? 0;
$hoanThanhCount = $orderCounts['Hoàn thành'] ?? 0;
$daHuyCount = $orderCounts['Đã hủy'] ?? 0;

// Calculate revenue and profit
$revenue = $connect->calculateRevenue($currentMonth, $currentYear);
$pendingOrders = $choXacNhanCount + $daXacNhanCount;
$allOrders = $connect->getAllOrders($currentMonth, $currentYear);
$totalProfit = $connect->calculateProfit($currentMonth, $currentYear);


?>

<div class="container-fluid">
    <h1 class="mb-4">Dashboard</h1>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="" class="row align-items-end">
                        <input type="hidden" name="page" value="dashboard">
                        <div class="col-md-4">
                            <label for="month">Tháng:</label>
                            <select name="month" id="month" class="form-select">
                                <option value="">Tất cả các tháng</option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($currentMonth == $i) ? 'selected' : ''; ?>>
                                        Tháng <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="year">Năm:</label>
                            <select name="year" id="year" class="form-select">
                                <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($currentYear == $i) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Lọc</button>
                            <a href="admin.php?page=dashboard" class="btn btn-secondary">Đặt lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card stats-card primary">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title text-muted mb-0">Tổng sản phẩm</h4>
                        <h2 class="mt-2 mb-0"><?php echo $totalProducts; ?></h2>
                    </div>
                    <div class="icon text-primary">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card stats-card success">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title text-muted mb-0">Tổng danh mục</h4>
                        <h2 class="mt-2 mb-0"><?php echo $totalCategories; ?></h2>
                    </div>
                    <div class="icon text-success">
                        <i class="fas fa-list"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card stats-card warning">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title text-muted mb-0">Tổng đơn hàng</h4>
                        <h2 class="mt-2 mb-0"><?php echo $totalOrders; ?></h2>
                    </div>
                    <div class="icon text-warning">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card stats-card info">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title text-muted mb-0">Tổng người dùng</h4>
                        <h2 class="mt-2 mb-0"><?php echo $totalUsers; ?></h2>
                    </div>
                    <div class="icon text-info">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Second Row - Revenue and Order Status -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card stats-card danger">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title text-muted mb-0">Tổng tiền bán ra</h4>
                        <h2 class="mt-2 mb-0"><?php echo number_format($revenue, 0, ',', '.'); ?> ₫</h2>
                    </div>
                    <div class="icon text-danger">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card stats-card success">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title text-muted mb-0">Tổng lợi nhuận</h4>
                        <h2 class="mt-2 mb-0"><?php echo number_format($totalProfit, 0, ',', '.'); ?> ₫</h2>
                    </div>
                    <div class="icon text-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card stats-card warning">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title text-muted mb-0">Đơn hàng đang xử lý</h4>
                        <h2 class="mt-2 mb-0"><?php echo $pendingOrders; ?></h2>
                    </div>
                    <div class="icon text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Orders and Chart -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h3 class="card-title mb-0">Đơn hàng gần đây</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentOrders)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Không có đơn hàng nào</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['iddonhang']; ?></td>
                                            <td><?php echo $order['tendangnhap'] ?? $order['tennguoidat']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($order['ngaydat'])); ?></td>
                                            <td><?php echo number_format($order['tongtien'], 0, ',', '.'); ?> ₫</td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                switch ($order['trangthai']) {
                                                    case 'Chờ xác nhận':
                                                        $statusClass = 'badge bg-warning';
                                                        break;
                                                    case 'Đã xác nhận':
                                                        $statusClass = 'badge bg-info';
                                                        break;
                                                    case 'Đang vận chuyển':
                                                        $statusClass = 'badge bg-primary';
                                                        break;
                                                    case 'Hoàn thành':
                                                        $statusClass = 'badge bg-success';
                                                        break;
                                                    case 'Đã hủy':
                                                        $statusClass = 'badge bg-danger';
                                                        break;
                                                    default:
                                                        $statusClass = 'badge bg-secondary';
                                                }
                                                ?>
                                                <span class="<?php echo $statusClass; ?>"><?php echo $order['trangthai']; ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="admin.php?page=orders" class="btn btn-sm btn-primary">Xem tất cả đơn hàng</a>
                </div>
            </div>
        </div>
        
        <!-- Order Status Chart -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h3 class="card-title mb-0">Trạng thái đơn hàng</h3>
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart" height="260"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Profit Products -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h3 class="card-title mb-0">Sản phẩm có lợi nhuận cao nhất</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng đã bán</th>
                                    <th>Doanh thu</th>
                                    <th>Lợi nhuận</th>
                                    <th>Tỷ suất lợi nhuận</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($topProfitProducts)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($topProfitProducts as $product): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($product['path_anh_goc'])): ?>
                                                        <img src="../<?php echo $product['path_anh_goc']; ?>" alt="<?php echo $product['tensanpham']; ?>" class="img-thumbnail mr-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <?php endif; ?>
                                                    <span><?php echo $product['tensanpham']; ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo $product['total_sold']; ?></td>
                                            <td><?php echo number_format($product['total_revenue'], 0, ',', '.'); ?> ₫</td>
                                            <td><?php echo number_format($product['total_profit'], 0, ',', '.'); ?> ₫</td>
                                            <td>
                                                <?php 
                                                    $profitMargin = ($product['total_revenue'] > 0) ? 
                                                        ($product['total_profit'] / $product['total_revenue'] * 100) : 0;
                                                    echo number_format($profitMargin, 2) . '%';
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="admin.php?page=products" class="btn btn-sm btn-primary">Quản lý sản phẩm</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
    <!-- Top Selling Products -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h3 class="card-title mb-0">Sản phẩm bán chạy nhất</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Giá bán</th>
                                <th>Đã bán</th>
                                <th>Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topSellingProducts)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Không có dữ liệu</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($topSellingProducts as $product): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($product['path_anh_goc'])): ?>
                                                    <img src="../<?php echo $product['path_anh_goc']; ?>" alt="<?php echo $product['tensanpham']; ?>" class="img-thumbnail mr-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php endif; ?>
                                                <span><?php echo $product['tensanpham']; ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo number_format($product['giaban'], 0, ',', '.'); ?> ₫</td>
                                        <td><span class="badge bg-success"><?php echo $product['total_sold']; ?></span></td>
                                        <td><?php echo number_format($product['total_revenue'], 0, ',', '.'); ?> ₫</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <a href="admin.php?page=products" class="btn btn-sm btn-primary">Quản lý sản phẩm</a>
            </div>
        </div>
    </div>
    
    <!-- Low Stock Products -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h3 class="card-title mb-0">Sản phẩm sắp hết hàng</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Giá bán</th>
                                <th>Tồn kho</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($lowStockProducts)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Không có sản phẩm sắp hết hàng</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($lowStockProducts as $product): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($product['path_anh_goc'])): ?>
                                                    <img src="../<?php echo $product['path_anh_goc']; ?>" alt="<?php echo $product['tensanpham']; ?>" class="img-thumbnail mr-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php endif; ?>
                                                <span><?php echo $product['tensanpham']; ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo $product['tendanhmuc']; ?></td>
                                        <td><?php echo number_format($product['giaban'], 0, ',', '.'); ?> ₫</td>
                                        <td>
                                            <?php if ($product['soluong'] <= 0): ?>
                                                <span class="badge bg-danger">Hết hàng</span>
                                            <?php elseif ($product['soluong'] <= 3): ?>
                                                <span class="badge bg-warning"><?php echo $product['soluong']; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-info"><?php echo $product['soluong']; ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <a href="admin.php?page=products" class="btn btn-sm btn-danger">Nhập thêm hàng</a>
            </div>
        </div>
    </div>
</div>
    
    <!-- Quick Links -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h3 class="card-title mb-0">Truy cập nhanh</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="admin.php?page=products" class="btn btn-outline-primary btn-lg w-100">
                                <i class="fas fa-box mb-2"></i><br>
                                Quản lý sản phẩm
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="admin.php?page=categories" class="btn btn-outline-success btn-lg w-100">
                                <i class="fas fa-list mb-2"></i><br>
                                Quản lý danh mục
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="admin.php?page=orders" class="btn btn-outline-warning btn-lg w-100">
                                <i class="fas fa-shopping-cart mb-2"></i><br>
                                Quản lý đơn hàng
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="admin.php?page=users" class="btn btn-outline-info btn-lg w-100">
                                <i class="fas fa-users mb-2"></i><br>
                                Quản lý người dùng
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
        // Order Status Chart
        var orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
        var orderStatusChart = new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Hoàn thành', 'Chờ xác nhận', 'Đã xác nhận', 'Đang vận chuyển', 'Đã hủy'],
                datasets: [{
                    data: [
                        <?php echo $hoanThanhCount; ?>,
                        <?php echo $choXacNhanCount; ?>,
                        <?php echo $daXacNhanCount; ?>,
                        <?php echo $dangVanChuyenCount; ?>,
                        <?php echo $daHuyCount; ?>
                    ],
                    backgroundColor: [
                        '#28a745', // Hoàn thành - green
                        '#ffc107', // Chờ xác nhận - yellow
                        '#17a2b8', // Đã xác nhận - info blue
                        '#007bff', // Đang vận chuyển - primary blue
                        '#dc3545'  // Đã hủy - red
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                }
            }
        });
    });
</script>