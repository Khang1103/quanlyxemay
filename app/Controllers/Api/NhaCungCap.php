<?php
// Nạp model vào để sử dụng
require_once _DIR_ROOT . '/app/Models/NhaCungCap.php';

class NhaCungCap {
    private $model;

    public function __construct() {
        $this->model = new NhaCungCapModel();
    }

    // 1. Lấy danh sách NCC: .../api/NhaCungCap/list
    public function list() {
        $data = $this->model->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // 2. Lấy chi tiết 1 NCC: .../api/NhaCungCap/detail/5
    public function detail($id) {
        $data = $this->model->getById($id);
        header('Content-Type: application/json');
        if ($data) {
            echo json_encode($data);
        } else {
            echo json_encode(["status" => "error", "message" => "Không tìm thấy nhà cung cấp"]);
        }
        exit;
    }

    // 3. Thêm mới NCC
    public function add() {
        $data = json_decode(file_get_contents('php://input'), true);
        header('Content-Type: application/json');

        if (empty($data['ten_ncc']) || empty($data['sdt'])) {
            echo json_encode(["status" => "error", "message" => "Vui lòng nhập tên và số điện thoại"]);
            exit;
        }

        // Kiểm tra chi tiết
        if ($this->model->checkPhone($data['sdt'])) {
            echo json_encode(["status" => "error", "message" => "Số điện thoại này đã thuộc về nhà cung cấp khác"]);
            exit;
        }
        if (!empty($data['email']) && $this->model->checkEmail($data['email'])) {
            echo json_encode(["status" => "error", "message" => "Email này đã thuộc về nhà cung cấp khác"]);
            exit;
        }

        $result = $this->model->create($data);
        echo json_encode($result ? ["status" => "success", "message" => "Thêm thành công"] : ["status" => "error", "message" => "Lỗi Database"]);
    }

    // 4. Cập nhật NCC
    public function update() {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        header('Content-Type: application/json');

        if (!$id) {
            echo json_encode(["status" => "error", "message" => "Thiếu ID"]);
            exit;
        }

        // Kiểm tra trùng sdt (trừ chính nó)
        if ($this->model->checkPhone($data['sdt'], $id)) {
            echo json_encode(["status" => "error", "message" => "Số điện thoại đã bị trùng"]);
            exit;
        }
        
        // Kiểm tra trùng email (trừ chính nó)
        if (!empty($data['email']) && $this->model->checkEmail($data['email'], $id)) {
            echo json_encode(["status" => "error", "message" => "Email đã bị trùng"]);
            exit;
        }

        $result = $this->model->update($id, $data);
        echo json_encode($result ? ["status" => "success", "message" => "Cập nhật thành công"] : ["status" => "error", "message" => "Cập nhật thất bại"]);
    }

    public function delete($id) {
        header('Content-Type: application/json');
        
        // 1. Kiểm tra xem NCC có phiếu nhập nào không
        if ($this->model->hasImports($id)) {
            echo json_encode([
                "success" => false, 
                "message" => "Không thể xóa! Nhà cung cấp này đã có lịch sử nhập hàng."
            ]);
            exit;
        }

        // 2. Thực hiện xóa
        $result = $this->model->delete($id);
        if ($result) {
            echo json_encode(["success" => true, "message" => "Xóa thành công"]);
        } else {
            echo json_encode(["success" => false, "message" => "Lỗi hệ thống không thể xóa"]);
        }
        exit;
    }

}