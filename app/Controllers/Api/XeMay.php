<?php
// Nạp model vào để sử dụng
require_once _DIR_ROOT . '/app/Models/XeMay.php';

class XeMay {
    private $model;

    public function __construct() {
        $this->model = new XeMayModel();
    }

    // 1. Lấy danh sách (GET): .../api/XeMay/list
    public function list() {
        $data = $this->model->getAll();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Đường dẫn: .../api/XeMay/search?q=airblade
    public function search() {
        header('Content-Type: application/json');
        $q = $_GET['q'] ?? '';

        if (empty($q)) {
            echo json_encode([]);
            exit;
        }

        $data = $this->model->search($q);
        echo json_encode($data);
        exit;
    }

    // 2. LẤY DANH MỤC (MỚI): .../api/XeMay/categories
    public function categories() {
        $data = $this->model->getCategories();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // POST /api/xemay/add
    public function add() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['ten_xe'])) {
            echo json_encode(["status" => "error", "message" => "Tên xe không được để trống"]);
            return;
        }

        // 🔥 Bước kiểm tra trùng tên
        if ($this->model->checkExists($data['ten_xe'])) {
            echo json_encode([
                "status" => "error", 
                "message" => "Sản phẩm này đã có trong kho (trùng tên xe)!"
            ]);
            return;
        }

        // Nếu không trùng thì tiến hành thêm mới như cũ
        $result = $this->model->create($data);
        if ($result) {
            echo json_encode(["status" => "success", "message" => "Thêm thành công"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống không thể lưu"]);
        }
    }

    // 4. Đổi trạng thái (GET): .../api/XeMay/toggle/1/1
    public function toggle($id, $currentStatus) {
        $result = $this->model->toggleStatus($id, $currentStatus);
        header('Content-Type: application/json');
        echo json_encode(["success" => $result]);
        exit;
    }
    
    // 5. Lấy chi tiết 1 xe: .../api/XeMay/detail/1
    public function detail($id) {
        $data = $this->model->getById($id);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // 6. Cập nhật xe: .../api/XeMay/update
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            header('Content-Type: application/json');

            if (!$data || !isset($data['id'])) {
                echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu cập nhật"]);
                exit;
            }

            $id = $data['id'];
            $ten_xe = $data['ten_xe'] ?? '';

            // 1. Kiểm tra nếu tên xe bị để trống
            if (empty($ten_xe)) {
                echo json_encode(["status" => "error", "message" => "Tên xe không được để trống"]);
                exit;
            }

            // 2. 🔥 Kiểm tra trùng tên (Loại trừ ID hiện tại của chính nó)
            // Truyền $id vào tham số thứ 2 để Model biết là đang kiểm tra cho việc cập nhật
            if ($this->model->checkExists($ten_xe, $id)) {
                echo json_encode([
                    "status" => "error", 
                    "message" => "Tên xe này đã tồn tại ở một sản phẩm khác!"
                ]);
                exit;
            }

            // 3. Nếu mọi thứ ổn, tiến hành cập nhật
            $result = $this->model->update($id, $data);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Cập nhật thành công"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Không thể lưu thay đổi"]);
            }
            exit;
        }
    }

    public function delete($id) {
        header('Content-Type: application/json');

        // 1. Kiểm tra xem xe có tồn tại không
        $xe = $this->model->getById($id);
        if (!$xe) {
            echo json_encode(["status" => "error", "message" => "Sản phẩm không tồn tại"]);
            exit;
        }

        // 2. Kiểm tra ràng buộc giao dịch
        if ($this->model->hasTransactions($id)) {
            echo json_encode([
                "status" => "error", 
                "message" => "Không thể xóa! Xe này đã có trong lịch sử nhập hàng hoặc hóa đơn bán hàng."
            ]);
            exit;
        }

        // 3. Tiến hành xóa nếu không có ràng buộc
        $result = $this->model->delete($id);
        if ($result) {
            echo json_encode(["status" => "success", "message" => "Xóa sản phẩm thành công"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống, không thể xóa"]);
        }
        exit;
    }
}