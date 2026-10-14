<?php
// Nạp model vào để sử dụng
require_once _DIR_ROOT . '/app/Models/KhachHang.php';

class KhachHang {
    private $model;

    public function __construct() {
        $this->model = new KhachHangModel();
    }

    // 1. Lấy danh sách khách hàng: .../api/KhachHang/list
    public function list() {
        $data = $this->model->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Đường dẫn: .../api/KhachHang/search?q=hao
    public function search() {
        header('Content-Type: application/json');
        $q = $_GET['q'] ?? '';

        if (empty($q)) {
            echo json_encode([]);
            exit;
        }

        // Gọi hàm search Hào vừa thêm vào Model
        $data = $this->model->search($q);
        echo json_encode($data);
        exit;
    }

    // 2. Lấy chi tiết 1 khách hàng: .../api/KhachHang/detail/5
    public function detail($id) {
        $data = $this->model->getById($id);
        header('Content-Type: application/json');
        if ($data) {
            echo json_encode($data);
        } else {
            echo json_encode(["status" => "error", "message" => "Không tìm thấy khách hàng"]);
        }
        exit;
    }

    // 3. Thêm mới khách hàng: .../api/KhachHang/add
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data || empty($data['ten_kh']) || empty($data['sdt'])) {
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "Vui lòng nhập đầy đủ tên và số điện thoại"]);
                exit;
            }

            // Kiểm tra trùng SDT (sử dụng hàm isExists đã thêm ở Model)
            if ($this->model->isExists($data['sdt'])) {
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "Số điện thoại đã tồn tại trên hệ thống"]);
                exit;
            }

            $result = $this->model->create($data);
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(["status" => "success", "message" => "Thêm khách hàng thành công"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Lỗi lưu Database"]);
            }
            exit;
        }
    }

    // 4. Cập nhật khách hàng: .../api/KhachHang/update
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $id = $data['id'] ?? null;
            if (!$id) {
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "Thiếu ID khách hàng"]);
                exit;
            }

            // Kiểm tra trùng SDT/Email với người khác
            if ($this->model->isExists($data['sdt'], $id)) {
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "Số điện thoại này đã thuộc về khách hàng khác"]);
                exit;
            }

            $result = $this->model->update($id, $data);
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(["status" => "success", "message" => "Cập nhật thông tin khách hàng thành công"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Cập nhật thất bại"]);
            }
            exit;
        }
    }

    public function delete($id) {
        header('Content-Type: application/json');

        // 1. Kiểm tra ràng buộc: Nếu đã mua hàng thì không cho xóa để giữ lịch sử
        if ($this->model->hasInvoices($id)) {
            echo json_encode([
                "success" => false, 
                "message" => "Không thể xóa khách hàng này vì đã có lịch sử mua hàng trên hệ thống."
            ]);
            exit;
        }

        // 2. Thực hiện xóa
        $result = $this->model->delete($id);
        if ($result) {
            echo json_encode(["success" => true, "message" => "Đã xóa khách hàng thành công."]);
        } else {
            echo json_encode(["success" => false, "message" => "Lỗi hệ thống, không thể xóa khách hàng."]);
        }
        exit;
    }
}