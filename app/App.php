<?php
class App {
    public function __construct() {
        $url = $this->urlProcess();

        // TRƯỜNG HỢP 1: GỌI API (Giữ nguyên vì đây là REST API)
        if (isset($url[0]) && strtolower($url[0]) == 'api') {
            array_shift($url); // Bỏ chữ 'api'
            $controllerName = $url[0] ?? 'Home';
            $path = _DIR_ROOT . "/app/Controllers/Api/" . $controllerName . ".php";
            
            if (file_exists($path)) {
                require_once $path;
                $controller = new $controllerName;
                array_shift($url);
                $method = $url[0] ?? 'index';
                array_shift($url);
                $params = $url ? array_values($url) : [];
                call_user_func_array([$controller, $method], $params);
            }
            return; // Chạy xong API thì dừng luôn
        }

        // TRƯỜNG HỢP 2: GỌI GIAO DIỆN (Bỏ qua Controller Web, nhảy thẳng vào View)
        // Link ví dụ: localhost/public/XeMay -> Tìm file views/XeMay.php
        $viewName = !empty($url[0]) ? $url[0] : 'Login'; 
        $viewPath = _DIR_ROOT . "/views/" . $viewName . ".php";

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            echo "Trang 404 - Không tìm thấy giao diện!";
        }
    }

    function urlProcess() {
        if (isset($_GET["url"])) {
            return explode("/", filter_var(trim($_GET["url"], "/")));
        }
        return [];
    }
}