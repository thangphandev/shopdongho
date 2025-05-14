<?php
require_once 'config.php';

class connect {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getNewProducts($limit = 8) {
        try {
            $query = "SELECT DISTINCT s.*, 
                        k.gia_giam,
                        k.ngaybatdau,
                        k.ngayketthuc 
                     FROM sanpham s
                     LEFT JOIN khuyenmai k ON s.idkhuyenmai = k.idkhuyenmai
                     WHERE (k.ngayketthuc IS NULL OR k.ngayketthuc >= CURRENT_TIMESTAMP)
                     AND (k.ngaybatdau IS NULL OR k.ngaybatdau <= CURRENT_TIMESTAMP)
                     AND s.trangthai = 1
                     AND s.ngaytao >= DATE_SUB(CURRENT_DATE(), INTERVAL 10 DAY)
                     AND s.soluong > 0
                     ORDER BY s.ngaytao DESC
                     LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    // hàng có sẵn
    public function getProductsByStock() {
        try {
            $query = "SELECT DISTINCT s.*, 
                        k.gia_giam,
                        k.ngaybatdau,
                        k.ngayketthuc 
                     FROM sanpham s
                     LEFT JOIN khuyenmai k ON s.idkhuyenmai = k.idkhuyenmai
                     WHERE s.soluong > 0 
                     AND s.trangthai = 1
                     AND (k.ngayketthuc IS NULL OR k.ngayketthuc >= CURRENT_TIMESTAMP)
                     AND (k.ngaybatdau IS NULL OR k.ngaybatdau <= CURRENT_TIMESTAMP)

                     GROUP BY s.idsanpham
                     ORDER BY s.ngaytao DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getProductsByStock: " . $e->getMessage());
            return array();
        }
    }
    public function getAllPolicies() {
        try {
            $query = "SELECT * FROM chinhsach ORDER BY id ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getAllPolicies: " . $e->getMessage());
            return array();
        }
    }
    public function getPolicyById($id) {
        try {
            $query = "SELECT * FROM chinhsach WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getPolicyById: " . $e->getMessage());
            return null;
        }
    }
    // Lấy tất cả các mục giới thiệu
public function getAllIntroSections() {
    try {
        $sql = "SELECT * FROM gioithieu WHERE trang_thai = 1 ORDER BY thu_tu ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Lỗi khi lấy danh sách mục giới thiệu: " . $e->getMessage());
        return [];
    }
}
// Lấy thông tin mục giới thiệu theo ID
public function getIntroSectionByCode($id) {
    try {
        $sql = "SELECT * FROM gioithieu WHERE id = :id AND trang_thai = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Lỗi khi lấy thông tin mục giới thiệu: " . $e->getMessage());
        return null;
    }
}
    public function addPolicy($tieude, $noidung) {
        try {
            $query = "INSERT INTO chinhsach (tieude, noidung) VALUES (:tieude, :noidung)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':tieude', $tieude, PDO::PARAM_STR);
            $stmt->bindParam(':noidung', $noidung, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error in addPolicy: " . $e->getMessage());
            return false;
        }
    }
    public function updatePolicy($id, $tieude, $noidung) {
        try {
            $query = "UPDATE chinhsach SET tieude = :tieude, noidung = :noidung WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':tieude', $tieude, PDO::PARAM_STR);
            $stmt->bindParam(':noidung', $noidung, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error in updatePolicy: " . $e->getMessage());
            return false;
        }
    }
    public function deletePolicy($id) {
        try {
            $query = "DELETE FROM chinhsach WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error in deletePolicy: " . $e->getMessage());
            return false;
        }
    }
    public function getCategories() {  
        try {
            $query = "SELECT * FROM danhmuc 
                    WHERE trangthai = 1" ;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }        
    public function getProductsByCategory($categoryId) {
        try {
            $query = "SELECT DISTINCT s.*, h.duongdan as image_path, d.tendanhmuc , k.gia_giam,
                        k.ngaybatdau,
                        k.ngayketthuc 
                    FROM sanpham s 
                    LEFT JOIN khuyenmai k ON s.idkhuyenmai = k.idkhuyenmai
                    LEFT JOIN hinhanhsanpham h ON s.idsanpham = h.idsanpham
                    LEFT JOIN danhmuc d ON s.iddanhmuc = d.iddanhmuc
                    WHERE s.iddanhmuc = :categoryId 
                    AND s.trangthai = 1
                    AND s.soluong > 0
                    GROUP BY s.idsanpham
                    ORDER BY s.ngaytao DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage(); 
            return false;
        }
    }

    public function getProductDetails($productId) {
    try {
        $query = "SELECT s.*, h.duongdan as image_path, d.tendanhmuc,
                    k.gia_giam, k.ngaybatdau, k.ngayketthuc ,loaiday.ten_loai_day, loaiday.mo_ta_loai_day,
                    loaimay.mo_ta_loai_may,
                    loaimay.ten_loai_may, chinhsachbaohanh.*
                FROM sanpham s 
                JOIN chinhsachbaohanh ON s.chinhsachbaohanh = chinhsachbaohanh.id_chinh_sach
                LEFT JOIN hinhanhsanpham h ON s.idsanpham = h.idsanpham 
                LEFT JOIN danhmuc d ON s.iddanhmuc = d.iddanhmuc
                LEFT JOIN loaiday  ON s.loaiday = loaiday.id_loai_day
                LEFT JOIN loaimay  ON s.loaimay = loaimay.id_loai_may
                LEFT JOIN khuyenmai k ON s.idkhuyenmai = k.idkhuyenmai                
                WHERE s.idsanpham = :productId 
                AND (k.ngayketthuc IS NULL OR k.ngayketthuc >= CURRENT_TIMESTAMP)
                AND (k.ngaybatdau IS NULL OR k.ngaybatdau <= CURRENT_TIMESTAMP)
                GROUP BY s.idsanpham";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

//tìm kiếm sản phầm theo form
public function searchProducts($keyword, $brands, $watch_types, $strap_types, $gender, $price_ranges, $min_price, $max_price) {
    try {
        $conditions = [];
        $params = [];
        
        // Base query
        $query = "SELECT DISTINCT s.*, h.duongdan as image_path, d.tendanhmuc,
                    k.gia_giam, k.ngaybatdau, k.ngayketthuc,
                    loaiday.ten_loai_day, loaimay.ten_loai_may
                FROM sanpham s 
                LEFT JOIN hinhanhsanpham h ON s.idsanpham = h.idsanpham 
                LEFT JOIN danhmuc d ON s.iddanhmuc = d.iddanhmuc
                LEFT JOIN loaiday ON s.loaiday = loaiday.id_loai_day
                LEFT JOIN loaimay ON s.loaimay = loaimay.id_loai_may
                LEFT JOIN khuyenmai k ON s.idkhuyenmai = k.idkhuyenmai
                WHERE s.trangthai = 1 AND s.soluong > 0";
        
       
        if (!empty($keyword)) {
            $conditions[] = "(s.tensanpham LIKE :keyword OR s.mota LIKE :keyword OR d.tendanhmuc LIKE :keyword)";
            $params[':keyword'] = "%$keyword%";
        }
        
        // Brand filter
        if (!empty($brands)) {
            $placeholders = [];
            foreach ($brands as $key => $brand) {
                $param = ":brand$key";
                $placeholders[] = $param;
                $params[$param] = $brand;
            }
            $conditions[] = "s.iddanhmuc IN (" . implode(", ", $placeholders) . ")";
        }
        
        // Watch type filter
        if (!empty($watch_types)) {
            $placeholders = [];
            foreach ($watch_types as $key => $type) {
                $param = ":watchType$key";
                $placeholders[] = $param;
                $params[$param] = $type;
            }
            $conditions[] = "s.loaimay IN (" . implode(", ", $placeholders) . ")";
        }
        
        // Strap type filter
        if (!empty($strap_types)) {
            $placeholders = [];
            foreach ($strap_types as $key => $type) {
                $param = ":strapType$key";
                $placeholders[] = $param;
                $params[$param] = $type;
            }
            $conditions[] = "s.loaiday IN (" . implode(", ", $placeholders) . ")";
        }
        
        // Gender filter
        if (!empty($gender)) {
            $placeholders = [];
            foreach ($gender as $key => $g) {
                $param = ":gender$key";
                $placeholders[] = $param;
                $params[$param] = $g;
            }
            $conditions[] = "s.gioitinh IN (" . implode(", ", $placeholders) . ")";
        }
        
        // Price range filter from checkboxes
        if (!empty($price_ranges)) {
            $priceConditions = [];
            foreach ($price_ranges as $key => $range) {
                list($min, $max) = explode('-', $range);
                $priceConditions[] = "(s.giaban >= $min AND s.giaban <= $max)";
            }
            $conditions[] = "(" . implode(" OR ", $priceConditions) . ")";
        }
        
        // Price range filter from slider
        if ($min_price > 0 || $max_price < PHP_INT_MAX) {
            $conditions[] = "(s.giaban >= :min_price AND s.giaban <= :max_price)";
            $params[':min_price'] = $min_price;
            $params[':max_price'] = $max_price;
        }
        
        // Add conditions to query
        if (!empty($conditions)) {
            $query .= " AND " . implode(" AND ", $conditions);
        }
        
        // Group by and order
        $query .= " GROUP BY s.idsanpham ORDER BY s.ngaytao DESC";
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error in searchProducts: " . $e->getMessage());
        return [];
    }
}
    // Add these new methods
    public function getAllBrands() {
        try {
            $query = "SELECT iddanhmuc, tendanhmuc 
                      FROM danhmuc 
                      WHERE trangthai = 1 
                      ORDER BY tendanhmuc";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getAllBrands: " . $e->getMessage());
            return [];
        }
    }

    

    public function getStrapTypes() {
        try {
            $query = "SELECT * FROM loaiday where trangthai = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    public function getRelatedProducts($categoryId, $currentProductId, $limit = 10) {
        try {
            $query = "SELECT * FROM sanpham 
                    WHERE iddanhmuc = :categoryId 
                    AND idsanpham != :currentProductId 
                    AND trangthai = 1
                    ORDER BY ngaytao DESC 
                    LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
            $stmt->bindParam(':currentProductId', $currentProductId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getRelatedProducts: " . $e->getMessage());
            return [];
        }
    }
    public function getAllProductImages($productId) {
        try {
            $query = "SELECT duongdan as image_path 
                    FROM hinhanhsanpham 
                    WHERE idsanpham = :productId";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getAllProductImages: " . $e->getMessage());
            return [];
        }
    }

    

//hàm tương tác với đon hàng
    public function getUserOrders($userId) {
        try {
            $query = "SELECT d.*, ct.trangthai as trangthai_thanhtoan 
                    FROM donhang d
                    LEFT JOIN chitietthanhtoan ct ON d.idthanhtoan = ct.idthanhtoan
                    WHERE d.idnguoidung = :userId 
                    ORDER BY d.ngaydat DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting user orders: " . $e->getMessage());
            return false;
        }
    }

    // public function getOrderDetails($orderId) {
    //     try {
    //         $query = "SELECT cd.*, s.tensanpham, s.idsanpham, s.path_anh_goc,
    //                     s.bosuutap, s.loaimay, s.chatlieuvo, s.loaiday, s.matkinh,
    //                     s.mausac, s.kichthuoc, s.doday, s.chongnuoc,
    //                     cd.giaban, cd.soluong,
    //                     d.ngaydat, d.trangthai, d.tennguoidat, d.diachigiao, d.sdt, lm.ten_loai_may,
    //                     ct.phuongthuctt, ct.trangthai as trangthai_thanhtoan
    //                 FROM chitietdonhang cd
    //                 JOIN loaimay lm ON s.loaimay = lm.id_loai_may
    //                 JOIN sanpham s ON cd.idsanpham = s.idsanpham
    //                 JOIN donhang d ON cd.iddonhang = d.iddonhang
    //                 LEFT JOIN chitietthanhtoan ct ON d.idthanhtoan = ct.idthanhtoan
    //                 WHERE cd.iddonhang = :orderId";
            
    //         $stmt = $this->conn->prepare($query);
    //         $stmt->bindParam(':orderId', $orderId);
    //         $stmt->execute();
    //         return $stmt->fetchAll(PDO::FETCH_ASSOC);
    //     } catch(PDOException $e) {
    //         error_log("Error getting order details: " . $e->getMessage());
    //         return [];
    //     }
    // }
    public function getOrderDetails($orderId) {
        try {
            $query = "SELECT cd.*, s.tensanpham, s.idsanpham, s.path_anh_goc,
                        s.bosuutap, s.loaimay, s.chatlieuvo, s.loaiday, s.matkinh,
                        s.mausac, s.kichthuoc, s.doday, s.chongnuoc,
                        cd.giaban, cd.soluong,
                        d.ngaydat, d.trangthai, d.tennguoidat, d.diachigiao, d.sdt, 
                        lm.ten_loai_may,
                        ct.phuongthuctt, ct.trangthai as trangthai_thanhtoan
                    FROM chitietdonhang cd
                    JOIN sanpham s ON cd.idsanpham = s.idsanpham
                    LEFT JOIN loaimay lm ON s.loaimay = lm.id_loai_may
                    JOIN donhang d ON cd.iddonhang = d.iddonhang
                    LEFT JOIN chitietthanhtoan ct ON d.idthanhtoan = ct.idthanhtoan
                    WHERE cd.iddonhang = :orderId";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':orderId', $orderId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting order details: " . $e->getMessage());
            return [];
        }
    }

    public function cancelOrder($orderId, $userId) {
        try {
            $this->conn->beginTransaction();
    
            // Check if order belongs to user and is in cancellable state
            $query = "SELECT trangthai FROM donhang 
                    WHERE iddonhang = :orderId 
                    AND idnguoidung = :userId 
                    AND trangthai IN ('Chờ xác nhận', 'Đã xác nhận')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':orderId' => $orderId, ':userId' => $userId]);
            
            if ($stmt->rowCount() > 0) {
                // Get order details to restore product quantities
                $detailsQuery = "SELECT idsanpham, soluong FROM chitietdonhang 
                               WHERE iddonhang = :orderId";
                $detailsStmt = $this->conn->prepare($detailsQuery);
                $detailsStmt->execute([':orderId' => $orderId]);
                $orderDetails = $detailsStmt->fetchAll(PDO::FETCH_ASSOC);
    
                // Update product quantities
                $updateProductQuery = "UPDATE sanpham 
                                     SET soluong = soluong + :soluong 
                                     WHERE idsanpham = :idsanpham";
                $updateProductStmt = $this->conn->prepare($updateProductQuery);
    
                foreach ($orderDetails as $detail) {
                    $updateProductStmt->execute([
                        ':soluong' => $detail['soluong'],
                        ':idsanpham' => $detail['idsanpham']
                    ]);
                }
    
                // Update order status
                $updateOrderQuery = "UPDATE donhang SET trangthai = 'Đã hủy' 
                                   WHERE iddonhang = :orderId";
                $updateOrderStmt = $this->conn->prepare($updateOrderQuery);
                $updateOrderStmt->execute([':orderId' => $orderId]);
    
                $this->conn->commit();
                return true;
            }
    
            $this->conn->rollBack();
            return false;
        } catch(PDOException $e) {
            $this->conn->rollBack();
            error_log("Error canceling order: " . $e->getMessage());
            return false;
        }
    }




   

    // News connect
    public function getNews() {
        try {
            $query = "SELECT * FROM news";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }


    public function getSelectedCartItems($userId, $selectedItems) {
        try {
            $placeholders = str_repeat('?,', count($selectedItems) - 1) . '?';
            $query = "SELECT c.*, s.tensanpham, s.path_anh_goc, s.giaban, (c.soluong * s.giaban) as thanhtien 
                    FROM giohang c
                    JOIN sanpham s ON c.idsanpham = s.idsanpham
                    WHERE c.idkhachhang = ? AND c.idsanpham IN ($placeholders)";
            
            $stmt = $this->conn->prepare($query);
            $params = array_merge([$userId], $selectedItems);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getSelectedCartItems: " . $e->getMessage());
            return [];
        }
    }

    public function addNews($title, $date, $views, $image, $url, $short_content) {
        try {
            $query = "INSERT INTO news (title, date, views, image, url, short_content) 
                     VALUES (:title, :date, :views, :image, :url, :short_content)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":date", $date);
            $stmt->bindParam(":views", $views);
            $stmt->bindParam(":image", $image);
            $stmt->bindParam(":url", $url);
            $stmt->bindParam(":short_content", $short_content);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function updateNews($id, $title, $date, $views, $image, $url, $short_content) {
        try {
            $query = "UPDATE news 
                     SET title = :title, date = :date, views = :views, 
                         image = :image, url = :url, short_content = :short_content 
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":date", $date);
            $stmt->bindParam(":views", $views);
            $stmt->bindParam(":image", $image);
            $stmt->bindParam(":url", $url);
            $stmt->bindParam(":short_content", $short_content);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function deleteNews($id) {
        try {
            $query = "DELETE FROM news WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    //giỏ hàng
    public function addToCart($idnguoidung, $idsanpham, $soluong) {
        try {
            // Check if user already has a cart
            $query = "SELECT idgiohang FROM giohang WHERE idnguoidung = :idnguoidung LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idnguoidung', $idnguoidung);
            $stmt->execute();
            
            if($stmt->rowCount() == 0) {
                // Create new cart
                $query = "INSERT INTO giohang (idnguoidung) VALUES (:idnguoidung)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':idnguoidung', $idnguoidung);
                $stmt->execute();
                $idgiohang = $this->conn->lastInsertId();
            } else {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $idgiohang = $result['idgiohang'];
            }
            
            // Check if product exists in cart
            $query = "SELECT soluong FROM chitietgiohang 
                     WHERE idgiohang = :idgiohang AND idsanpham = :idsanpham";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idgiohang', $idgiohang);
            $stmt->bindParam(':idsanpham', $idsanpham);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                // Update existing product quantity
                $currentQty = $stmt->fetch(PDO::FETCH_ASSOC)['soluong'];
                $newQty = $currentQty + $soluong;
                
                $query = "UPDATE chitietgiohang 
                         SET soluong = :newQty 
                         WHERE idgiohang = :idgiohang AND idsanpham = :idsanpham";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':newQty', $newQty);
                $stmt->bindParam(':idgiohang', $idgiohang);
                $stmt->bindParam(':idsanpham', $idsanpham);
            } else {
                // Add new product to cart
                $query = "INSERT INTO chitietgiohang (idgiohang, idsanpham, soluong) 
                         VALUES (:idgiohang, :idsanpham, :soluong)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':idgiohang', $idgiohang);
                $stmt->bindParam(':idsanpham', $idsanpham);
                $stmt->bindParam(':soluong', $soluong);
            }
            
            if($stmt->execute()) {
                $this->updateCartTotal($idgiohang);
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Add to cart error: " . $e->getMessage());
            return false;
        }
    }
    
    private function updateCartTotal($idgiohang) {
        try {
            $query = "UPDATE giohang g 
                     SET tongtien = (
                         SELECT SUM(s.gia * c.soluong) 
                         FROM chitietgiohang c 
                         JOIN sanpham s ON c.idsanpham = s.idsanpham 
                         WHERE c.idgiohang = :idgiohang
                     )
                     WHERE g.idgiohang = :idgiohang";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idgiohang', $idgiohang);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Update cart total error: " . $e->getMessage());
            return false;
        }
    }
    //xóa sản phẩm trong giỏ hàng
    public function removesanphamtronggiohang($idnguoidung, $idsanpham) {
        try {
            // Get cart ID
            $query = "SELECT idgiohang 
                     FROM giohang 
                     WHERE idnguoidung = :idnguoidung 
                     LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idnguoidung', $idnguoidung);
            $stmt->execute();
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($cart) {
                // Delete item from chitietgiohang
                $query = "DELETE FROM chitietgiohang 
                         WHERE idgiohang = :idgiohang 
                         AND idsanpham = :idsanpham";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':idgiohang', $cart['idgiohang']);
                $stmt->bindParam(':idsanpham', $idsanpham);
                
                if($stmt->execute()) {
                    // Update total in giohang
                    $query = "UPDATE giohang g 
                             SET tongtien = (
                                 SELECT COALESCE(SUM(c.soluong * c.giaban), 0)
                                 FROM chitietgiohang c
                                 WHERE c.idgiohang = g.idgiohang
                             )
                             WHERE g.idgiohang = :idgiohang";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':idgiohang', $cart['idgiohang']);
                    return $stmt->execute();
                }
            }
            return false;
        } catch(PDOException $e) {
            error_log("Remove cart item error: " . $e->getMessage());
            return false;
        }
    }
    
    //cập nhật ssô lượng sản phẩm trong giỏ hàng
    public function updatesoluongsanpham($idnguoidung, $idsanpham, $soluong) {
        try {
            // Get cart ID
            $query = "SELECT idgiohang FROM giohang WHERE idnguoidung = :idnguoidung LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idnguoidung', $idnguoidung);
            $stmt->execute();
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($cart) {
                // Update quantity in chitietgiohang
                $query = "UPDATE chitietgiohang 
                         SET soluong = :soluong 
                         WHERE idgiohang = :idgiohang AND idsanpham = :idsanpham";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':idgiohang', $cart['idgiohang']);
                $stmt->bindParam(':idsanpham', $idsanpham);
                $stmt->bindParam(':soluong', $soluong);
                
                return $stmt->execute();
            }
            return false;
        } catch(PDOException $e) {
            error_log("Update cart quantity error: " . $e->getMessage());
            return false;
        }
    }

    public function laysoluongsanpham($userId) {
        try {
            $query = "SELECT COUNT(c.idsanpham) as total 
                     FROM giohang g 
                     JOIN chitietgiohang c ON g.idgiohang = c.idgiohang 
                     WHERE g.idnguoidung = :userId";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch(PDOException $e) {
            error_log("Error in laysoluongsanpham: " . $e->getMessage());
            return 0;
        }
    }


    public function createOrder($userId, $orderData, $products, $paymentMethod) {
        try {
            $this->conn->beginTransaction();
    
            // 1. Create payment record
            $paymentSql = "INSERT INTO chitietthanhtoan (phuongthuctt, tongtien, trangthai, magiaodich ) 
                          VALUES (:phuongthuctt, :tongtien, :trangthai, :magiaodich)";
            $stmt = $this->conn->prepare($paymentSql);
            $stmt->execute([
                ':phuongthuctt' => $paymentMethod,
                ':tongtien' => $orderData['ship'],
                ':trangthai' => $paymentMethod === 'paypal' ? 'Đã thanh toán' : 'Chưa thanh toán',
                ':magiaodich' => $paymentMethod === 'paypal' ? ($orderData['payment_details'] ?? '') : '',
                
            ]);
            $idthanhtoan = $this->conn->lastInsertId();
    
            // 2. Create order
            $orderSql = "INSERT INTO donhang (idnguoidung, tennguoidat, sdt, diachigiao, tongtien, 
                                            phuongthuctt, trangthai, idthanhtoan) 
                        VALUES (:userId, :fullname, :phone, :address, :total, 
                                :payment, :trangthai, :idthanhtoan)";
            
            $stmt = $this->conn->prepare($orderSql);
            $stmt->execute([
                ':userId' => $userId,
                ':fullname' => $orderData['fullname'],
                ':phone' => $orderData['phone'],
                ':address' => $orderData['full_address'],
                ':total' => $orderData['total_amount'],
                ':payment' => $paymentMethod,
                ':trangthai' => $orderData['status'],
                ':idthanhtoan' => $idthanhtoan
            ]);
            
            $orderId = $this->conn->lastInsertId();
    
            // 3. Create order details
            $detailSql = "INSERT INTO chitietdonhang (iddonhang, idsanpham, soluong, giaban) 
                          VALUES (:orderId, :productId, :quantity, :price)";
            $stmt = $this->conn->prepare($detailSql);
            
            foreach ($products as $product) {
                $stmt->execute([
                    ':orderId' => $orderId,
                    ':productId' => $product['idsanpham'],
                    ':quantity' => $product['quantity'],
                    ':price' => $product['price']
                ]);
            }
    
            $this->conn->commit();
            return [
                'success' => true, 
                'order_id' => $orderId,
                'payment_id' => $idthanhtoan
            ];
    
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Create order error: " . $e->getMessage());
            return [
                'success' => false, 
                'message' => 'Error creating order: ' . $e->getMessage()
            ];
        }
    }

    // public function createOrder($userId, $orderData, $products, $paymentMethod) {
    //     try {
    //         $this->conn->beginTransaction();
    
    //         // 1. Create payment record first
    //         $paymentSql = "INSERT INTO chitietthanhtoan (phuongthuctt, tongtien, trangthai, magiaodich ) 
    //                       VALUES (:phuongthuctt, :tongtien, :trangthai, :magiaodich)";
    //         $stmt = $this->conn->prepare($paymentSql);
    //         $stmt->execute([
    //             ':phuongthuctt' => $paymentMethod,
    //             ':tongtien' => $orderData['total_amount'],
    //             ':trangthai' => 'Chưa thanh toán',
    //             ':magiaodich' => $paymentMethod === 'cod' ? NULL : '',
    //             
    //         ]);
    //         $idthanhtoan = $this->conn->lastInsertId();
    
    //         // 2. Create order
    //         $orderSql = "INSERT INTO donhang (idnguoidung, tennguoidat, sdt, diachigiao, tongtien, 
    //                                         phuongthuctt, trangthai, idthanhtoan) 
    //                     VALUES (:userId, :fullname, :phone, :address, :total, 
    //                             :payment, :trangthai, :idthanhtoan)";
            
    //         $stmt = $this->conn->prepare($orderSql);
    //         $stmt->execute([
    //             ':userId' => $userId,
    //             ':fullname' => $orderData['fullname'],
    //             ':phone' => $orderData['phone'],
    //             ':address' => $orderData['full_address'],
    //             ':total' => $orderData['total_amount'],
    //             ':payment' => $paymentMethod,
    //             ':trangthai' => 'Chờ xác nhận',
    //             ':idthanhtoan' => $idthanhtoan
    //         ]);
            
    //         $orderId = $this->conn->lastInsertId();
    
    //         // 3. Create order details
    //         foreach ($products as $product) {
    //             $detailSql = "INSERT INTO chitietdonhang (iddonhang, idsanpham, soluong, giaban) 
    //                          VALUES (:orderId, :productId, :quantity, :price)";
                
    //             $stmt = $this->conn->prepare($detailSql);
    //             $stmt->execute([
    //                 ':orderId' => $orderId,
    //                 ':productId' => $product['idsanpham'],
    //                 ':quantity' => $product['quantity'],
    //                 ':price' => $product['price']
    //             ]);
    //         }
    
    //         // 4. Remove items from cart if order is from cart
    //         if ($orderData['type'] === 'cart') {
    //             $cartStmt = $this->conn->prepare("SELECT idgiohang FROM giohang WHERE idnguoidung = ?");
    //             $cartStmt->execute([$userId]);
    //             $cartId = $cartStmt->fetchColumn();
    
    //             if ($cartId) {
    //                 foreach ($products as $product) {
    //                     $deleteStmt = $this->conn->prepare(
    //                         "DELETE FROM chitietgiohang WHERE idgiohang = ? AND idsanpham = ?"
    //                     );
    //                     $deleteStmt->execute([$cartId, $product['idsanpham']]);
    //                 }
    //                 $this->updateCartTotal($cartId);
    //             }
    //         }
    
    //         $this->conn->commit();
    //         return ['success' => true, 'orderId' => $orderId];
    
    //     } catch (Exception $e) {
    //         $this->conn->rollBack();
    //         error_log("Order creation error: " . $e->getMessage());
    //         error_log("Stack trace: " . $e->getTraceAsString());
    //         return ['success' => false, 'message' => 'Không thể tạo đơn hàng: ' . $e->getMessage()];
    //     }
    // }

    //xóa đơn hàng
    public function deleteOrder($orderId) {
        try {
            $this->conn->beginTransaction();
    
            // Check if order is cancelled
            $checkQuery = "SELECT trangthai FROM donhang WHERE iddonhang = :orderId";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':orderId', $orderId);
            $checkStmt->execute();
            $order = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
            if ($order && $order['trangthai'] === 'Đã hủy') {
                // Delete from chitietdonhang first (child table)
                $deleteDetailsQuery = "DELETE FROM chitietdonhang WHERE iddonhang = :orderId";
                $detailsStmt = $this->conn->prepare($deleteDetailsQuery);
                $detailsStmt->bindParam(':orderId', $orderId);
                $detailsStmt->execute();
    
                // Delete the order (parent table)
                $deleteOrderQuery = "DELETE FROM donhang WHERE iddonhang = :orderId";
                $orderStmt = $this->conn->prepare($deleteOrderQuery);
                $orderStmt->bindParam(':orderId', $orderId);
                $orderStmt->execute();
    
                $this->conn->commit();
                return true;
            }
            
            $this->conn->rollBack();
            return false;
        } catch(PDOException $e) {
            $this->conn->rollBack();
            error_log("Error deleting order: " . $e->getMessage());
            return false;
        }
    }

    public function removePurchasedItems($userId, $productIds) {
        try {
            // Get the user's cart ID
            $stmt = $this->conn->prepare("SELECT idgiohang FROM giohang WHERE idnguoidung = ?");
            $stmt->execute([$userId]);
            $cartId = $stmt->fetchColumn();
    
            if (!$cartId) {
                return false;
            }
    
            // Create placeholders for the IN clause
            $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
            
            // Delete items from chitietgiohang
            $sql = "DELETE FROM chitietgiohang 
                    WHERE idgiohang = ? 
                    AND idsanpham IN ($placeholders)";
            
            // Combine cartId with productIds for the execute parameters
            $params = array_merge([$cartId], $productIds);
            
            $stmt = $this->conn->prepare($sql);
            $success = $stmt->execute($params);
    
            if ($success) {
                // Update cart total
                $this->updateCartTotal($cartId);
            }
    
            return $success;
        } catch (PDOException $e) {
            error_log("Error removing purchased items: " . $e->getMessage());
            return false;
        }
    }
    
 

    public function getCartItems($userId) {
        try {
            $query = "SELECT g.idgiohang, g.idnguoidung, c.idsanpham, c.soluong, 
                        s.tensanpham, s.path_anh_goc, s.giaban, 
                        k.gia_giam, k.ngaybatdau, k.ngayketthuc,
                        CASE 
                            WHEN k.gia_giam IS NOT NULL 
                                AND k.ngaybatdau <= NOW() 
                                AND k.ngayketthuc >= NOW() 
                            THEN s.giaban - k.gia_giam
                            ELSE s.giaban 
                        END as gia_sau_giam,
                        c.soluong * (
                            CASE 
                                WHEN k.gia_giam IS NOT NULL 
                                    AND k.ngaybatdau <= NOW() 
                                    AND k.ngayketthuc >= NOW() 
                                THEN s.giaban - k.gia_giam
                                ELSE s.giaban 
                            END
                        ) as thanhtien
                    FROM giohang g
                    INNER JOIN chitietgiohang c ON g.idgiohang = c.idgiohang
                    INNER JOIN sanpham s ON c.idsanpham = s.idsanpham
                    LEFT JOIN khuyenmai k ON s.idkhuyenmai = k.idkhuyenmai
                    WHERE g.idnguoidung = :userId
                    AND s.trangthai = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getCartItems: " . $e->getMessage());
            return [];
        }
    }



    //trang admin
    //tính tổng lợi nhuận
    public function calculateProfit($month = null, $year = null) {
        try {
            $query = "SELECT SUM(cd.giaban * cd.soluong - s.gianhap * cd.soluong) as total_profit
                      FROM chitietdonhang cd
                      JOIN sanpham s ON cd.idsanpham = s.idsanpham
                      JOIN donhang d ON cd.iddonhang = d.iddonhang
                      WHERE d.trangthai = 'Hoàn thành'";
            
            // Add date filters if provided
            if ($month !== null && $year !== null) {
                $query .= " AND MONTH(d.ngaydat) = :month AND YEAR(d.ngaydat) = :year";
            } else if ($year !== null) {
                $query .= " AND YEAR(d.ngaydat) = :year";
            }
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters if provided
            if ($month !== null && $year !== null) {
                $stmt->bindParam(':month', $month, PDO::PARAM_INT);
                $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            } else if ($year !== null) {
                $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total_profit'] ?? 0;
        } catch(PDOException $e) {
            error_log("Error calculating profit: " . $e->getMessage());
            return 0;
        }
    }


    //tính tổng bán ra
    public function calculateRevenue($month = null, $year = null) {
        try {
            $query = "SELECT SUM(cd.giaban * cd.soluong) as total_revenue
                      FROM chitietdonhang cd
                      JOIN donhang d ON cd.iddonhang = d.iddonhang
                      WHERE d.trangthai = 'Hoàn thành'";
            
            // Add date filters if provided
            if ($month !== null && $year !== null) {
                $query .= " AND MONTH(d.ngaydat) = :month AND YEAR(d.ngaydat) = :year";
            } else if ($year !== null) {
                $query .= " AND YEAR(d.ngaydat) = :year";
            }
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters if provided
            if ($month !== null && $year !== null) {
                $stmt->bindParam(':month', $month, PDO::PARAM_INT);
                $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            } else if ($year !== null) {
                $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total_revenue'] ?? 0;
        } catch(PDOException $e) {
            error_log("Error calculating revenue: " . $e->getMessage());
            return 0;
        }
    }

    // lấy sản phẩm bán chạy nhất
    public function getTopSellingProducts($limit = 5, $month = null, $year = null) {
        try {
            $query = "SELECT s.idsanpham, s.tensanpham, s.path_anh_goc, s.giaban, s.soluong,
                      SUM(cd.soluong) as total_sold,
                      SUM(cd.giaban * cd.soluong) as total_revenue
                      FROM chitietdonhang cd
                      JOIN sanpham s ON cd.idsanpham = s.idsanpham
                      JOIN donhang d ON cd.iddonhang = d.iddonhang
                      WHERE d.trangthai = 'Hoàn thành'";
            
            // Add date filters if provided
            if ($month !== null && $year !== null) {
                $query .= " AND MONTH(d.ngaydat) = :month AND YEAR(d.ngaydat) = :year";
            } else if ($year !== null) {
                $query .= " AND YEAR(d.ngaydat) = :year";
            }
            
            $query .= " GROUP BY s.idsanpham
                      ORDER BY total_sold DESC
                      LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            
            // Bind parameters if provided
            if ($month !== null && $year !== null) {
                $stmt->bindParam(':month', $month, PDO::PARAM_INT);
                $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            } else if ($year !== null) {
                $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting top selling products: " . $e->getMessage());
            return [];
        }
    }

    // lấy danh sách sản phẩm sắp hết hàng
    public function getLowStockProducts($threshold = 5, $limit = 8) {
        try {
            $query = "SELECT s.idsanpham, s.tensanpham, s.path_anh_goc, s.giaban, s.soluong,
                      d.tendanhmuc
                      FROM sanpham s
                      LEFT JOIN danhmuc d ON s.iddanhmuc = d.iddanhmuc
                      WHERE s.soluong <= :threshold AND s.trangthai = 1
                      ORDER BY s.soluong ASC
                      LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':threshold', $threshold, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting low stock products: " . $e->getMessage());
            return [];
        }
    }
    //top sản phẩm có doanh thu cao nhất
    public function getTopProfitProducts($limit = 5) {
        try {
            $query = "SELECT s.idsanpham, s.tensanpham, s.path_anh_goc,
                      SUM(cd.soluong) as total_sold,
                      SUM(cd.giaban * cd.soluong) as total_revenue,
                      SUM(cd.giaban * cd.soluong - s.gianhap * cd.soluong) as total_profit
                      FROM chitietdonhang cd
                      JOIN sanpham s ON cd.idsanpham = s.idsanpham
                      JOIN donhang d ON cd.iddonhang = d.iddonhang
                      WHERE d.trangthai = 'Đã giao hàng'
                      GROUP BY s.idsanpham
                      ORDER BY total_profit DESC
                      LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting top profit products: " . $e->getMessage());
            return [];
        }
    }

    
    //lấy các bình luận
        
        public function getProductReviews($productId) {
            try {
                $query = "SELECT d.*, u.tendangnhap
                          FROM danhgia d
                          JOIN nguoidung u ON d.idnguoidung = u.idnguoidung
                          WHERE d.idsanpham = :productId 
                          AND d.trangthai = 1
                          ORDER BY d.ngaytao DESC";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                error_log("Error getting product reviews: " . $e->getMessage());
                return [];
            }
        }
        
        // tinh số sao
        public function getProductAverageRating($productId) {
            try {
                $query = "SELECT AVG(sosao) as average_rating, COUNT(*) as review_count 
                          FROM danhgia 
                          WHERE idsanpham = :productId 
                          AND trangthai = 1";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                error_log("Error getting average rating: " . $e->getMessage());
                return ['average_rating' => 0, 'review_count' => 0];
            }
        }
        
        // kiểm tra bình luận chưa
        public function hasUserReviewed($userId, $productId) {
            try {
                $query = "SELECT COUNT(*) as count 
                          FROM danhgia 
                          WHERE idnguoidung = :userId 
                          AND idsanpham = :productId";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['count'] > 0;
            } catch(PDOException $e) {
                error_log("Error checking if user reviewed: " . $e->getMessage());
                return false;
            }
        }
        
        // thêm bình luận
        public function addProductReview($productId, $userId, $rating, $content) {
            try {
                $query = "INSERT INTO danhgia (idsanpham, idnguoidung, sosao, noidung, trangthai) 
                          VALUES (:productId, :userId, :rating, :content, 1)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
                $stmt->bindParam(':content', $content, PDO::PARAM_STR);
                return $stmt->execute();
            } catch(PDOException $e) {
                error_log("Error adding review: " . $e->getMessage());
                return false;
            }
        }

        public function getAllReviews() {
            try {
                $query = "SELECT d.*, s.tensanpham, u.tendangnhap
                          FROM danhgia d
                          LEFT JOIN sanpham s ON d.idsanpham = s.idsanpham
                          LEFT JOIN nguoidung u ON d.idnguoidung = u.idnguoidung
                          ORDER BY d.ngaytao DESC";
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Add fallback values for NULL fields
                foreach ($reviews as &$review) {
                    if (!isset($review['tensanpham']) || $review['tensanpham'] === null) {
                        $review['tensanpham'] = 'Sản phẩm đã xóa';
                    }
                    if (!isset($review['hoten']) || $review['hoten'] === null) {
                        $review['hoten'] = 'Người dùng đã xóa';
                    }
                }
                
                return $reviews;
            } catch(PDOException $e) {
                error_log("Error getting all reviews: " . $e->getMessage());
                return [];
            }
        }
        
        // Delete a review
        public function deleteReview($reviewId) {
            try {
                $query = "DELETE FROM danhgia WHERE iddanhgia = :reviewId";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':reviewId', $reviewId, PDO::PARAM_INT);
                return $stmt->execute();
            } catch(PDOException $e) {
                error_log("Error deleting review: " . $e->getMessage());
                return false;
            }
        }
        
        // Toggle review status (show/hide)
        public function toggleReviewStatus($reviewId) {
            try {
                $query = "UPDATE danhgia SET trangthai = NOT trangthai WHERE iddanhgia = :reviewId";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':reviewId', $reviewId, PDO::PARAM_INT);
                return $stmt->execute();
            } catch(PDOException $e) {
                error_log("Error toggling review status: " . $e->getMessage());
                return false;
            }
        }



    public function checkPermission($userId, $requiredRole) {
        try {
            $query = "SELECT role FROM nguoidung WHERE idnguoidung = :userId LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return $user['role'] >= $requiredRole;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Permission check error: " . $e->getMessage());
            return false;
        }
    }
    
    // Product management functions
    public function getAllProducts($search = '', $category = '', $sort = '', $order = 'ASC') {
        try {
            $query = "SELECT s.*, d.tendanhmuc 
                      FROM sanpham s
                      LEFT JOIN danhmuc d ON s.iddanhmuc = d.iddanhmuc
                      WHERE 1=1";
            
            $params = [];
            
            // Add search condition if provided
            if (!empty($search)) {
                $query .= " AND (s.tensanpham LIKE :search OR s.mota LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            // Add category filter if provided
            if (!empty($category)) {
                $query .= " AND s.iddanhmuc = :category";
                $params[':category'] = $category;
            }
            
            // Add sorting
            if (!empty($sort)) {
                $query .= " ORDER BY $sort $order";
            } else {
                $query .= " ORDER BY s.ngaytao DESC";
            }
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting all products: " . $e->getMessage());
            return [];
        }
    }

    //lấy thôngn tin sản phẩm
    public function getProductById($id) {
        try {
            $query = "SELECT s.*, d.tendanhmuc 
                      FROM sanpham s
                      LEFT JOIN danhmuc d ON s.iddanhmuc = d.iddanhmuc
                      WHERE s.idsanpham = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product) {
                // Get additional images
                $product['additional_images'] = $this->getProductImages($id);
            }
            
            return $product;
        } catch(PDOException $e) {
            error_log("Error getting product by ID: " . $e->getMessage());
            return null;
        }
    }

    //thêm san phẩm mới
    public function addProduct($data) {
        try {
            $query = "INSERT INTO sanpham (
                tensanpham, mota, giaban, gianhap, soluong, iddanhmuc, 
                loaiday, loaimay, gioitinh, path_anh_goc, trangthai, idkhuyenmai,
                bosuutap, chatlieuvo, matkinh, mausac, kichthuoc, doday,
                chongnuoc, tinhnangdacbiet, chinhsachbaohanh, idnhacungcap, ngaytao
            ) VALUES (
                :tensanpham, :mota, :giaban, :gianhap, :soluong, :iddanhmuc,
                :loaiday, :loaimay, :gioitinh, :path_anh_goc, :trangthai, :idkhuyenmai,
                :bosuutap, :chatlieuvo, :matkinh, :mausac, :kichthuoc, :doday,
                :chongnuoc, :tinhnangdacbiet, :chinhsachbaohanh, :idnhacungcap, NOW()
            )";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind all parameters
            $stmt->bindParam(':tensanpham', $data['tensanpham']);
            $stmt->bindParam(':mota', $data['mota']);
            $stmt->bindParam(':giaban', $data['giaban']);
            $stmt->bindParam(':gianhap', $data['gianhap']);
            $stmt->bindParam(':soluong', $data['soluong']);
            $stmt->bindParam(':iddanhmuc', $data['iddanhmuc']);
            $stmt->bindParam(':loaiday', $data['loaiday']);
            $stmt->bindParam(':loaimay', $data['loaimay']);
            $stmt->bindParam(':gioitinh', $data['gioitinh']);
            $stmt->bindParam(':path_anh_goc', $data['path_anh_goc']);
            $stmt->bindParam(':trangthai', $data['trangthai']);
            $stmt->bindParam(':idkhuyenmai', $data['idkhuyenmai']);
            $stmt->bindParam(':bosuutap', $data['bosuutap']);
            $stmt->bindParam(':chatlieuvo', $data['chatlieuvo']);
            $stmt->bindParam(':matkinh', $data['matkinh']);
            $stmt->bindParam(':mausac', $data['mausac']);
            $stmt->bindParam(':kichthuoc', $data['kichthuoc']);
            $stmt->bindParam(':doday', $data['doday']);
            $stmt->bindParam(':chongnuoc', $data['chongnuoc']);
            $stmt->bindParam(':tinhnangdacbiet', $data['tinhnangdacbiet']);
            $stmt->bindParam(':chinhsachbaohanh', $data['chinhsachbaohanh']);
            $stmt->bindParam(':idnhacungcap', $data['idnhacungcap']);
            
            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            error_log("Error adding product: " . $e->getMessage());
            return false;
        }
    }

    //cập nhật sản phẩm
    public function updateProduct($productId, $productData) {
        try {
            $this->conn->beginTransaction();
    
            $query = "UPDATE sanpham SET 
                tensanpham = :tensanpham,
                mota = :mota,
                giaban = :giaban,
                gianhap = :gianhap,
                soluong = :soluong,
                iddanhmuc = :iddanhmuc,
                loaiday = :loaiday,
                loaimay = :loaimay,
                gioitinh = :gioitinh,
                trangthai = :trangthai,
                idkhuyenmai = :idkhuyenmai,
                chinhsachbaohanh = :chinhsachbaohanh,
                bosuutap = :bosuutap,
                chatlieuvo = :chatlieuvo,
                matkinh = :matkinh,
                mausac = :mausac,
                kichthuoc = :kichthuoc,
                doday = :doday,
                chongnuoc = :chongnuoc,
                tinhnangdacbiet = :tinhnangdacbiet,
                idnhacungcap = :idnhacungcap";
    
            // Add path_anh_goc to update only if it exists in productData
            if (isset($productData['path_anh_goc'])) {
                $query .= ", path_anh_goc = :path_anh_goc";
            }
    
            $query .= " WHERE idsanpham = :idsanpham";
    
            $stmt = $this->conn->prepare($query);
    
            // Bind all parameters
            $stmt->bindParam(':tensanpham', $productData['tensanpham']);
            $stmt->bindParam(':mota', $productData['mota']);
            $stmt->bindParam(':giaban', $productData['giaban']);
            $stmt->bindParam(':gianhap', $productData['gianhap']);
            $stmt->bindParam(':soluong', $productData['soluong']);
            $stmt->bindParam(':iddanhmuc', $productData['iddanhmuc']);
            $stmt->bindParam(':loaiday', $productData['loaiday']);
            $stmt->bindParam(':loaimay', $productData['loaimay']);
            $stmt->bindParam(':gioitinh', $productData['gioitinh']);
            $stmt->bindParam(':trangthai', $productData['trangthai']);
            $stmt->bindParam(':idkhuyenmai', $productData['idkhuyenmai']);
            $stmt->bindParam(':chinhsachbaohanh', $productData['chinhsachbaohanh']);
            $stmt->bindParam(':bosuutap', $productData['bosuutap']);
            $stmt->bindParam(':chatlieuvo', $productData['chatlieuvo']);
            $stmt->bindParam(':matkinh', $productData['matkinh']);
            $stmt->bindParam(':mausac', $productData['mausac']);
            $stmt->bindParam(':kichthuoc', $productData['kichthuoc']);
            $stmt->bindParam(':doday', $productData['doday']);
            $stmt->bindParam(':chongnuoc', $productData['chongnuoc']);
            $stmt->bindParam(':tinhnangdacbiet', $productData['tinhnangdacbiet']);
            $stmt->bindParam(':idnhacungcap', $productData['idnhacungcap']);
            $stmt->bindParam(':idsanpham', $productId);
    
            // Bind path_anh_goc if it exists
            if (isset($productData['path_anh_goc'])) {
                $stmt->bindParam(':path_anh_goc', $productData['path_anh_goc']);
            }
    
            $result = $stmt->execute();
            
            if ($result) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch(PDOException $e) {
            $this->conn->rollBack();
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }

    //xóa sản phẩm
    public function deleteProduct($id) {
        try {
            $this->conn->beginTransaction();

            // kiểm tra xem sản phẩm có trong đơn hàng chưa
            $query = "SELECT COUNT(*) as count FROM chitietdonhang WHERE idsanpham = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                // Product is in orders, just update status to inactive
                $query = "UPDATE sanpham SET trangthai = 0 WHERE idsanpham = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $success = $stmt->execute();
            } else {
                // Delete additional images first
                $this->deleteProductImages($id);
                
                // Then delete the product
                $query = "DELETE FROM sanpham WHERE idsanpham = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $success = $stmt->execute();
            }

            $this->conn->commit();
            return $success;
        } catch(PDOException $e) {
            $this->conn->rollBack();
            error_log("Error deleting product: " . $e->getMessage());
            return false;
        }
    }

    //lấy tất cả hình ảnh sản phẩm
    public function getProductImages($productId) {
        try {
            $query = "SELECT * FROM hinhanhsanpham WHERE idsanpham = :productId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':productId', $productId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting product images: " . $e->getMessage());
            return [];
        }
    }

    //thêm hình ảnh phụ sản phẩm
    public function addProductImages($productId, $imagePaths) {
        try {
            $query = "INSERT INTO hinhanhsanpham (idsanpham, duongdan) VALUES (:productId, :path)";
            $stmt = $this->conn->prepare($query);
            
            foreach ($imagePaths as $path) {
                $stmt->execute([
                    ':productId' => $productId,
                    ':path' => $path
                ]);
            }
            return true;
        } catch(PDOException $e) {
            error_log("Error adding product images: " . $e->getMessage());
            return false;
        }
    }

    //xóa hình ảnh phụ sản phẩm
    public function deleteProductImages($productId) {
        try {
            $query = "DELETE FROM hinhanhsanpham WHERE idsanpham = :productId";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([':productId' => $productId]);
        } catch(PDOException $e) {
            error_log("Error deleting product images: " . $e->getMessage());
            return false;
        }
    }
    // lấy tất cả bộ suu tập
    public function getAllSuppliers($search = '') {
        try {
            $query = "SELECT * FROM nhacungcap WHERE 1=1";
            $params = [];
    
            if (!empty($search)) {
                $query .= " AND tennhacungcap LIKE :search";
                $params[':search'] = "%$search%";
            }
    
            $query .= " ORDER BY idnhacungcap ASC";
            
            $stmt = $this->conn->prepare($query);
            
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting suppliers: " . $e->getMessage());
            return [];
        }
    }
    
    public function supplierNameExists($tenNhaCungCap, $excludeId = null) {
        try {
            $query = "SELECT COUNT(*) FROM nhacungcap WHERE tennhacungcap = :tennhacungcap";
            if ($excludeId !== null) {
                $query .= " AND idnhacungcap != :excludeId";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':tennhacungcap', $tenNhaCungCap);
            if ($excludeId !== null) {
                $stmt->bindParam(':excludeId', $excludeId);
            }
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        } catch(PDOException $e) {
            error_log("Error checking supplier name: " . $e->getMessage());
            return false;
        }
    }
    
    public function addSupplier($data) {
        try {
            // Check if supplier name already exists
            if ($this->supplierNameExists($data['tennhacungcap'])) {
                throw new Exception("Tên nhà cung cấp đã tồn tại!");
            }
    
            $query = "INSERT INTO nhacungcap (tennhacungcap, diachi, sdt) 
                      VALUES (:tennhacungcap, :diachi, :sdt)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':tennhacungcap', $data['tennhacungcap']);
            $stmt->bindParam(':diachi', $data['diachi']);
            $stmt->bindParam(':sdt', $data['sdt']);
            return $stmt->execute();
        } catch(Exception $e) {
            error_log("Error adding supplier: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function updateSupplier($data) {
        try {
            // Check if supplier name already exists (excluding current supplier)
            if ($this->supplierNameExists($data['tennhacungcap'], $data['idnhacungcap'])) {
                throw new Exception("Tên nhà cung cấp đã tồn tại!");
            }
    
            $query = "UPDATE nhacungcap 
                      SET tennhacungcap = :tennhacungcap, 
                          diachi = :diachi, 
                          sdt = :sdt 
                      WHERE idnhacungcap = :idnhacungcap";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idnhacungcap', $data['idnhacungcap']);
            $stmt->bindParam(':tennhacungcap', $data['tennhacungcap']);
            $stmt->bindParam(':diachi', $data['diachi']);
            $stmt->bindParam(':sdt', $data['sdt']);
            return $stmt->execute();
        } catch(Exception $e) {
            error_log("Error updating supplier: " . $e->getMessage());
            throw $e;
        }
    }
    
    // public function deleteSupplier($id) {
    //     try {
    //         $query = "DELETE FROM nhacungcap WHERE idnhacungcap = :idnhacungcap";
    //         $stmt = $this->conn->prepare($query);
    //         $stmt->bindParam(':idnhacungcap', $id);
    //         return $stmt->execute();
    //     } catch(PDOException $e) {
    //         error_log("Error deleting supplier: " . $e->getMessage());
    //         return false;
    //     }
    // }
    //lấy chinh sach bao hanh
    public function getAllWarrantyPolicies() {
        try {
            $query = "SELECT * FROM chinhsachbaohanh ORDER BY ngay_tao DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting warranty policies: " . $e->getMessage());
            return [];
        }
    }

    //
    public function deleteProductImage($imageId) {
        try {
            $this->conn->beginTransaction();
            
            // Get the image path first
            $query = "SELECT duongdan FROM hinhanhsanpham WHERE idhinhanh = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $imageId, PDO::PARAM_INT);
            $stmt->execute();
            $image = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($image) {
                // Delete the physical file
                $filePath = __DIR__ . '/' . $image['duongdan'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                
                // Delete from database
                $query = "DELETE FROM hinhanhsanpham WHERE idhinhanh = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $imageId, PDO::PARAM_INT);
                $result = $stmt->execute();
                
                $this->conn->commit();
                return $result;
            }
            
            $this->conn->rollBack();
            return false;
        } catch(PDOException $e) {
            $this->conn->rollBack();
            error_log("Error deleting product image: " . $e->getMessage());
            return false;
        }
    }
    
    
    public function getWatchTypes() {
        try {
            $query = "SELECT * FROM loaimay ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }
    
    public function getAllWatchTypesAdmin($search = '') {
        try {
            $query = "SELECT * FROM loaimay WHERE 1=1"; // Changed this line
            $params = [];
    
            if (!empty($search)) {
                $query .= " AND ten_loai_may LIKE :search";
                $params[':search'] = "%$search%";
            }
    
            $query .= " ORDER BY id_loai_may ASC";
            
            $stmt = $this->conn->prepare($query);
            
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting watch types: " . $e->getMessage());
            return [];
        }
    }
    public function getMonthlyReports($month, $year) {
        try {
            $query = "SELECT * FROM baocao 
                      WHERE MONTH(ngaytao) = :month 
                      AND YEAR(ngaytao) = :year 
                      ORDER BY ngaytao DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':month', $month, PDO::PARAM_INT);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting monthly reports: " . $e->getMessage());
            return [];
        }
    }

    public function getRevenueAndProfit($month, $year) {
        try {
            $query = "SELECT 
                        COALESCE(SUM(cd.giaban * cd.soluong), 0) as doanh_thu,
                        COALESCE(SUM((cd.giaban - sp.gianhap) * cd.soluong), 0) as loi_nhuan
                    FROM donhang d
                    JOIN chitietdonhang cd ON d.iddonhang = cd.iddonhang
                    JOIN sanpham sp ON cd.idsanpham = sp.idsanpham
                    WHERE MONTH(d.ngaytao) = :month 
                    AND YEAR(d.ngaytao) = :year
                    AND d.trangthai = 'Hoàn thành'";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':month', $month, PDO::PARAM_INT);
            $stmt->bindValue(':year', $year, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new Exception("Query execution failed");
            }
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: [
                'doanh_thu' => 0,
                'loi_nhuan' => 0
            ];
        } catch(Exception $e) {
            error_log("Error in getRevenueAndProfit: " . $e->getMessage());
            return [
                'doanh_thu' => 0,
                'loi_nhuan' => 0
            ];
        }
    }
    
    public function getTopSellingProductsthongke($year, $limit = 5) {
        try {
            $query = "SELECT 
                        sp.tensanpham,
                        SUM(cd.soluong) as total_quantity,
                        SUM(cd.giaban * cd.soluong) as total_revenue
                    FROM chitietdonhang cd
                    JOIN sanpham sp ON cd.idsanpham = sp.idsanpham
                    JOIN donhang d ON cd.iddonhang = d.iddonhang
                    WHERE YEAR(d.ngaydat) = :year
                    AND d.trangthai = 'Hoàn thành'
                    GROUP BY sp.idsanpham, sp.tensanpham
                    ORDER BY total_quantity DESC
                    LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':year', $year, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getTopSellingProducts: " . $e->getMessage());
            return [];
        }
    }
    
    public function getCategoryStats($year) {
        try {
            $query = "SELECT 
                        dm.tendanhmuc,
                        COUNT(DISTINCT d.iddonhang) as so_don_hang,
                        SUM(cd.soluong) as so_san_pham,
                        SUM(cd.giaban * cd.soluong) as doanh_thu
                    FROM chitietdonhang cd
                    JOIN sanpham sp ON cd.idsanpham = sp.idsanpham
                    JOIN danhmuc dm ON sp.iddanhmuc = dm.iddanhmuc
                    JOIN donhang d ON cd.iddonhang = d.iddonhang
                    WHERE YEAR(d.ngaydat) = :year
                    AND d.trangthai = 'Hoàn thành'
                    GROUP BY dm.iddanhmuc, dm.tendanhmuc
                    ORDER BY doanh_thu DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':year', $year, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getCategoryStats: " . $e->getMessage());
            return [];
        }
    }

    public function saveChat($userId, $message, $role, $time) {
        try {
            $query = "INSERT INTO chatbox (idnguoidung, noidungchat, role, thoigian) 
                     VALUES (:idnguoidung, :noidungchat, :role, :thoigian)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idnguoidung', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':noidungchat', $message, PDO::PARAM_STR);
            $stmt->bindParam(':role', $role, PDO::PARAM_INT);
            $stmt->bindParam(':thoigian', $time);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error saving chat: " . $e->getMessage());
            return false;
        }
    }
    
    public function getChatHistory($userId) {
        try {
            $query = "SELECT * FROM chatbox 
                     WHERE idnguoidung = :idnguoidung 
                     ORDER BY thoigian ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idnguoidung', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting chat history: " . $e->getMessage());
            return [];
        }
    }

    public function getChatUsers() {
        try {
            $query = "SELECT DISTINCT n.*, 
                        (SELECT thoigian 
                         FROM chatbox 
                         WHERE idnguoidung = n.idnguoidung 
                         ORDER BY thoigian DESC 
                         LIMIT 1) as last_message
                     FROM nguoidung n
                     INNER JOIN chatbox c ON n.idnguoidung = c.idnguoidung
                     WHERE n.trangthai = 1
                     ORDER BY last_message DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting chat users: " . $e->getMessage());
            return [];
        }
    }

 
    

    //lấy tất cả loại máy
    public function getAllWatchTypes($search = '') {
        try {
            $query = "SELECT * FROM loaimay WHERE trangthai=1";
            $params = [];
    
            if (!empty($search)) {
                $query .= " AND ten_loai_may LIKE :search";
                $params[':search'] = "%$search%";
            }
    
            $query .= " ORDER BY id_loai_may ASC";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind search parameter if it exists
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting watch types: " . $e->getMessage());
            return [];
        }
    }

    public function watchTypeExists($tenLoaiMay, $excludeId = null) {
        try {
            $query = "SELECT COUNT(*) FROM loaimay WHERE ten_loai_may = :ten_loai_may";
            if ($excludeId !== null) {
                $query .= " AND id_loai_may != :excludeId";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':ten_loai_may', $tenLoaiMay);
            if ($excludeId !== null) {
                $stmt->bindParam(':excludeId', $excludeId);
            }
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        } catch(PDOException $e) {
            error_log("Error checking watch type existence: " . $e->getMessage());
            return false;
        }
    }
    
    public function addWatchType($data) {
        try {
            // Check if watch type name already exists
            if ($this->watchTypeExists($data['ten_loai_may'])) {
                throw new Exception("Tên loại máy đã tồn tại!");
            }
    
            $query = "INSERT INTO loaimay (ten_loai_may, mo_ta_loai_may, trangthai) 
                      VALUES (:ten_loai_may, :mo_ta_loai_may, :trangthai)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':ten_loai_may', $data['ten_loai_may']);
            $stmt->bindParam(':mo_ta_loai_may', $data['mo_ta_loai_may']);
            $stmt->bindParam(':trangthai', $data['trangthai']);
            return $stmt->execute();
        } catch(Exception $e) {
            error_log("Error adding watch type: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function updateWatchType($data) {
        try {
            // Check if watch type name already exists (excluding current watch type)
            if ($this->watchTypeExists($data['ten_loai_may'], $data['id_loai_may'])) {
                throw new Exception("Tên loại máy đã tồn tại!");
            }
    
            $query = "UPDATE loaimay 
                      SET ten_loai_may = :ten_loai_may, 
                          mo_ta_loai_may = :mo_ta_loai_may, 
                          trangthai = :trangthai 
                      WHERE id_loai_may = :id_loai_may";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_loai_may', $data['id_loai_may']);
            $stmt->bindParam(':ten_loai_may', $data['ten_loai_may']);
            $stmt->bindParam(':mo_ta_loai_may', $data['mo_ta_loai_may']);
            $stmt->bindParam(':trangthai', $data['trangthai']);
            return $stmt->execute();
        } catch(Exception $e) {
            error_log("Error updating watch type: " . $e->getMessage());
            throw $e;
        }
    }


    //xóa loại máy
    public function deleteWatchType($id) {
        try {
            $query = "DELETE FROM loaimay WHERE id_loai_may = :id_loai_may";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_loai_may', $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error deleting watch type: " . $e->getMessage());
            return false;
        }
    }

    //lấy tất cả loại dây
    public function getAllStrapTypes($search = '') {
        try {
            $query = "SELECT * FROM loaiday WHERE 1=1";
            $params = [];
    
            if (!empty($search)) {
                $query .= " AND ten_loai_day LIKE :search";
                $params[':search'] = "%$search%";
            }
    
            $query .= " ORDER BY id_loai_day ASC";
            
            $stmt = $this->conn->prepare($query);
            
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting strap types: " . $e->getMessage());
            return [];
        }
    }


    public function strapTypeExists($tenLoaiDay, $excludeId = null) {
        try {
            $query = "SELECT COUNT(*) FROM loaiday WHERE ten_loai_day = :ten_loai_day";
            if ($excludeId !== null) {
                $query .= " AND id_loai_day != :excludeId";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':ten_loai_day', $tenLoaiDay);
            if ($excludeId !== null) {
                $stmt->bindParam(':excludeId', $excludeId);
            }
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        } catch(PDOException $e) {
            error_log("Error checking strap type existence: " . $e->getMessage());
            return false;
        }
    }
    
    public function addStrapType($data) {
        try {
            // Check if strap type name already exists
            if ($this->strapTypeExists($data['ten_loai_day'])) {
                throw new Exception("Tên loại dây đã tồn tại!");
            }
    
            $query = "INSERT INTO loaiday (ten_loai_day, mo_ta_loai_day, trangthai) 
                      VALUES (:ten_loai_day, :mo_ta_loai_day, :trangthai)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':ten_loai_day', $data['ten_loai_day']);
            $stmt->bindParam(':mo_ta_loai_day', $data['mo_ta_loai_day']);
            $stmt->bindParam(':trangthai', $data['trangthai']);
            return $stmt->execute();
        } catch(Exception $e) {
            error_log("Error adding strap type: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function updateStrapType($data) {
        try {
            // Check if strap type name already exists (excluding current strap type)
            if ($this->strapTypeExists($data['ten_loai_day'], $data['id_loai_day'])) {
                throw new Exception("Tên loại dây đã tồn tại!");
            }
    
            $query = "UPDATE loaiday 
                      SET ten_loai_day = :ten_loai_day, 
                          mo_ta_loai_day = :mo_ta_loai_day, 
                          trangthai = :trangthai 
                      WHERE id_loai_day = :id_loai_day";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_loai_day', $data['id_loai_day']);
            $stmt->bindParam(':ten_loai_day', $data['ten_loai_day']);
            $stmt->bindParam(':mo_ta_loai_day', $data['mo_ta_loai_day']);
            $stmt->bindParam(':trangthai', $data['trangthai']);
            return $stmt->execute();
        } catch(Exception $e) {
            error_log("Error updating strap type: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function deleteStrapType($id) {
        try {
            $query = "DELETE FROM loaiday WHERE id_loai_day = :id_loai_day";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_loai_day', $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error deleting strap type: " . $e->getMessage());
            return false;
        }
    }
    public function getAllPromotionsadmin($search = '') {
        try {
            $query = "SELECT * FROM khuyenmai WHERE 1=1";
            $params = [];
    
            if (!empty($search)) {
                $query .= " AND tenkhuyenmai LIKE :search";
                $params[':search'] = "%$search%";
            }
    
            $query .= " ORDER BY ngaytao DESC";
            
            $stmt = $this->conn->prepare($query);
            
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting promotions: " . $e->getMessage());
            return [];
        }
    }

    public function addPromotion($data) {
        try {
            $query = "INSERT INTO khuyenmai (tenkhuyenmai, gia_giam, ngaybatdau, ngayketthuc) 
                      VALUES (:tenkhuyenmai, :gia_giam, :ngaybatdau, :ngayketthuc)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':tenkhuyenmai', $data['tenkhuyenmai']);
            $stmt->bindParam(':gia_giam', $data['gia_giam']);
            $stmt->bindParam(':ngaybatdau', $data['ngaybatdau']);
            $stmt->bindParam(':ngayketthuc', $data['ngayketthuc']);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error adding promotion: " . $e->getMessage());
            return false;
        }
    }

    public function updatePromotion($data) {
        try {
            $query = "UPDATE khuyenmai 
                      SET tenkhuyenmai = :tenkhuyenmai,
                          gia_giam = :gia_giam,
                          ngaybatdau = :ngaybatdau,
                          ngayketthuc = :ngayketthuc
                      WHERE idkhuyenmai = :idkhuyenmai";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idkhuyenmai', $data['idkhuyenmai']);
            $stmt->bindParam(':tenkhuyenmai', $data['tenkhuyenmai']);
            $stmt->bindParam(':gia_giam', $data['gia_giam']);
            $stmt->bindParam(':ngaybatdau', $data['ngaybatdau']);
            $stmt->bindParam(':ngayketthuc', $data['ngayketthuc']);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating promotion: " . $e->getMessage());
            return false;
        }
    }

    public function deletePromotion($id) {
        try {
            $query = "DELETE FROM khuyenmai WHERE idkhuyenmai = :idkhuyenmai";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idkhuyenmai', $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error deleting promotion: " . $e->getMessage());
            return false;
        }
    }

    public function getPromotionById($id) {
        try {
            $query = "SELECT * FROM khuyenmai WHERE idkhuyenmai = :idkhuyenmai";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idkhuyenmai', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting promotion: " . $e->getMessage());
            return null;
        }
    }

    //lấy các khuyến mãi
    public function getAllPromotions() {
        try {
            $query = "SELECT * FROM khuyenmai ORDER BY ngaytao DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting promotions: " . $e->getMessage());
            return [];
        }
    }
    
    // Category management functions
    public function getAllCategories() {
        try {
            $query = "SELECT * FROM danhmuc ORDER BY tendanhmuc";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getAllCategories: " . $e->getMessage());
            return [];
        }
    }
    
    //thêm mới danh mục
    public function categoryExists($tendanhmuc, $excludeId = null) {
        try {
            $query = "SELECT COUNT(*) FROM danhmuc WHERE tendanhmuc = :tendanhmuc";
            if ($excludeId !== null) {
                $query .= " AND iddanhmuc != :excludeId";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':tendanhmuc', $tendanhmuc);
            if ($excludeId !== null) {
                $stmt->bindParam(':excludeId', $excludeId);
            }
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        } catch(PDOException $e) {
            error_log("Error checking category existence: " . $e->getMessage());
            return false;
        }
    }
    
    public function addCategory($data) {
        try {
            // Check if category name already exists
            if ($this->categoryExists($data['tendanhmuc'])) {
                throw new Exception("Tên danh mục đã tồn tại!");
            }
    
            $query = "INSERT INTO danhmuc (tendanhmuc, trangthai) VALUES (:tendanhmuc, :trangthai)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':tendanhmuc', $data['tendanhmuc']);
            $stmt->bindParam(':trangthai', $data['trangthai']);
            return $stmt->execute();
        } catch(Exception $e) {
            error_log("Error adding category: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function updateCategory($data) {
        try {
            // Check if category name already exists (excluding current category)
            if ($this->categoryExists($data['tendanhmuc'], $data['iddanhmuc'])) {
                throw new Exception("Tên danh mục đã tồn tại!");
            }
    
            $query = "UPDATE danhmuc SET tendanhmuc = :tendanhmuc, trangthai = :trangthai WHERE iddanhmuc = :iddanhmuc";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':iddanhmuc', $data['iddanhmuc']);
            $stmt->bindParam(':tendanhmuc', $data['tendanhmuc']);
            $stmt->bindParam(':trangthai', $data['trangthai']);
            return $stmt->execute();
        } catch(Exception $e) {
            error_log("Error updating category: " . $e->getMessage());
            throw $e;
        }
    }
    public function checkCategoryHasProducts($categoryId) {
        try {
            $query = "SELECT COUNT(*) as count FROM sanpham WHERE iddanhmuc = :iddanhmuc";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':iddanhmuc', $categoryId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch(PDOException $e) {
            error_log("Lỗi khi kiểm tra sản phẩm trong danh mục: " . $e->getMessage());
            return false;
        }
    }
    public function deleteCategory($id) {
        try {
            // Kiểm tra xem danh mục có sản phẩm không
            $productCount = $this->checkCategoryHasProducts($id);
            
            if ($productCount > 0) {
                // Nếu có sản phẩm, không cho phép xóa
                throw new Exception("Không thể xóa danh mục này vì đang có " . $productCount . " sản phẩm!");
            }
            
            // Nếu không có sản phẩm, tiến hành xóa
            $query = "DELETE FROM danhmuc WHERE iddanhmuc = :iddanhmuc";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':iddanhmuc', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(Exception $e) {
            error_log("Lỗi khi xóa danh mục: " . $e->getMessage());
            throw $e; // Ném lại ngoại lệ để được bắt ở nơi gọi hàm
        }
    }
    public function getTopSellingProductsByQuantity($limit = 5, $month = null, $year = null) {
        try {
            $query = "SELECT sp.*, 
                     SUM(cd.soluong) as total_quantity,
                     COUNT(DISTINCT cd.iddonhang) as total_sold,
                     SUM(cd.soluong * cd.dongia) as total_revenue,
                     dm.tendanhmuc
                     FROM sanpham sp
                     JOIN chitietdonhang cd ON sp.idsanpham = cd.idsanpham
                     JOIN donhang dh ON cd.iddonhang = dh.iddonhang
                     LEFT JOIN danhmuc dm ON sp.iddanhmuc = dm.iddanhmuc
                     WHERE dh.trangthai = 'Hoàn thành'";
            
            if ($month && $year) {
                $query .= " AND MONTH(dh.ngaydat) = :month AND YEAR(dh.ngaydat) = :year";
            }
            
            $query .= " GROUP BY sp.idsanpham
                       ORDER BY total_quantity DESC
                       LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            
            if ($month && $year) {
                $stmt->bindParam(':month', $month, PDO::PARAM_INT);
                $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            }
            
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting top selling products by quantity: " . $e->getMessage());
            return [];
        }
    }
    //lấy danh sach đơn hàng
    public function getAllOrdersAdmin($search = '', $status = '') {
        try {
            $query = "SELECT d.*, ct.trangthai as trangthai_tt 
                      FROM donhang d 
                      LEFT JOIN chitietthanhtoan ct ON d.idthanhtoan = ct.idthanhtoan 
                      WHERE 1=1";
            $params = [];
    
            if (!empty($search)) {
                $query .= " AND (d.tennguoidat LIKE :search OR d.sdt LIKE :search)";
                $params[':search'] = "%$search%";
            }
    
            if (!empty($status)) {
                $query .= " AND d.trangthai = :status";
                $params[':status'] = $status;
            }
    
            $query .= " ORDER BY d.ngaydat DESC";
            
            $stmt = $this->conn->prepare($query);
            
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting orders: " . $e->getMessage());
            return [];
        }
    }

    public function getOrderByIdAdmin($orderId) {
        try {
            $query = "SELECT d.*, ct.trangthai as trangthai_tt, ct.magiaodich 
                      FROM donhang d 
                      LEFT JOIN chitietthanhtoan ct ON d.idthanhtoan = ct.idthanhtoan 
                      WHERE d.iddonhang = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $orderId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting order: " . $e->getMessage());
            return null;
        }
    }

    public function getOrderDetailsAdmin($orderId) {
        try {
            $query = "SELECT cd.*, sp.tensanpham, sp.idsanpham, sp.path_anh_goc
                      FROM chitietdonhang cd 
                      JOIN sanpham sp ON cd.idsanpham = sp.idsanpham 
                      WHERE cd.iddonhang = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $orderId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting order details: " . $e->getMessage());
            return [];
        }
    }
    
    public function updateOrderStatusAdmin($data) {
        try {
            $query = "UPDATE donhang SET trangthai = :trangthai WHERE iddonhang = :iddonhang";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':iddonhang', $data['iddonhang']);
            $stmt->bindParam(':trangthai', $data['trangthai']);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating order status: " . $e->getMessage());
            return false;
        }
    }
    
    // Order management functions
    public function getAllOrders($month = null, $year = null) {
        try {
            $query = "SELECT d.*, u.tendangnhap 
                      FROM donhang d 
                      LEFT JOIN nguoidung u ON d.idnguoidung = u.idnguoidung";
            
            // Add date filters if provided
            if ($month !== null && $year !== null) {
                $query .= " WHERE MONTH(d.ngaydat) = :month AND YEAR(d.ngaydat) = :year";
            } else if ($year !== null) {
                $query .= " WHERE YEAR(d.ngaydat) = :year";
            }
            
            $query .= " ORDER BY d.ngaydat DESC";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters if provided
            if ($month !== null && $year !== null) {
                $stmt->bindParam(':month', $month, PDO::PARAM_INT);
                $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            } else if ($year !== null) {
                $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting all orders: " . $e->getMessage());
            return [];
        }
    }

    public function getOrderCountsByStatus($month = null, $year = null) {
        try {
            $query = "SELECT trangthai, COUNT(*) as count
                      FROM donhang";
            
            // Add date filters if provided
            if ($month !== null && $year !== null) {
                $query .= " WHERE MONTH(ngaydat) = :month AND YEAR(ngaydat) = :year";
            } else if ($year !== null) {
                $query .= " WHERE YEAR(ngaydat) = :year";
            }
            
            $query .= " GROUP BY trangthai";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters if provided
            if ($month !== null && $year !== null) {
                $stmt->bindParam(':month', $month, PDO::PARAM_INT);
                $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            } else if ($year !== null) {
                $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $counts = [];
            foreach ($results as $result) {
                $counts[$result['trangthai']] = $result['count'];
            }
            
            return $counts;
        } catch(PDOException $e) {
            error_log("Error getting order counts by status: " . $e->getMessage());
            return [];
        }
    }
    
    public function updateOrderStatus($orderId, $status) {
        try {
            $query = "UPDATE donhang SET trangthai = :trangthai WHERE iddonhang = :iddonhang";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':trangthai' => $status,
                ':iddonhang' => $orderId
            ]);
            return true;
        } catch(PDOException $e) {
            error_log("Error in updateOrderStatus: " . $e->getMessage());
            return false;
        }
    }
    
    public function updatePaymentStatus($paymentId, $status) {
        try {
            $query = "UPDATE chitietthanhtoan SET trangthai = :trangthai WHERE idthanhtoan = :idthanhtoan";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':trangthai' => $status,
                ':idthanhtoan' => $paymentId
            ]);
            return true;
        } catch(PDOException $e) {
            error_log("Error in updatePaymentStatus: " . $e->getMessage());
            return false;
        }
    }
    
    // User management functions
    public function getAllCustomers($search = '') {
        try {
            $query = "SELECT * FROM nguoidung WHERE role = 0";
            
            if (!empty($search)) {
                $query .= " AND (tendangnhap LIKE :search OR email LIKE :search)";
            }
            
            $query .= " ORDER BY ngaytao DESC";
            
            $stmt = $this->conn->prepare($query);
            
            if (!empty($search)) {
                $stmt->bindValue(':search', "%$search%");
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting customers: " . $e->getMessage());
            return [];
        }
    }

    //câp nhat thông tin người dùng
    public function updateCustomerStatus($id, $status) {
        try {
            $query = "UPDATE nguoidung 
                     SET trangthai = :status 
                     WHERE idnguoidung = :id AND role = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating customer status: " . $e->getMessage());
            return false;
        }
    }

    //chi tiêt don hang nguoi dung
    public function getCustomerDetails($id) {
        try {
            // Get customer basic info
            $query = "SELECT * FROM nguoidung WHERE idnguoidung = :id AND role = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($customer) {
                // Get order statistics
                $query = "SELECT 
                            COUNT(*) as total_orders,
                            SUM(CASE WHEN trangthai = 'Hoàn thành' THEN 1 ELSE 0 END) as completed_orders,
                            SUM(CASE WHEN trangthai = 'Đã hủy' THEN 1 ELSE 0 END) as cancelled_orders,
                            SUM(CASE WHEN trangthai = 'Hoàn thành' THEN tongtien ELSE 0 END) as total_spent
                         FROM donhang 
                         WHERE idnguoidung = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $stats = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Get recent orders
                $query = "SELECT * FROM donhang WHERE idnguoidung = :id ORDER BY ngaydat DESC LIMIT 10";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Merge all information
                return array_merge($customer, $stats, ['orders' => $orders]);
            }
            
            return null;
        } catch(PDOException $e) {
            error_log("Error getting customer details: " . $e->getMessage());
            return null;
        }
    }

    public function getAllStaff($search = '') {
        try {
            $query = "SELECT idnguoidung, tendangnhap, email, ngaytao, trangthai 
                     FROM nguoidung 
                     WHERE role = 1";
            if (!empty($search)) {
                $query .= " AND (tendangnhap LIKE :search OR email LIKE :search)";
            }
            $query .= " ORDER BY ngaytao DESC";
            
            $stmt = $this->conn->prepare($query);
            if (!empty($search)) {
                $searchParam = "%$search%";
                $stmt->bindParam(':search', $searchParam);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting all staff: " . $e->getMessage());
            return [];
        }
    }
    
    public function addStaff($username, $email, $password) {
        try {
            $query = "INSERT INTO nguoidung (tendangnhap, email, matkhau, role, trangthai, sdt, cccd) 
                     VALUES (:username, :email, :password, 1, 1, '', '')";
            $stmt = $this->conn->prepare($query);
            $hashedPassword = md5($password, PASSWORD_DEFAULT);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            if ($stmt->execute()) {
                $userId = $this->conn->lastInsertId();
                // Thêm quyền mặc định vào bảng quyentruycap
                $this->addDefaultPermissions($userId);
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error adding staff: " . $e->getMessage());
            return false;
        }
    }
    public function addDefaultPermissions($userId) {
        try {
            $query = "INSERT INTO quyentruycap (idnguoidung, sanpham, danhmuc, loaimay, loaiday, nhacungcap, donhang, khachhang, nhanvien, danhgia, tinnhan, baocao) 
                     VALUES (:idnguoidung, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idnguoidung', $userId);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error adding default permissions: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateStaff($id, $username, $email, $password = null) {
        try {
            $query = "UPDATE nguoidung SET tendangnhap = :username, email = :email";
            if ($password !== null) {
                $query .= ", matkhau = :password";
            }
            $query .= " WHERE idnguoidung = :id AND role = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id', $id);
            
            if ($password !== null) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt->bindParam(':password', $hashedPassword);
            }
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating staff: " . $e->getMessage());
            return false;
        }
    }
    public function getPermissions($userId) {
        try {
            $query = "SELECT * FROM quyentruycap WHERE idnguoidung = :idnguoidung";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idnguoidung', $userId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
                'sanpham' => 0,
                'danhmuc' => 0,
                'loaimay' => 0,
                'loaiday' => 0,
                'nhacungcap' => 0,
                'donhang' => 0,
                'khachhang' => 0,
                'nhanvien' => 0,
                'danhgia' => 0,
                'tinnhan' => 0,
                'baocao' => 0
            ];
        } catch(PDOException $e) {
            error_log("Error getting permissions: " . $e->getMessage());
            return [];
        }
    }

    public function getStaffById($id) {
        try {
            $query = "SELECT * FROM nguoidung WHERE idnguoidung = :id AND role = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting staff by ID: " . $e->getMessage());
            return null;
        }
    }
    public function updatePermissions($userId, $permissions) {
        try {
            // Kiểm tra xem đã có quyền cho người dùng chưa
            $query = "SELECT idquyen FROM quyentruycap WHERE idnguoidung = :idnguoidung";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idnguoidung', $userId);
            $stmt->execute();
            $exists = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($exists) {
                // Cập nhật quyền
                $query = "UPDATE quyentruycap 
                         SET sanpham = :sanpham, danhmuc = :danhmuc, loaimay = :loaimay, loaiday = :loaiday, 
                             nhacungcap = :nhacungcap, donhang = :donhang, khachhang = :khachhang, 
                             nhanvien = :nhanvien, danhgia = :danhgia, tinnhan = :tinnhan, baocao = :baocao, khuyenmai = :khuyenmai
                         WHERE idnguoidung = :idnguoidung";
            } else {
                // Thêm mới quyền
                $query = "INSERT INTO quyentruycap 
                         (idnguoidung, sanpham, danhmuc, loaimay, loaiday, nhacungcap, donhang, khachhang, nhanvien, danhgia, tinnhan, baocao, khuyenmai) 
                         VALUES (:idnguoidung, :sanpham, :danhmuc, :loaimay, :loaiday, :nhacungcap, :donhang, :khachhang, :nhanvien, :danhgia, :tinnhan, :baocao, :khuyenmai)";
            }

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idnguoidung', $userId);
            $stmt->bindParam(':sanpham', $permissions['sanpham'], PDO::PARAM_INT);
            $stmt->bindParam(':danhmuc', $permissions['danhmuc'], PDO::PARAM_INT);
            $stmt->bindParam(':loaimay', $permissions['loaimay'], PDO::PARAM_INT);
            $stmt->bindParam(':loaiday', $permissions['loaiday'], PDO::PARAM_INT);
            $stmt->bindParam(':nhacungcap', $permissions['nhacungcap'], PDO::PARAM_INT);
            $stmt->bindParam(':donhang', $permissions['donhang'], PDO::PARAM_INT);
            $stmt->bindParam(':khachhang', $permissions['khachhang'], PDO::PARAM_INT);
            $stmt->bindParam(':nhanvien', $permissions['nhanvien'], PDO::PARAM_INT);
            $stmt->bindParam(':danhgia', $permissions['danhgia'], PDO::PARAM_INT);
            $stmt->bindParam(':tinnhan', $permissions['tinnhan'], PDO::PARAM_INT);
            $stmt->bindParam(':baocao', $permissions['baocao'], PDO::PARAM_INT);
            $stmt->bindParam(':khuyenmai', $permissions['khuyenmai'], PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating permissions: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateStaffStatus($id, $status) {
        try {
            $query = "UPDATE nguoidung SET trangthai = :status 
                     WHERE idnguoidung = :id AND role = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating staff status: " . $e->getMessage());
            return false;
        }
    }
    
    public function addUser($userData) {
        try {
            $query = "INSERT INTO nguoidung (tendangnhap, email, matkhau, role, trangthai) 
                      VALUES (:tendangnhap, :email, :matkhau, :role, :trangthai)";
            $stmt = $this->conn->prepare($query);
            $hashedPassword = md5($userData['matkhau']);
            $stmt->execute([
                ':tendangnhap' => $userData['tendangnhap'],
                ':email' => $userData['email'],
                ':matkhau' => $hashedPassword,
                ':role' => $userData['role'],
                ':trangthai' => $userData['trangthai']
            ]);
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            error_log("Error in addUser: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateUser($userId, $userData) {
        try {
            $query = "UPDATE nguoidung SET 
                        tendangnhap = :tendangnhap,
                        email = :email,
                        role = :role,
                        trangthai = :trangthai
                      WHERE idnguoidung = :idnguoidung";
            
            $params = [
                ':tendangnhap' => $userData['tendangnhap'],
                ':email' => $userData['email'],
                ':role' => $userData['role'],
                ':trangthai' => $userData['trangthai'],
                ':idnguoidung' => $userId
            ];
            
            // If password is being updated
            if (!empty($userData['matkhau'])) {
                $query = "UPDATE nguoidung SET 
                            tendangnhap = :tendangnhap,
                            email = :email,
                            matkhau = :matkhau,
                            role = :role,
                            trangthai = :trangthai
                          WHERE idnguoidung = :idnguoidung";
                $params[':matkhau'] = md5($userData['matkhau']);
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return true;
        } catch(PDOException $e) {
            error_log("Error in updateUser: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteUser($userId) {
        try {
            $query = "DELETE FROM nguoidung WHERE idnguoidung = :idnguoidung";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':idnguoidung' => $userId]);
            return true;
        } catch(PDOException $e) {
            error_log("Error in deleteUser: " . $e->getMessage());
            return false;
        }
    }
    public function getAllUsers() {
        try {
            $query = "SELECT * FROM nguoidung ORDER BY ngaytao DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting all users: " . $e->getMessage());
            return [];
        }
    }
    public function updateProductStock($productId, $quantity) {
        try {
            $query = "UPDATE sanpham SET soluong = soluong - :quantity 
                     WHERE idsanpham = :productId AND soluong >= :quantity";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating product stock: " . $e->getMessage());
            return false;
        }
    }
    public function deleteSupplier($id) {
        try {
            // Kiểm tra xem nhà cung cấp có đang cung cấp sản phẩm nào không
            $query = "SELECT COUNT(*) as count FROM sanpham WHERE idnhacungcap = :idnhacungcap";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idnhacungcap', $id);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $productCount = $result['count'];
            
            if ($productCount > 0) {
                // Nếu có sản phẩm, không cho phép xóa
                throw new Exception("Không thể xóa nhà cung cấp này vì đang cung cấp " . $productCount . " sản phẩm!");
            }
            
            // Nếu không có sản phẩm, tiến hành xóa
            $query = "DELETE FROM nhacungcap WHERE idnhacungcap = :idnhacungcap";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idnhacungcap', $id);
            return $stmt->execute();
        } catch(Exception $e) {
            error_log("Lỗi khi xóa nhà cung cấp: " . $e->getMessage());
            throw $e; // Ném lại ngoại lệ để được bắt ở nơi gọi hàm
        }
    }

    public function getUserById($userId) {
        try {
            $query = "SELECT * FROM nguoidung WHERE idnguoidung = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting user: " . $e->getMessage());
            return null;
        }
    }
    public function getTotalSpentByUser($userId) {
        try {
            $query = "SELECT SUM(tongtien) as total_spent 
                      FROM donhang 
                      WHERE idnguoidung = :userId AND trangthai = 'Hoàn thành'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_spent'] ?? 0;
        } catch(PDOException $e) {
            error_log("Error in getTotalSpentByUser: " . $e->getMessage());
            return 0;
        }
    }

    public function verifyUserPassword($userId, $currentPassword) {
        try {
            $query = "SELECT matkhau FROM nguoidung WHERE idnguoidung = :userId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Compare using MD5 hash
            if ($user && md5($currentPassword) === $user['matkhau']) {
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error in verifyUserPassword: " . $e->getMessage());
            return false;
        }
    }
    
    // Xác minh mật khẩu hiện tại của người dùng
    
    
    // Cập nhật mật khẩu người dùng
    public function updateUserPassword($userId, $newPassword) {
        try {
            $hashedPassword = md5($newPassword); // Chỉ cần truyền $newPassword
            $query = "UPDATE nguoidung SET matkhau = :password WHERE idnguoidung = :userId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error in updateUserPassword: " . $e->getMessage());
            return false;
        }
    }

    //đăng xuất đăng nhập


    public function login($email, $matkhau) {
        try {
            // Check for active user with matching email
            $query = "SELECT * FROM nguoidung WHERE email = :email LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                // Check if account is active
                if($user['trangthai'] != 1) {
                    return [
                        'success' => false,
                        'message' => 'Tài khoản đã bị khóa'
                    ];
                }
                
                // Verify password
                if(md5($matkhau) === $user['matkhau']) {
                    // Start session if not already started
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['idnguoidung'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['tendangnhap'] = $user['tendangnhap'];
                    $_SESSION['role'] = $user['role'];
                    
                    return [
                        'success' => true,
                        'message' => 'Đăng nhập thành công',
                        'role' => $user['role']
                    ];
                }
                return [
                    'success' => false,
                    'message' => 'Mật khẩu không chính xác'
                ];
            }
            return [
                'success' => false,
                'message' => 'Email không tồn tại'
            ];
        } catch(PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình đăng nhập'
            ];
        }
    }
    
    public function register($tendangnhap, $email, $matkhau) {
        try {
            // Kiểm tra email đã tồn tại chưa
            $checkQuery = "SELECT idnguoidung FROM nguoidung WHERE email = :email LIMIT 1";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':email', $email);
            $checkStmt->execute();
            
            if($checkStmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email đã tồn tại'];
            }
            
            // Hash the password with MD5
            $hashedPassword = md5($matkhau);
            
            // Thêm người dùng mới
            $query = "INSERT INTO nguoidung (tendangnhap, email, matkhau, role, trangthai) 
                     VALUES (:tendangnhap, :email, :matkhau, 0, 1)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':tendangnhap', $tendangnhap);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':matkhau', $hashedPassword);
            
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Đăng ký thành công'];
            } else {
                return ['success' => false, 'message' => 'Đăng ký thất bại'];
            }
        } catch(PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Đăng ký thất bại'];
        }
    }
    
    public function logout() {
        session_start();
        session_destroy();
        return true;
    }
    
}
?>
