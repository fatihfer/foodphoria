<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pendapatan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'id_helper', 'auth']); // include auth helper
        $this->load->library('session');
        $this->load->model('Pendapatan_model');

        // Require manager_resto role
        require_role('manager_resto');
    }

    // 🔹 List all pendapatan (offline + online)
    public function index()
    {
        $data = $this->Pendapatan_model->get_all();
        echo json_encode([
            'status' => true,
            'data' => $data
        ]);
    }

    // 🔹 Create offline pendapatan
    public function create_offline()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['tanggal'], $input['penjualan_cash'], $input['cabang_id'])) {
            echo json_encode(['status' => false, 'message' => 'Invalid input']);
            return;
        }

        $result = $this->Pendapatan_model->insert_offline($input);

        if ($result) {
            $this->Pendapatan_model->update_total_omset($input['cabang_id'], $input['tanggal']);
            echo json_encode(['status' => true, 'message' => 'Pendapatan offline recorded successfully']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to record pendapatan offline']);
        }
    }

    // 🔹 Create online pendapatan with details
    public function create_online()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['tanggal'], $input['cabang_id'], $input['details']) || !is_array($input['details'])) {
            echo json_encode(['status' => false, 'message' => 'Invalid input']);
            return;
        }

        $result = $this->Pendapatan_model->insert_online($input);

        if ($result) {
            $this->Pendapatan_model->update_total_omset($input['cabang_id'], $input['tanggal']);
            echo json_encode(['status' => true, 'message' => 'Pendapatan online recorded successfully']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to record pendapatan online']);
        }
    }
}
