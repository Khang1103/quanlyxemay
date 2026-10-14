<?php
// 1. SỬA ĐƯỜNG DẪN: Phải khớp với tên file thực tế trong thư mục Models
require_once _DIR_ROOT . '/app/Models/PhieuNhap.php';

class PhieuNhap {
    private $model;

    public function __construct() {
        $this->model = new PhieuNhapModel();
    }

    // Lấy danh sách phiếu nhập
    public function list() {
        header('Content-Type: application/json');
        try {
            $data = $this->model->getAll();
            // Trả về mảng phẳng để JS dùng được hàm .filter() và .map()
            echo json_encode($data);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;
    }

    // Xem chi tiết phiếu nhập
    public function view($id) {
        header('Content-Type: application/json');
        
        $info = $this->model->getInfo($id);
        if (!$info) {
            echo json_encode(["status" => "error", "message" => "Không tìm thấy phiếu nhập"]);
            exit;
        }

        $details = $this->model->getDetails($id);
        
        echo json_encode([
            "status" => "success",
            "info" => $info,
            "details" => $details
        ]);
        exit;
    }

    // Tạo mới phiếu nhập
    public function add() {
        header('Content-Type: application/json');
        
        // Đảm bảo session đã được khởi tạo
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data || empty($data['id_ncc']) || empty($data['items'])) {
                echo json_encode(["status" => "error", "message" => "Dữ liệu không đầy đủ"]);
                exit;
            }

            // LẤY ID NHÂN VIÊN TỪ SESSION USERMODEL (khớp với hàm login của bạn)
            if (isset($_SESSION['id_nhanvien']) && !empty($_SESSION['id_nhanvien'])) {
                $data['id_nhanvien_login'] = $_SESSION['id_nhanvien'];
            } else {
                echo json_encode([
                    "status" => "error", 
                    "message" => "Không tìm thấy phiên làm việc của nhân viên. Vui lòng đăng nhập lại!"
                ]);
                exit;
            }

            $result = $this->model->create($data);
            echo json_encode($result);
            exit;
        }
    }

    // Xem chi tiết phiếu nhập
    public function detail() { // Đổi tên hàm thành detail để khớp với JS: /api/PhieuNhap/detail
        header('Content-Type: application/json');
        
        $id = $_GET['id'] ?? null; // Lấy ID từ query string (?id=...)
        
        if (!$id) {
            echo json_encode(["status" => "error", "message" => "Thiếu ID phiếu nhập"]);
            exit;
        }

        $info = $this->model->getInfo($id);
        if (!$info) {
            echo json_encode(["status" => "error", "message" => "Không tìm thấy phiếu nhập"]);
            exit;
        }

        $details = $this->model->getDetails($id);
        
        // Format lại ngày tháng trước khi gửi về giao diện (nếu muốn)
        $info['ngay_nhap'] = date('d/m/Y H:i', strtotime($info['ngay_nhap']));

        echo json_encode([
            "status" => "success",
            "info" => $info,
            "details" => $details
        ]);
        exit;
    }

    public function update() {
    header('Content-Type: application/json');
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data || empty($data['id']) || empty($data['items'])) {
            echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ"]);
            exit;
        }

        $result = $this->model->update($data);
        echo json_encode($result);
        exit;
    }
}
}