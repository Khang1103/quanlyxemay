<?php
require_once _DIR_ROOT . '/app/Models/User.php';

class User {
    private $model;

    public function __construct() {
        $this->model = new UserModel();
        header('Content-Type: application/json');
    }

    // POST /api/user/login
    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(["message" => "Dữ liệu không hợp lệ"]);
            return;
        }

        $result = $this->model->login($data['username'] ?? '', $data['password'] ?? '');

        // ❌ KHÔNG trả hash
        if ($result['status'] === 'error') {
            echo json_encode([
                "status" => "error",
                "message" => "Sai tài khoản hoặc mật khẩu"
            ]);
        } else {
            echo json_encode($result);
        }
    }

    // GET /api/user
    public function index() {
        if (UserModel::isLoggedIn()) {
            echo json_encode([
                "status" => "success",
                "user" => [
                    "id" => $_SESSION['user_id'],
                    "id_nhanvien" => $_SESSION['id_nhanvien'],
                    "name" => $_SESSION['user_name'],
                    "role" => $_SESSION['user_role']
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Chưa đăng nhập"]);
        }
    }

    // DELETE /api/user/logout
    public function destroy() {
        echo json_encode($this->model->logout());
    }

    // GET /api/user/list
    public function list() {
        // Bạn cần viết hàm getAll() trong UserModel
        $users = $this->model->getAll(); 
        echo json_encode($users);
    }

    public function nhanvien() {
        header('Content-Type: application/json');
        echo json_encode($this->model->getNhanVienList());
        exit;
    }

    // POST /api/user/create
    public function create() {
        // Chỉ Admin mới có quyền tạo tài khoản
        if (!UserModel::isAdmin()) {
            http_response_code(403);
            echo json_encode(["status" => "error", "message" => "Bạn không có quyền thực hiện thao tác này"]);
            return;
        }

        // Lấy dữ liệu JSON từ request body
        $data = json_decode(file_get_contents("php://input"), true);

        // Kiểm tra dữ liệu đầu vào cơ bản
        if (empty($data['username']) || empty($data['password']) || empty($data['id_nhanvien'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Vui lòng nhập đầy đủ thông tin"]);
            return;
        }

        // Gọi hàm create trong Model đã có của bạn
        $result = $this->model->create($data);

        if ($result['status'] === 'success') {
            echo json_encode($result);
        } else {
            http_response_code(400);
            echo json_encode($result);
        }
    }

    public function detail($id = null) {
    header('Content-Type: application/json');

    // 1. Kiểm tra đăng nhập trước
    if (!UserModel::isLoggedIn()) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Vui lòng đăng nhập"]);
        return;
    }

    // 2. Ưu tiên lấy ID từ tham số truyền vào, nếu không có thì lấy từ $_GET, cuối cùng mới soi URL
    if (!$id) {
        $id = $_GET['id'] ?? null;
    }

    if (!$id) {
        $url = $_SERVER['REQUEST_URI'];
        $parts = explode('/', rtrim($url, '/'));
        $lastPart = end($parts);
        if (is_numeric($lastPart)) {
            $id = $lastPart;
        }
    }

    // 3. Kiểm tra ID có hợp lệ không
    if (!$id || !is_numeric($id)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID tài khoản không hợp lệ: " . $id]);
        return;
    }

    // 4. Lấy dữ liệu từ Model
    $user = $this->model->getById($id);

    if ($user) {
        // Trả về object trực tiếp để khớp với code fetch(res => res.json()) của Hào
        echo json_encode($user); 
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Tài khoản không tồn tại"]);
    }
    exit; // Ngắt để không bị dính giao diện nếu có
}

    // POST /api/user/update
public function update() {
    if (!UserModel::isAdmin()) {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "Không có quyền"]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;

    if (!$id) {
        echo json_encode(["status" => "error", "message" => "Thiếu ID"]);
        return;
    }

    echo json_encode($this->model->update($id, $data));
}

// POST /api/user/delete
public function delete() {
    if (!UserModel::isAdmin()) {
        echo json_encode(["status" => "error", "message" => "Không có quyền"]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;

    if (!$id) {
        echo json_encode(["status" => "error", "message" => "Thiếu ID tài khoản"]);
        return;
    }

    // Truyền vào trạng thái 0 (Đã xóa/Khóa)
    echo json_encode($this->model->changeStatus($id, 0));
}
}