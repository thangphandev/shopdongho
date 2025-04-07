-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th3 27, 2025 lúc 07:15 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `shopdongho99`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `baohanh`
--

CREATE TABLE `baohanh` (
  `idbaohanh` int(11) NOT NULL,
  `idsanpham` int(11) NOT NULL,
  `tensanpham` text NOT NULL,
  `trangthai` text NOT NULL,
  `thoigian` datetime NOT NULL,
  `quydinhbaohanh` text NOT NULL,
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chatbox`
--

CREATE TABLE `chatbox` (
  `idchatbox` int(11) NOT NULL,
  `idnguoidung` int(11) NOT NULL,
  `noidungchat` text NOT NULL,
  `role` int(11) NOT NULL,
  `thoigian` datetime NOT NULL,
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdonhang`
--

CREATE TABLE `chitietdonhang` (
  `idchitietdonhang` int(11) NOT NULL,
  `iddonhang` int(11) NOT NULL,
  `idsanpham` int(11) NOT NULL,
  `soluong` int(11) NOT NULL,
  `giaban` decimal(10,2) NOT NULL,
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietgiohang`
--

CREATE TABLE `chitietgiohang` (
  `idchitietgiohang` int(11) NOT NULL,
  `idgiohang` int(11) NOT NULL,
  `idsanpham` int(11) NOT NULL,
  `soluong` int(11) NOT NULL,
  `giaban` decimal(10,2) NOT NULL,
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietthanhtoan`
--

CREATE TABLE `chitietthanhtoan` (
  `idthanhtoan` int(11) NOT NULL,
  `phuongthuctt` text NOT NULL,
  `tongtien` decimal(10,2) NOT NULL,
  `trangthai` text NOT NULL,
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `magiaodich` text NOT NULL,
  `ngaynhanhang` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhgia`
--

CREATE TABLE `danhgia` (
  `iddanhgia` int(11) NOT NULL,
  `idsanpham` int(11) NOT NULL,
  `idnguoidung` int(11) NOT NULL,
  `sosao` int(11) NOT NULL,
  `noidung` text NOT NULL,
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhmuc`
--

CREATE TABLE `danhmuc` (
  `iddanhmuc` int(11) NOT NULL,
  `tendanhmuc` text NOT NULL,
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donhang`
--

CREATE TABLE `donhang` (
  `iddonhang` int(11) NOT NULL,
  `idnguoidung` int(11) NOT NULL,
  `tennguoidat` text NOT NULL,
  `ngaydat` datetime DEFAULT current_timestamp(),
  `trangthai` text NOT NULL,
  `tongtien` decimal(10,2) NOT NULL,
  `diachigiao` text NOT NULL,
  `sdt` varchar(15) NOT NULL,
  `phuongthuctt` varchar(50) NOT NULL,
  `idthanhtoan` int(11) NOT NULL,
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giohang`
--

CREATE TABLE `giohang` (
  `idgiohang` int(11) NOT NULL,
  `idnguoidung` int(11) NOT NULL,
  `tongtien` decimal(10,2) DEFAULT 0.00,
  `ngaycapnhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hinhanhsanpham`
--

CREATE TABLE `hinhanhsanpham` (
  `idhinhanh` int(11) NOT NULL,
  `idsanpham` int(11) NOT NULL,
  `duongdan` text NOT NULL,
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khuyenmai`
--

CREATE TABLE `khuyenmai` (
  `idkhuyenmai` int(11) NOT NULL,
  `tenkhuyenmai` text NOT NULL,
  `giaban` decimal(10,2) NOT NULL,
  `ngaybatdau` datetime NOT NULL,
  `ngayketthuc` datetime NOT NULL,
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung`
--

CREATE TABLE `nguoidung` (
  `idnguoidung` int(11) NOT NULL,
  `tendangnhap` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `sdt` varchar(15) NOT NULL,
  `matkhau` varchar(50) NOT NULL,
  `ngaytao` timestamp NULL DEFAULT current_timestamp(),
  `role` int(11) NOT NULL,
  `cccd` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhacungcap`
--

CREATE TABLE `nhacungcap` (
  `idnhacungcap` int(11) NOT NULL,
  `tennhacungcap` text NOT NULL,
  `diachi` text NOT NULL,
  `sdt` varchar(15) NOT NULL,
  `idsanpham` int(11) NOT NULL,
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

CREATE TABLE `sanpham` (
  `idsanpham` int(11) NOT NULL,
  `tensanpham` text NOT NULL,
  `mota` text DEFAULT NULL,
  `giaban` decimal(10,2) NOT NULL,
  `tonkho` int(11) NOT NULL DEFAULT 0,
  `iddanhmuc` int(11) DEFAULT NULL,
  `idkhuyenmai` int(11) DEFAULT NULL,
  `gianhap` decimal(10,2) NOT NULL,
  `idnhacungcap` int(11) NOT NULL,
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `bosuutap` text NOT NULL,
  `loaimay` text NOT NULL,
  `chatlieuvo` text NOT NULL,
  `loaiday` text NOT NULL,
  `matkinh` text NOT NULL,
  `mausac` text NOT NULL,
  `kichthuoc` text NOT NULL,
  `doday` text NOT NULL,
  `chongnuoc` text NOT NULL,
  `tinhnangdacbiet` text NOT NULL,
  `chinhsachbaohanh` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanphamkhuyenmai`
--

CREATE TABLE `sanphamkhuyenmai` (
  `idsanphamkhuyenmai` int(11) NOT NULL,
  `idkhuyenmai` int(11) NOT NULL,
  `idsanpham` int(11) NOT NULL,
  `ngaytao` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `baohanh`
--
ALTER TABLE `baohanh`
  ADD PRIMARY KEY (`idbaohanh`),
  ADD KEY `masanpham` (`idsanpham`);

--
-- Chỉ mục cho bảng `chatbox`
--
ALTER TABLE `chatbox`
  ADD PRIMARY KEY (`idchatbox`),
  ADD KEY `nguoidungid` (`idnguoidung`);

--
-- Chỉ mục cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD PRIMARY KEY (`idchitietdonhang`),
  ADD KEY `madonhang` (`iddonhang`),
  ADD KEY `masanpham` (`idsanpham`);

--
-- Chỉ mục cho bảng `chitietgiohang`
--
ALTER TABLE `chitietgiohang`
  ADD PRIMARY KEY (`idchitietgiohang`),
  ADD KEY `magiohang` (`idgiohang`),
  ADD KEY `masanpham` (`idsanpham`);

--
-- Chỉ mục cho bảng `chitietthanhtoan`
--
ALTER TABLE `chitietthanhtoan`
  ADD PRIMARY KEY (`idthanhtoan`);

--
-- Chỉ mục cho bảng `danhgia`
--
ALTER TABLE `danhgia`
  ADD PRIMARY KEY (`iddanhgia`),
  ADD KEY `masanpham` (`idsanpham`),
  ADD KEY `manguoidung` (`idnguoidung`);

--
-- Chỉ mục cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD PRIMARY KEY (`iddanhmuc`);

--
-- Chỉ mục cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD PRIMARY KEY (`iddonhang`),
  ADD KEY `manguoidung` (`idnguoidung`),
  ADD KEY `idthanhtoan` (`idthanhtoan`);

--
-- Chỉ mục cho bảng `giohang`
--
ALTER TABLE `giohang`
  ADD PRIMARY KEY (`idgiohang`),
  ADD UNIQUE KEY `manguoidung` (`idnguoidung`);

--
-- Chỉ mục cho bảng `hinhanhsanpham`
--
ALTER TABLE `hinhanhsanpham`
  ADD PRIMARY KEY (`idhinhanh`),
  ADD KEY `masanpham` (`idsanpham`);

--
-- Chỉ mục cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  ADD PRIMARY KEY (`idkhuyenmai`);

--
-- Chỉ mục cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`idnguoidung`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`);

--
-- Chỉ mục cho bảng `nhacungcap`
--
ALTER TABLE `nhacungcap`
  ADD PRIMARY KEY (`idnhacungcap`),
  ADD KEY `idsanpham` (`idsanpham`);

--
-- Chỉ mục cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`idsanpham`),
  ADD KEY `madanhmuc` (`iddanhmuc`),
  ADD KEY `makhuyenmai` (`idkhuyenmai`),
  ADD KEY `nhacungcapid` (`idnhacungcap`);

--
-- Chỉ mục cho bảng `sanphamkhuyenmai`
--
ALTER TABLE `sanphamkhuyenmai`
  ADD PRIMARY KEY (`idsanphamkhuyenmai`),
  ADD KEY `khuyenmai_id` (`idkhuyenmai`),
  ADD KEY `sanpham_id` (`idsanpham`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `baohanh`
--
ALTER TABLE `baohanh`
  MODIFY `idbaohanh` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `chatbox`
--
ALTER TABLE `chatbox`
  MODIFY `idchatbox` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  MODIFY `idchitietdonhang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT cho bảng `chitietgiohang`
--
ALTER TABLE `chitietgiohang`
  MODIFY `idchitietgiohang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `chitietthanhtoan`
--
ALTER TABLE `chitietthanhtoan`
  MODIFY `idthanhtoan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `danhgia`
--
ALTER TABLE `danhgia`
  MODIFY `iddanhgia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  MODIFY `iddanhmuc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `donhang`
--
ALTER TABLE `donhang`
  MODIFY `iddonhang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT cho bảng `giohang`
--
ALTER TABLE `giohang`
  MODIFY `idgiohang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `hinhanhsanpham`
--
ALTER TABLE `hinhanhsanpham`
  MODIFY `idhinhanh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  MODIFY `idkhuyenmai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  MODIFY `idnguoidung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `nhacungcap`
--
ALTER TABLE `nhacungcap`
  MODIFY `idnhacungcap` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  MODIFY `idsanpham` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `sanphamkhuyenmai`
--
ALTER TABLE `sanphamkhuyenmai`
  MODIFY `idsanphamkhuyenmai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `baohanh`
--
ALTER TABLE `baohanh`
  ADD CONSTRAINT `baohanh_ibfk_1` FOREIGN KEY (`idsanpham`) REFERENCES `sanpham` (`idsanpham`);

--
-- Các ràng buộc cho bảng `chatbox`
--
ALTER TABLE `chatbox`
  ADD CONSTRAINT `chatbox_ibfk_1` FOREIGN KEY (`idnguoidung`) REFERENCES `nguoidung` (`idnguoidung`);

--
-- Các ràng buộc cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD CONSTRAINT `chitietdonhang_ibfk_1` FOREIGN KEY (`iddonhang`) REFERENCES `donhang` (`iddonhang`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietdonhang_ibfk_2` FOREIGN KEY (`idsanpham`) REFERENCES `sanpham` (`idsanpham`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `chitietgiohang`
--
ALTER TABLE `chitietgiohang`
  ADD CONSTRAINT `chitietgiohang_ibfk_1` FOREIGN KEY (`idgiohang`) REFERENCES `giohang` (`idgiohang`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietgiohang_ibfk_2` FOREIGN KEY (`idsanpham`) REFERENCES `sanpham` (`idsanpham`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `danhgia`
--
ALTER TABLE `danhgia`
  ADD CONSTRAINT `danhgia_ibfk_1` FOREIGN KEY (`idsanpham`) REFERENCES `sanpham` (`idsanpham`) ON DELETE CASCADE,
  ADD CONSTRAINT `danhgia_ibfk_2` FOREIGN KEY (`idnguoidung`) REFERENCES `nguoidung` (`idnguoidung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`idnguoidung`) REFERENCES `nguoidung` (`idnguoidung`) ON DELETE CASCADE,
  ADD CONSTRAINT `donhang_ibfk_2` FOREIGN KEY (`idthanhtoan`) REFERENCES `chitietthanhtoan` (`idthanhtoan`);

--
-- Các ràng buộc cho bảng `giohang`
--
ALTER TABLE `giohang`
  ADD CONSTRAINT `giohang_ibfk_1` FOREIGN KEY (`idnguoidung`) REFERENCES `nguoidung` (`idnguoidung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `hinhanhsanpham`
--
ALTER TABLE `hinhanhsanpham`
  ADD CONSTRAINT `hinhanhsanpham_ibfk_1` FOREIGN KEY (`idsanpham`) REFERENCES `sanpham` (`idsanpham`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nhacungcap`
--
ALTER TABLE `nhacungcap`
  ADD CONSTRAINT `nhacungcap_ibfk_1` FOREIGN KEY (`idsanpham`) REFERENCES `sanpham` (`idsanpham`);

--
-- Các ràng buộc cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`iddanhmuc`) REFERENCES `danhmuc` (`iddanhmuc`) ON DELETE SET NULL,
  ADD CONSTRAINT `sanpham_ibfk_2` FOREIGN KEY (`idkhuyenmai`) REFERENCES `khuyenmai` (`idkhuyenmai`) ON DELETE SET NULL,
  ADD CONSTRAINT `sanpham_ibfk_3` FOREIGN KEY (`idnhacungcap`) REFERENCES `nhacungcap` (`idnhacungcap`);

--
-- Các ràng buộc cho bảng `sanphamkhuyenmai`
--
ALTER TABLE `sanphamkhuyenmai`
  ADD CONSTRAINT `sanphamkhuyenmai_ibfk_1` FOREIGN KEY (`idkhuyenmai`) REFERENCES `khuyenmai` (`idkhuyenmai`),
  ADD CONSTRAINT `sanphamkhuyenmai_ibfk_2` FOREIGN KEY (`idsanpham`) REFERENCES `sanpham` (`idsanpham`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
