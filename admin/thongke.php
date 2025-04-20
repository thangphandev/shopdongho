<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../connect.php';
$connect = new Connect();

$currentYear = date('Y');
$currentMonth = date('n');
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : $currentYear;
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : $currentMonth;

// Initialize arrays
$revenueData = [];
$profitData = [];
$yearlyTotals = [
    'doanh_thu' => 0,
    'loi_nhuan' => 0
];

// Get monthly data and calculate yearly totals
for ($month = 1; $month <= 12; $month++) {
    $monthData = $connect->getRevenueAndProfit($month, $selectedYear);
    $revenueData[] = floatval($monthData['doanh_thu']);
    $profitData[] = floatval($monthData['loi_nhuan']);
    $yearlyTotals['doanh_thu'] += $monthData['doanh_thu'];
    $yearlyTotals['loi_nhuan'] += $monthData['loi_nhuan'];
}

// Get data for selected month
$currentMonthData = $connect->getRevenueAndProfit($selectedMonth, $selectedYear);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê doanh thu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        #revenueChart {
            width: 100%;
            height: 400px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h2 class="mb-4">Thống kê doanh thu và lợi nhuận</h2>
        
        <!-- Month and Year selector -->
        <div class="mb-4">
            <form class="d-flex align-items-center gap-3">
                <input type="hidden" name="page" value="thongke">
                
                <div class="d-flex align-items-center">
                    <label class="me-2">Chọn tháng:</label>
                    <select name="month" class="form-select w-auto" onchange="this.form.submit()">
                        <?php
                        for ($month = 1; $month <= 12; $month++) {
                            $selected = ($month == $selectedMonth) ? 'selected' : '';
                            echo "<option value='$month' $selected>Tháng $month</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="d-flex align-items-center">
                    <label class="me-2">Chọn năm:</label>
                    <select name="year" class="form-select w-auto" onchange="this.form.submit()">
                        <?php
                        for ($year = $currentYear; $year >= $currentYear - 5; $year--) {
                            $selected = ($year == $selectedYear) ? 'selected' : '';
                            echo "<option value='$year' $selected>$year</option>";
                        }
                        ?>
                    </select>
                </div>
            </form>
        </div>

        <!-- Overview cards -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Doanh thu tháng <?php echo $selectedMonth; ?>/<?php echo $selectedYear; ?></h5>
                        <p class="card-text"><?php echo number_format($currentMonthData['doanh_thu'], 0, ',', '.'); ?> đ</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Lợi nhuận tháng <?php echo $selectedMonth; ?>/<?php echo $selectedYear; ?></h5>
                        <p class="card-text"><?php echo number_format($currentMonthData['loi_nhuan'], 0, ',', '.'); ?> đ</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Tổng doanh thu năm <?php echo $selectedYear; ?></h5>
                        <p class="card-text"><?php echo number_format($yearlyTotals['doanh_thu'], 0, ',', '.'); ?> đ</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Tổng lợi nhuận năm <?php echo $selectedYear; ?></h5>
                        <p class="card-text"><?php echo number_format($yearlyTotals['loi_nhuan'], 0, ',', '.'); ?> đ</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Biểu đồ doanh thu và lợi nhuận theo tháng năm <?php echo $selectedYear; ?></h5>
                <canvas id="revenueChart"></canvas>
                <?php if (array_sum($revenueData) == 0 && array_sum($profitData) == 0): ?>
                    <p class="text-warning">Không có dữ liệu để hiển thị biểu đồ cho năm <?php echo $selectedYear; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('revenueChart');
        if (!canvas) {
            console.error('Canvas element with id "revenueChart" not found');
            return;
        }

        const revenueData = <?php echo json_encode($revenueData); ?>;
        const profitData = <?php echo json_encode($profitData); ?>;
        console.log('Revenue Data:', revenueData);
        console.log('Profit Data:', profitData);

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                datasets: [{
                    label: 'Doanh thu',
                    data: revenueData,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }, {
                    label: 'Lợi nhuận',
                    data: profitData,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND'
                                }).format(value);
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + 
                                    new Intl.NumberFormat('vi-VN', {
                                        style: 'currency',
                                        currency: 'VND'
                                    }).format(context.raw);
                            }
                        }
                    }
                }
            }
        });
    });
    </script>
</body>
</html>