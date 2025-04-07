<?php
require_once 'connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $matkhau = $_POST['password'] ?? ''; // Changed to match database field

    $connect = new Connect();

    if ($connect->login($email, $matkhau)) {
        // Successful login
        header('Location: index.php');
        exit();
    } else {
        // Failed login
        header('Location: login.php?error=1&message=' . urlencode('Email hoặc mật khẩu không đúng'));
        exit();
    }
}
?>
<!DOCTYPE html>
<!-- <html itemscope="" itemtype="http://schema.org/WebPage" lang="vi"> -->

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="robots" content="index,follow">
    <title>Watch</title>
    <meta property="og:site_name" content="Watch">
    <meta property="og:type" content="article">
    <meta property="og:locale" content="vi_vn">
    <link rel="shortcut icon" href="images/3_2.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/toastr.min.css">
    <link rel="stylesheet" href="css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/font-awesome.css">
    <link rel="stylesheet" href="css/tiny-slider.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/media.css">
    <link rel="stylesheet" href="css/thuc.css"> 
    <script src="js/jquery3.2.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Your existing styles -->
</head>

<body class="scrollstyle1">
    <header>
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center rela">
                <a href="" title="Đồng Hồ Boss Luxury" alt="Đồng Hồ Boss Luxury" class="smooth logo d-inline-block">
                    <img src="images/2222.png" alt="" title="Đồng Hồ Boss Luxury" class="img-responsive">
                </a>
                <div class="h-menu">
                    <div class="main-nav d-flex flex-wrap align-items-center">
                        <ul>
                            <li><a href="index.php" title="TRANG CHỦ" class="smooth">TRANG CHỦ</a></li>
                            <li class="menusp">
                                <a href="san-pham" title="Thương hiệu" class="smooth">Thương hiệu</a>
                                <ul>
                                    <li class="col_menusp col_tm">
                                        <span>Thương hiệu</span>
                                        <ul>
                                            <li class=""><a href="https://bossluxurywatch.vn/rolex" title="Rolex"
                                                    class="smooth lr upper">Rolex</a></li>
                                            <li class=""><a href="https://bossluxurywatch.vn/hublot" title="Hublot"
                                                    class="smooth lr upper">Hublot</a></li>
                                            <li class=""><a href="https://bossluxurywatch.vn/richard-mille" title="Richard Mille"
                                                    class="smooth lr upper">Richard Mille</a></li>
                                            <li class=""><a href="https://bossluxurywatch.vn/patek-philippe" title="Patek Philippe"
                                                    class="smooth lr upper">Patek Philippe</a></li>
                                            <li class=""><a href="https://bossluxurywatch.vn/corum" title="Corum"
                                                    class="smooth lr upper">Corum</a></li>
                                            <li class=""><a href="https://bossluxurywatch.vn/audemars-piguet-3" title="AUDEMARS PIGUET" class="smooth lr upper">AUDEMARS PIGUET</a></li>
                                            <li class=""><a href="https://bossluxurywatch.vn/jacob-co" title="Jacob&amp;Co"
                                                    class="smooth lr upper">Jacob&amp;Co</a></li>
                                            <li class=""><a href="https://bossluxurywatch.vn/chopard" title="Chopard"
                                                    class="smooth lr upper">Chopard</a></li>
                                            <li class=""><a href="https://bossluxurywatch.vn/vacheron-constantin"
                                                    title="Vacheron Constantin" class="smooth lr upper">Vacheron Constantin</a></li>
                                            <li class=""><a href="https://bossluxurywatch.vn/zenith" title="Zenith"
                                                    class="smooth lr upper">Zenith</a></li>
                                            <li class=""><a href="https://bossluxurywatch.vn/piaget" title="Piaget"
                                                    class="smooth lr upper">Piaget</a></li>
                                            <li class=""><a href="https://bossluxurywatch.vn/bvl-gari" title="BVLGARI"
                                                    class="smooth lr upper">BVLGARI</a></li>
                                            <li class=""><a href="https://bossluxurywatch.vn/chanel" title="Chanel"
                                                    class="smooth lr upper">Chanel</a></li>
                                        </ul>
                                    </li>
                                    <li class="col_menusp col_segment">
                                        <span>Phân khúc</span>
                                        <ul>
                                            <li class=" mb-2"><a
                                                    href="https://bossluxurywatch.vn/phan-khuc?price=10000000%20-%20200000000"
                                                    title="Dưới 200 triệu VNĐ" class="smooth ">Dưới 200 triệu VNĐ</a></li>
                                            <li class=" mb-2"><a
                                                    href="https://bossluxurywatch.vn/phan-khuc?price=200000000%20-%20500000000"
                                                    title="Từ 200 - 500 triệu VNĐ" class="smooth ">Từ 200 - 500 triệu VNĐ</a></li>
                                            <li class=" mb-2"><a
                                                    href="https://bossluxurywatch.vn/phan-khuc?price=500000000%20-%201000000000"
                                                    title="Từ 500 triệu - 1 tỷ VNĐ" class="smooth ">Từ 500 triệu - 1 tỷ VNĐ</a></li>
                                            <li class=" mb-2"><a
                                                    href="https://bossluxurywatch.vn/phan-khuc?price=1000000000%20-%202000000000"
                                                    title="Từ 1 - 2 tỷ VNĐ" class="smooth ">Từ 1 - 2 tỷ VNĐ</a></li>
                                            <li class=" mb-2"><a
                                                    href="https://bossluxurywatch.vn/phan-khuc?price=2000000000%20-%205000000000"
                                                    title="Từ 2 tỷ - 5 tỷ VNĐ" class="smooth ">Từ 2 tỷ - 5 tỷ VNĐ</a></li>
                                        </ul>
                                    </li>
                                </ul>

                            </li>
                            <li class="menusp">
                                <a href="san-pham" title="Thương hiệu" class="smooth">HÀNG CÓ SẴN</a>
                            </li>
                            <li class="menusp">
                                <a href="san-pham" title="Thương hiệu" class="smooth">REIEW</a>
                                <!-- Menu động có thể thêm từ PHP -->
                            </li>
                            <li class="menusp">
                                <a href="san-pham" title="Thương hiệu" class="smooth">TƯ VẤN</a>
                                <!-- Menu động có thể thêm từ PHP -->
                            </li>
                            <li class="menusp">
                                <a href="san-pham" title="Thương hiệu" class="smooth">LIÊN HỆ</a>
                                <!-- Menu động có thể thêm từ PHP -->
                            </li>
                            <li class="menusp">
                                <a href="san-pham" title="Thương hiệu" class="smooth">GIỚI THIỆU</a> <!-- Menu động có thể thêm từ PHP -->
                            </li>
                            <!-- Rest of your menu items -->
                            <button type="button" class="d_btn clnau d_seach_btn toggle_seach cspoint ml-2">
                                <i class="fa fa-search"></i>
                            </button>

                            <a href="giohang.php" rel="nofollow,noindex,noopener" class="d_btn clnau bdnau fs20 ml-2 cspoint d-inline-block text-center cart-icon-wrapper">
                                <i class="fa fa-shopping-cart"></i>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <span class="cart-count"><?php echo $connect->laysoluongsanpham($_SESSION['user_id']); ?></span>
                                <?php endif; ?>
                            </a>

                            <div class="user-dropdown">
                                <button type="button" class="d_btn clnau bdnau fs20 cspoint d-inline-block text-center ml-2">
                                    <i class="fa fa-user"></i>
                                </button>
                                <div class="dropdown-content">
                                    <a href="thong-tin-ca-nhan"><i class="fa fa-user-circle"></i> Thông tin cá nhân</a>
                                    <a href="logout.php">Đăng xuất <i class="fa fa-sign-out"></i></a>
                                </div>
                            </div>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>


    <div class="search-popup">
        <div class="search-wrapper">
            <div class="container">
                <!-- ... existing search header ... -->
                <div class="search-content">
                    <form class="form_fast_search search-form" method="POST" action="tim-kiem" rel="nofollow">
                        <div class="quick-search mb-4">
                            <input type="text" name="key" class="form-control h-input fs24" placeholder="Tìm kiếm nhanh">
                        </div>
                        <div class="advanced-search">
                            <!-- Brand checkboxes -->
                            <div class="search-section mb-4">
                                <h4>Thương hiệu</h4>
                                <div class="checkbox-group">
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="brands[]" value="rolex">
                                        <span class="checkbox-text">Rolex</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="brands[]" value="hublot">
                                        <span class="checkbox-text">Hublot</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="brands[]" value="richard-mille">
                                        <span class="checkbox-text">Richard Mille</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="brands[]" value="patek-philippe">
                                        <span class="checkbox-text">Patek Philippe</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="brands[]" value="corum">
                                        <span class="checkbox-text">Corum</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="brands[]" value="audemars-piguet">
                                        <span class="checkbox-text">Audemars Piguet</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="brands[]" value="jacob-co">
                                        <span class="checkbox-text">Jacob&amp;Co</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="brands[]" value="chopard">
                                        <span class="checkbox-text">Chopard</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="brands[]" value="vacheron-constantin">
                                        <span class="checkbox-text">Vacheron Constantin</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="brands[]" value="zenith">
                                        <span class="checkbox-text">Zenith</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="brands[]" value="piaget">
                                        <span class="checkbox-text">Piaget</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="brands[]" value="bvlgari">
                                        <span class="checkbox-text">BVLGARI</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="brands[]" value="chanel">
                                        <span class="checkbox-text">Chanel</span>
                                    </label>
                                </div>
                            </div>

                            <div class="search-section mb-4">
                                <h4>Phân khúc giá</h4>
                                <div class="checkbox-group">
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="price_range[]" value="10000000-200000000">
                                        <span class="checkbox-text">Dưới 200 triệu VNĐ</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="price_range[]" value="200000000-500000000">
                                        <span class="checkbox-text">Từ 200 - 500 triệu VNĐ</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="price_range[]" value="500000000-1000000000">
                                        <span class="checkbox-text">Từ 500 triệu - 1 tỷ VNĐ</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="price_range[]" value="1000000000-2000000000">
                                        <span class="checkbox-text">Từ 1 - 2 tỷ VNĐ</span>
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="price_range[]" value="2000000000-5000000000">
                                        <span class="checkbox-text">Từ 2 tỷ - 5 tỷ VNĐ</span>
                                    </label>
                                </div>
                            </div>


                            <!-- Price range slider -->
                            <div class="search-section mb-4">
                                <h4>Khoảng giá</h4>
                                <div class="price-range-wrapper">
                                    <div class="price-input">
                                        <div class="field">
                                            <span>Min</span>
                                            <input type="number" class="input-min" value="0">
                                        </div>
                                        <div class="separator">-</div>
                                        <div class="field">
                                            <span>Max</span>
                                            <input type="number" class="input-max" value="5000000000">
                                        </div>
                                    </div>
                                    <div class="slider">
                                        <div class="progress"></div>
                                    </div>
                                    <div class="range-input">
                                        <input type="range" class="range-min" min="0" max="5000000000" value="0" step="1000000">
                                        <input type="range" class="range-max" min="0" max="5000000000" value="5000000000" step="1000000">
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="selected_brand" id="selected_brand">
                            <input type="hidden" name="min_price" id="min_price">
                            <input type="hidden" name="max_price" id="max_price">
                        </div>
                        <button type="submit" class="btn-search-submit" rel="nofollow">
                            <i class="fa fa-search"></i> Tìm kiếm
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .cart-icon-wrapper {
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color:rgb(247, 0, 0);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            z-index: 999999;
        }
        .user-dropdown {
            position: relative;
            display: inline-block;
        }

        .user-dropdown .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background-color: rgba(15, 15, 15, 0.1);
            min-width: 200px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            z-index: 1000;
            margin-top: 10px;
        }

        .user-dropdown .dropdown-content::before {
            content: '';
            position: absolute;
            top: -8px;
            right: 15px;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid #fff;
        }

        .user-dropdown .dropdown-content a {
            color: while;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: all 0.3s ease;
            font-size: 14px;
            text-align: center;
        }

        .user-dropdown .dropdown-content a i {
            margin-right: 8px;
            width: 12px;
            text-align: center;
        }

        .user-dropdown .dropdown-content a:hover {
            /* background-color: #f5f5f5; */
            color: #ffc107;
        }

        .user-dropdown .dropdown-content a:first-child {
            border-bottom: 1px solid #ffc107;
        }

        /* Active state for dropdown */
        .user-dropdown.active .dropdown-content {
            display: block;
        }

        /* search Header Styles  slider 2 value*/
        .search-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            display: none;
        }

        .search-wrapper {
            position: relative;
            background-color: #fff;
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            color: while;
            background-color: rgba(0, 0, 0, 0.49);
        }

        .search-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .search-header h3 {
            font-size: 24px;
            color: while;
            margin: 0;
        }

        .close-search {
            background: none;
            border: none;
            font-size: 24px;
            color: #666;
            cursor: pointer;
            padding: 5px;
            transition: color 0.3s ease;
        }

        .close-search:hover {
            color: #333;
        }

        .search-content {
            padding-top: 20px;
        }

        .quick-search input {
            height: 50px;
            border: 2px solid #eee;
            border-radius: 4px;
            padding: 0 15px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .quick-search input:focus {
            border-color: #c8a96a;
            outline: none;
        }

        .btn-search-submit {
            width: 100%;
            padding: 12px;
            background-color: #c8a96a;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-search-submit:hover {
            background-color: #b39355;
        }

        /* Animation */
        .search-wrapper {
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .search-popup.active .search-wrapper {
            transform: translateY(0);
            opacity: 1;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .search-wrapper {
                margin: 20px;
                padding: 15px;
            }

            .search-header h3 {
                font-size: 20px;
            }
        }


        /* Banner Slider Styles */
        .banner-slider {
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .banner-slider .slider-container {
            position: relative;
        }

        .banner-slider .item {
            position: relative;
            height: 500px;
        }

        .banner-slider .item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .banner-slider .slider-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 10px;
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            text-align: left;
        }

        .banner-slider .slider-caption h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #fff;
        }

        .banner-slider .slider-caption p {
            font-size: 16px;
            margin-bottom: 15px;
        }

        .banner-slider .btn-slider {
            display: inline-block;
            padding: 10px 20px;
            background-color: #c8a96a;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .banner-slider .btn-slider:hover {
            background-color: #a88c4e;
        }

        /* Tiny Slider Navigation Customization */
        .banner-slider .tns-nav {
            position: absolute;
            bottom: 20px;
            /* Điều chỉnh khoảng cách từ đáy */
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
        }


        .banner-slider .tns-nav button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            border: none;
            margin: 0 5px;
        }

        .banner-slider .tns-nav button.tns-nav-active {
            background: #c8a96a;
        }

        @media (max-width: 768px) {
            .banner-slider .item {
                height: 350px;
            }

            .banner-slider .slider-caption h2 {
                font-size: 22px;
            }

            .banner-slider .slider-caption p {
                font-size: 14px;
            }
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            color: #fff;
            cursor: pointer;
        }

        .checkbox-item input[type="checkbox"] {
            margin-right: 8px;
        }

        .search-section h4 {
            color: #fff;
            margin-bottom: 15px;
            font-size: 18px;
        }

        /* Price Range Slider Styles */
        .price-range-wrapper {
            width: 100%;
            background: transparent;
            padding: 10px 0;
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
            background: #c8a96a;
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
            background: #c8a96a;
            pointer-events: auto;
            -webkit-appearance: none;
            cursor: pointer;
        }

        .range-input input::-moz-range-thumb {
            height: 17px;
            width: 17px;
            border: none;
            border-radius: 50%;
            background: #c8a96a;
            pointer-events: auto;
            -moz-appearance: none;
            cursor: pointer;
        }
    </style>

    <script>
        $(document).ready(function() {
            // Toggle dropdown on button click
            $('.user-dropdown button').click(function(e) {
                e.stopPropagation();
                $('.user-dropdown').toggleClass('active');
            });

            // Close dropdown when clicking outside
            $(document).click(function(e) {
                if (!$(e.target).closest('.user-dropdown').length) {
                    $('.user-dropdown').removeClass('active');
                }
            });

            // Prevent dropdown from closing when clicking inside it
            $('.dropdown-content').click(function(e) {
                e.stopPropagation();
            });
        });
        $(document).ready(function() {
            const rangeInput = $(".range-input input");
            const priceInput = $(".price-input input");
            const range = $(".slider .progress");
            let priceGap = 100000000;

            priceInput.each(function() {
                $(this).on("input", function(e) {
                    let minPrice = parseInt($(".price-input .input-min").val());
                    let maxPrice = parseInt($(".price-input .input-max").val());

                    if ((maxPrice - minPrice >= priceGap) && maxPrice <= rangeInput.last().attr("max")) {
                        if ($(this).hasClass("input-min")) {
                            $(".range-input .range-min").val(minPrice);
                            range.css("left", (minPrice / rangeInput.last().attr("max")) * 100 + "%");
                        } else {
                            $(".range-input .range-max").val(maxPrice);
                            range.css("right", 100 - (maxPrice / rangeInput.last().attr("max")) * 100 + "%");
                        }
                    }
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
                            left: (minVal / rangeInput.last().attr("max")) * 100 + "%",
                            right: 100 - (maxVal / rangeInput.last().attr("max")) * 100 + "%"
                        });
                    }

                    // Update hidden inputs for form submission
                    $("#min_price").val(minVal);
                    $("#max_price").val(maxVal);
                });
            });

            // Handle checkbox changes
            $('input[name="brands[]"]').on('change', function() {
                let selectedBrands = [];
                $('input[name="brands[]"]:checked').each(function() {
                    selectedBrands.push($(this).val());
                });
                $('#selected_brand').val(selectedBrands.join(','));
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Open search popup
            $('.d_seach_btn').click(function(e) {
                e.preventDefault();
                $('.search-popup').fadeIn(300).addClass('active');
                $('body').css('overflow', 'hidden');
                $('.quick-search input').focus();
            });

            // Close search popup
            $('.close-search').click(function() {
                $('.search-popup').fadeOut(300).removeClass('active');
                $('body').css('overflow', '');
            });

            // Close when clicking outside
            $('.search-popup').click(function(e) {
                if ($(e.target).closest('.search-wrapper').length === 0) {
                    $('.search-popup').fadeOut(300).removeClass('active');
                    $('body').css('overflow', '');
                }
            });

            // Rest of your existing code...
        });
    </script>