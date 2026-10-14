# 🏍️ HỆ THỐNG QUẢN LÝ SHOWROOM XE MÁY (HONDA THẮNG LỢI)

Hệ thống Quản lý Showroom Xe máy là ứng dụng web toàn diện hỗ trợ quản lý vận hành kinh doanh bán lẻ xe máy, bao gồm quản lý kho hàng, nhập hàng, bán hàng, quản lý khách hàng, nhà cung cấp, nhân sự và thống kê doanh thu.

---

## 📌 MỤC LỤC
1. [Giới thiệu dự án](#-giới-thiệu-dự-án)
2. [Tính năng chính](#-tính-năng-chính)
3. [Công nghệ sử dụng](#-công-nghệ-sử-dụng)
4. [Cấu trúc thư mục](#-cấu-trúc-thư-mục)
5. [Cơ sở dữ liệu](#-cơ-sở-dữ-liệu)
6. [Yêu cầu hệ thống](#-yêu-cầu-hệ-thống)
7. [Hướng dẫn cài đặt & Chạy dự án](#-hướng-dẫn-cài-đặt--chạy-dự-án)
8. [Tài khoản mặc định](#-tài-khoản-mặc-định)
9. [Tác giả & Đóng góp](#-tác-giả--đóng-góp)

---

## 📖 Giới thiệu dự án

Hệ thống được phát triển phục vụ công tác số hóa quy trình quản lý của đại lý/showroom xe máy **Honda Thắng Lợi**. Ứng dụng xây dựng theo kiến trúc MVC phân tách rõ ràng giữa giao diện hiển thị, xử lý nghiệp vụ CSDL và cung cấp RESTful API cho các thao tác CRUD dữ liệu mượt mà bằng JavaScript (AJAX/Fetch API).

---

## 🚀 Tính năng chính

### 1. Xác thực & Phân quyền (Authentication & Authorization)
- Đăng nhập bảo mật với mã hóa mật khẩu (`password_hash` & `password_verify`).
- Lưu trữ phiên làm việc an toàn với PHP Session.
- Phân quyền người dùng theo 2 vai trò:
  - **Quản trị viên (`admin`)**: Toàn quyền quản trị nhân viên, tài khoản, nhà cung cấp, sản phẩm, kho, đơn hàng và xem báo cáo thống kê doanh thu.
  - **Nhân viên (`nhan_vien`)**: Thực hiện các thao tác bán hàng, lập hóa đơn, quản lý khách hàng, xem danh sách sản phẩm và quản lý nhập/xuất kho.

### 2. Quản lý Sản phẩm Xe máy
- Hiển thị danh mục xe máy với hình ảnh đại diện, giá niêm yết, số lượng tồn và trạng thái.
- Thêm mới, chỉnh sửa và cập nhật trạng thái kinh doanh (Bật/Ẩn) của xe.
- Quản lý theo các thuộc tính kỹ thuật chi tiết:
  - Loại xe (Xe ga, Xe số, Xe côn tay...)
  - Hệ thống phanh (CBS, ABS, Phanh cơ...)
  - Động cơ (110cc, 125cc, 150cc, 160cc...)
  - Màu sắc.
- Upload và xử lý hình ảnh sản phẩm.
- Kiểm tra trùng lặp tên xe và ràng buộc dữ liệu giao dịch trước khi xóa/sửa.

### 3. Quản lý Nhập kho & Tồn kho
- Lập phiếu nhập hàng chi tiết từ các nhà cung cấp.
- Hỗ trợ nhập nhiều loại xe trên cùng một phiếu nhập.
- Tự động cộng dồn số lượng tồn kho theo giao dịch an toàn (Database Transaction).
- Xem lịch sử nhập kho, xem chi tiết và in phiếu nhập kho chuẩn mẫu.

### 4. Quản lý Bán hàng & Hóa đơn
- Tạo đơn bán hàng nhanh chóng, tìm kiếm và chọn khách hàng hoặc thêm mới khách hàng trực tiếp.
- Kiểm tra số lượng tồn kho khả dụng thời gian thực khi chọn xe bán.
- Tự động trừ số lượng xe trong kho khi thanh toán hóa đơn.
- Quản lý danh sách lịch sử hóa đơn bán hàng.
- Xem chi tiết và hỗ trợ in hóa đơn bán hàng cho khách.

### 5. Quản lý Khách hàng & Đối tác
- Quản lý hồ sơ khách hàng: họ tên, số điện thoại, địa chỉ, lịch sử mua hàng.
- Quản lý nhà cung cấp: thông tin liên hệ, nguồn cung xe máy (Dành riêng cho Admin).

### 6. Quản lý Nhân sự & Phân quyền tài khoản
- Quản lý danh sách nhân viên showroom.
- Cấp tài khoản đăng nhập hệ thống liên kết theo từng nhân viên.
- Khóa hoặc kích hoạt tài khoản nhân viên.

### 7. Báo cáo & Thống kê doanh thu (Admin)
- Thống kê doanh thu theo tháng được chọn.
- Thống kê tổng số lượng xe đã bán ra và số lượng khách hàng mua hàng.
- Trực quan hóa dữ liệu bằng biểu đồ doanh thu theo từng ngày trong tháng.

---

## 🛠️ Công nghệ sử dụng

- **Backend**:
  - PHP thuần (Pure PHP OOP).
  - Kiến trúc MVC (Model - View - Controller).
  - Kết nối cơ sở dữ liệu qua **PDO (PHP Data Objects)**, hỗ trợ Prepared Statements chống tấn công SQL Injection.
  - Xử lý giao dịch nhiều bảng với PDO Transactions (`beginTransaction`, `commit`, `rollBack`).
- **Frontend**:
  - HTML5, CSS3 hiện đại (Responsive UI, CSS Flexbox/Grid).
  - Vanilla JavaScript (Fetch API, `async/await`, DOM Manipulation).
  - Bộ icon: **Font Awesome 6**.
- **Cơ sở dữ liệu**:
  - MySQL / MariaDB (Bộ mã `utf8_unicode_ci` / `utf8mb4`).
- **Máy chủ Web**:
  - Apache HTTP Server với module `mod_rewrite` được kích hoạt (cấu hình qua `.htaccess`).

---

## 📁 Cấu trúc thư mục

```text
quanlyxemay2/
├── app/
│   ├── Controllers/
│   │   └── Api/                 # Các REST API Controller (JSON Response)
│   │       ├── HoaDon.php       # API xử lý hóa đơn bán hàng
│   │       ├── KhachHang.php    # API thông tin khách hàng
│   │       ├── NhaCungCap.php   # API nhà cung cấp
│   │       ├── NhanVien.php     # API nhân sự
│   │       ├── PhieuNhap.php    # API phiếu nhập kho
│   │       ├── Report.php       # API báo cáo thống kê
│   │       ├── User.php         # API đăng nhập, tài khoản
│   │       └── XeMay.php        # API sản phẩm xe máy & danh mục
│   ├── Models/                  # Tầng truy xuất dữ liệu (Data Access Layer)
│   │   ├── HoaDon.php
│   │   ├── KhachHang.php
│   │   ├── NhaCungCap.php
│   │   ├── NhanVien.php
│   │   ├── PhieuNhap.php
│   │   ├── ReportModel.php
│   │   ├── User.php
│   │   └── XeMay.php
│   ├── App.php                  # Bộ định tuyến URL (Router) phân tách View và API
│   └── Database.php             # Lớp cấu hình kết nối CSDL MySQL qua PDO
├── public/                      # Thư mục gốc web công khai (Document Root)
│   ├── assets/
│   │   ├── css/                 # Các file stylesheet giao diện
│   │   └── img/                 # Thư mục lưu trữ hình ảnh sản phẩm upload
│   ├── .htaccess                # Cấu hình URL Rewrite của Apache
│   └── index.php                # Điểm vào chính của ứng dụng (Entry Point)
├── views/                       # Tầng giao diện người dùng (View Templates)
│   ├── HoaDon.php               # Danh sách hóa đơn
│   ├── HoaDon_Add.php           # Giao diện lập hóa đơn bán hàng
│   ├── HoaDon_Print.php         # Mẫu in hóa đơn
│   ├── KhachHang.php            # Danh sách khách hàng
│   ├── KhachHang_Add.php        # Thêm mới khách hàng
│   ├── KhachHang_Edit.php       # Chỉnh sửa khách hàng
│   ├── Kho.php                  # Quản lý phiếu nhập kho
│   ├── Kho_Add.php              # Lập phiếu nhập hàng mới
│   ├── Kho_Detail.php           # Chi tiết phiếu nhập
│   ├── Kho_Print.php            # Mẫu in phiếu nhập kho
│   ├── Login.php                # Giao diện đăng nhập
│   ├── navi.php                 # Thanh điều hướng (Sidebar Menu)
│   ├── Ncc.php                  # Danh sách nhà cung cấp
│   ├── Ncc_Add.php              # Thêm nhà cung cấp
│   ├── Ncc_Edit.php             # Chỉnh sửa nhà cung cấp
│   ├── NhanVien.php             # Danh sách nhân viên
│   ├── NhanVien_Add.php         # Thêm nhân viên
│   ├── NhanVien_Edit.php        # Cập nhật nhân viên
│   ├── SanPham.php              # Quản lý danh mục sản phẩm xe máy
│   ├── SanPham_Add.php          # Thêm sản phẩm xe máy
│   ├── SanPham_Edit.php         # Chỉnh sửa sản phẩm
│   ├── TaiKhoan.php             # Quản lý tài khoản người dùng
│   ├── TaiKhoan_Add.php         # Cấp tài khoản mới
│   ├── TaiKhoan_Edit.php        # Phân quyền/Sửa tài khoản
│   └── ThongKe.php              # Giao diện báo cáo thống kê & biểu đồ
└── README.md                    # Tài liệu hướng dẫn sử dụng và cài đặt
```

---

## 🗄️ Cơ sở dữ liệu

Dự án sử dụng cơ sở dữ liệu MySQL với tên mặc định là `quanlyxemay`. Các bảng chính trong hệ thống bao gồm:

| Tên bảng | Mô tả |
| :--- | :--- |
| `users` | Tài khoản đăng nhập hệ thống, liên kết khóa ngoại với nhân viên và xác định quyền (`admin` / `nhan_vien`). |
| `nhanvien` | Lưu thông tin chi tiết của nhân viên showroom. |
| `khachhang` | Lưu thông tin khách hàng mua sắm tại showroom. |
| `nhacungcap` | Danh sách các đối tác, nhà cung cấp linh kiện/xe máy. |
| `xemay` | Thông tin các mẫu xe, giá bán, số lượng tồn kho, hình ảnh, thông số kỹ thuật. |
| `danhmuc_loaixe` | Danh mục phân loại xe (Xe tay ga, xe số, v.v.). |
| `danhmuc_mau` | Bảng màu sắc xe máy. |
| `danhmuc_dongco` | Các phân khối/loại động cơ. |
| `danhmuc_phanh` | Danh mục loại phanh an toàn. |
| `phieunhap` | Thông tin phiếu nhập kho hàng từ nhà cung cấp. |
| `chitiet_phieunhap` | Danh sách từng loại xe và số lượng, giá nhập của mỗi phiếu nhập. |
| `hoadon` | Thông tin hóa đơn bán hàng cho khách hàng. |
| `chitiet_hoadon` | Chi tiết số lượng và đơn giá của các xe trong hóa đơn. |

---

## 💻 Yêu cầu hệ thống

- **Máy chủ web**: Apache (yêu cầu bật module `mod_rewrite`).
- **PHP**: Phiên bản 7.4 hoặc 8.x trở lên (đã kích hoạt extension `pdo`, `pdo_mysql`, `mbstring`, `fileinfo`).
- **Cơ sở dữ liệu**: MySQL 5.7+ hoặc MariaDB 10.4+.
- Phần mềm môi trường khuyên dùng: **XAMPP**, **Laragon**, hoặc **WampServer**.

---

## ⚙️ Hướng dẫn cài đặt & Chạy dự án

### Bước 1: Tải hoặc sao chép mã nguồn
Đặt thư mục dự án `quanlyxemay2` vào thư mục phục vụ web của bạn:
- **XAMPP**: `C:\xampp\htdocs\quanlyxemay2`
- **Laragon**: `C:\laragon\www\quanlyxemay2`

### Bước 2: Cấu hình cơ sở dữ liệu
1. Mở phpMyAdmin (hoặc MySQL Workbench/HeidiSQL).
2. Tạo mới cơ sở dữ liệu có tên: `quanlyxemay` với định dạng mã hóa `utf8mb4_unicode_ci`.
3. Nhập (Import) file CSDL vào `quanlyxemay`.
4. Mở file [app/Database.php](file:///d:/quanlyxemay2/app/Database.php) và điều chỉnh thông tin cấu hình nếu cần:
   ```php
   private $host = "localhost";
   private $db_name = "quanlyxemay";
   private $username = "root";
   private $password = ""; // Mật khẩu MySQL của bạn
   ```

### Bước 3: Kiểm tra cấu hình đường dẫn Web
Trong file [public/index.php](file:///d:/quanlyxemay2/public/index.php), đảm bảo hằng số `_WEB_ROOT` khớp với tên thư mục của bạn trên localhost:
```php
define('_WEB_ROOT', '/quanlyxemay2/public');
```

### Bước 4: Cấp quyền ghi cho thư mục Uploads
Đảm bảo thư mục [public/assets/img](file:///d:/quanlyxemay2/public/assets/img) có quyền ghi (Write Permission) để tải lên ảnh sản phẩm xe máy.

### Bước 5: Khởi chạy và trải nghiệm
1. Khởi động dịch vụ **Apache** và **MySQL** trên XAMPP / Laragon.
2. Mở trình duyệt web và truy cập theo đường dẫn:
   ```text
   http://localhost/quanlyxemay2/public/
   ```
3. Đăng nhập và bắt đầu sử dụng hệ thống.

---

## 🔑 Tài khoản mặc định

Sau khi cài đặt cơ sở dữ liệu, bạn có thể đăng nhập bằng tài khoản mẫu:

- **Tài khoản Quản trị viên (Admin)**:
  - Tài khoản: `admin`
  - Mật khẩu: `admin123` *(hoặc mật khẩu tương ứng được khởi tạo trong DB)*
  - Quyền hạn: Toàn quyền truy cập mọi tính năng và thống kê.

- **Tài khoản Nhân viên (Staff)**:
  - Tài khoản: `nhanvien`
  - Mật khẩu: `123456`
  - Quyền hạn: Bán hàng, quản lý kho, quản lý khách hàng.

---

## 🤝 Đóng góp & Bản quyền

Dự án phát triển phục vụ công tác học tập, nghiên cứu và quản lý kinh doanh showroom xe máy. Mọi đóng góp, báo lỗi hoặc đề xuất tính năng mới đều được hoan nghênh.
# quanlyxemay
