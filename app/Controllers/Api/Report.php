<?php
require_once _DIR_ROOT . '/app/Models/ReportModel.php';

class Report {
    private $model;

    public function __construct() {
        $this->model = new ReportModel();
        header('Content-Type: application/json');
    }

    public function monthly() {
        $month = $_GET['month'] ?? date('Y-m');
        $data = $this->model->getMonthlyStats($month);
        echo json_encode($data);
    }
}