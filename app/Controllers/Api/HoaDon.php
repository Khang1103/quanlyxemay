<?php
// Nạp Model Hóa đơn
require_once _DIR_ROOT . '/app/Models/HoaDon.php';

class HoaDon {
    private $model;

    public function __construct() {
        $this->model = new HoaDonModel();
    }

    // 1. Lấy danh sách hóa đơn: .../api/HoaDon/list
    public function list() {
        header('Content-Type: application/json');
        try {
            $data = $this->model->getAll();
            // Đảm bảo trả về mảng trực tiếp hoặc bọc trong status success
            echo json_encode($data);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;
    }

    // 2. Xem chi tiết 1 hóa đơn: .../api/HoaDon/view?id=5
    public function view() {
        header('Content-Type: application/json');
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                throw new Exception("Thiếu mã hóa đơn");
            }

            $info = $this->model->getById($id);
            if ($info) {
                $details = $this->model->getDetails($id);
                echo json_encode([
                    "status" => "success",
                    "info" => $info,
                    "details" => $details
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "Không tìm thấy hóa đơn"]);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;
    }

    // 3. Tạo mới hóa đơn (Bán hàng): .../api/HoaDon/create (POST)
    public function create() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (empty($data['id_khachhang']) || empty($data['id_nhanvien']) || empty($data['items'])) {
                echo json_encode([
                    "status" => "error", 
                    "message" => "Vui lòng chọn khách hàng, nhân viên và ít nhất 1 sản phẩm"
                ]);
                exit;
            }

            // Gọi hàm create (đã có Transaction và kiểm tra tồn kho trong Model)
            $result = $this->model->create($data);
            echo json_encode($result);
        } else {
            echo json_encode(["status" => "error", "message" => "Method không được hỗ trợ"]);
        }
        exit;
    }

    // 4. Hủy hóa đơn: .../api/HoaDon/delete (POST hoặc GET tùy bạn route)
    // Chức năng quan trọng để giải phóng kho khi cần sửa phiếu nhập
    public function delete() {
        header('Content-Type: application/json');
        try {
            // Lấy ID từ POST (khuyên dùng) hoặc GET
            $id = $_POST['id'] ?? ($_GET['id'] ?? null);
            
            if (!$id) {
                throw new Exception("Thiếu mã hóa đơn cần hủy");
            }

            $result = $this->model->delete($id);
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;
    }

    // 5. Lấy thống kê doanh thu: .../api/HoaDon/revenue
    public function revenue() {
        header('Content-Type: application/json');
        try {
            $data = $this->model->getRevenue();
            echo json_encode(["status" => "success", "data" => $data]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;
    }
}