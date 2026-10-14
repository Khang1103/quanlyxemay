<?php
// Nạp Model Nhân viên
require_once _DIR_ROOT . '/app/Models/NhanVien.php';

class NhanVien {
    private $model;

    public function __construct() {
        $this->model = new NhanVienModel();
    }

    // 1. Lấy danh sách nhân viên: .../api/NhanVien/list
    public function list() {
        header('Content-Type: application/json');
        try {
            $data = $this->model->getAll();
            echo json_encode($data);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;
    }

    // 2. Lấy chi tiết 1 nhân viên: .../api/NhanVien/view/1
    public function view($id) {
        header('Content-Type: application/json');
        $data = $this->model->getById($id);
        
        if ($data) {
            echo json_encode(["status" => "success", "data" => $data]);
        } else {
            echo json_encode(["status" => "error", "message" => "Không tìm thấy nhân viên"]);
        }
        exit;
    }

    // 3. Thêm nhân viên mới: .../api/NhanVien/add (Method: POST)
    public function add() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (empty($data['ten_nv']) || empty($data['sdt'])) {
                echo json_encode(["status" => "error", "message" => "Vui lòng nhập tên và số điện thoại"]);
                exit;
            }

            if ($this->model->create($data)) {
                echo json_encode(["status" => "success", "message" => "Thêm nhân viên thành công"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Không thể thêm nhân viên"]);
            }
            exit;
        }
    }

    // 4. Cập nhật nhân viên: .../api/NhanVien/update/1 (Method: POST/PUT)
    public function update($id) {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if ($this->model->update($id, $data)) {
                echo json_encode(["status" => "success", "message" => "Cập nhật thành công"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Cập nhật thất bại"]);
            }
            exit;
        }
    }

    public function delete($id) {
        header('Content-Type: application/json');

        // 1. Kiểm tra ràng buộc dữ liệu
        if ($this->model->hasWorkHistory($id)) {
            echo json_encode([
                "status" => "error", 
                "message" => "Không thể xóa nhân viên này vì đã có tên trên các hóa đơn hoặc phiếu nhập kho."
            ]);
            exit;
        }

        // 2. Thực hiện xóa vĩnh viễn
        $result = $this->model->delete($id);
        if ($result) {
            echo json_encode(["status" => "success", "message" => "Đã xóa nhân viên khỏi hệ thống."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống, không thể thực hiện lệnh xóa."]);
        }
        exit;
    }

}