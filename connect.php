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
    
    // Update getProductsByStock method similarly
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

    public function getCategories() {  
        try {
            $query = "SELECT * FROM danhmuc 
                    WHERE trangthai = 1";
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
            $query = "SELECT DISTINCT s.*, h.duongdan as image_path, d.tendanhmuc
                    FROM sanpham s 
                    LEFT JOIN hinhanhsanpham h ON s.idsanpham = h.idsanpham
                    LEFT JOIN danhmuc d ON s.iddanhmuc = d.iddanhmuc
                    WHERE s.iddanhmuc = :categoryId
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
                    k.gia_giam, k.ngaybatdau, k.ngayketthuc 
                FROM sanpham s 
                LEFT JOIN hinhanhsanpham h ON s.idsanpham = h.idsanpham 
                LEFT JOIN danhmuc d ON s.iddanhmuc = d.iddanhmuc
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

    public function addProduct($name, $code, $status, $image, $url) {
        try {
            $query = "INSERT INTO products (name, code, status, image, url) 
                     VALUES (:name, :code, :status, :image, :url)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":code", $code);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":image", $image);
            $stmt->bindParam(":url", $url);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function updateProduct($id, $name, $code, $status, $image, $url) {
        try {
            $query = "UPDATE products 
                     SET name = :name, code = :code, status = :status, 
                         image = :image, url = :url 
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":code", $code);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":image", $image);
            $stmt->bindParam(":url", $url);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function deleteProduct($id) {
        try {
            $query = "DELETE FROM products WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
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

    public function getCartItems($idnguoidung) {
        try {
            $query = "SELECT s.*, c.soluong, (s.giaban * c.soluong) as thanhtien 
                     FROM giohang g 
                     JOIN chitietgiohang c ON g.idgiohang = c.idgiohang 
                     JOIN sanpham s ON c.idsanpham = s.idsanpham 
                     WHERE g.idnguoidung = :idnguoidung";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idnguoidung', $idnguoidung);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get cart items error: " . $e->getMessage());
            return [];
        }
    }

    //đăng xuất đăng nhập



    public function login($email, $matkhau) {
        try {
            $query = "SELECT * FROM nguoidung WHERE email = :email AND role = 0 LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if(md5($matkhau) === $user['matkhau']) { // Compare with MD5 hash
                    session_start();
                    $_SESSION['user_id'] = $user['idnguoidung'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['tendangnhap'] = $user['tendangnhap'];
                    return true;
                }
            }
            return false;
        } catch(PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
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
            $query = "INSERT INTO nguoidung (tendangnhap, email, matkhau, role) 
                     VALUES (:tendangnhap, :email, :matkhau, 0)";
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