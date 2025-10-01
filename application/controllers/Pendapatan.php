<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pendapatan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pendapatan_model');
    }

    // ✅ Tambah pendapatan offline
    public function add_offline()
    {
        $data = json_decode($this->input->raw_input_stream, true);

        $result = $this->Pendapatan_model->insert_offline($data);

        echo json_encode($result);
    }

    // ✅ Tambah pendapatan online
    public function add_online()
    {
        $data = json_decode($this->input->raw_input_stream, true);

        $result = $this->Pendapatan_model->insert_online($data);

        echo json_encode($result);
    }

    // ✅ Tambah detail online
    public function add_online_detail()
    {
        $data = json_decode($this->input->raw_input_stream, true);

        $result = $this->Pendapatan_model->insert_online_detail($data);

        echo json_encode($result);
    }

    // ✅ Edit detail online
    public function update_online_detail($id)
    {
        $data = json_decode($this->input->raw_input_stream, true);

        $result = $this->Pendapatan_model->update_online_detail($id, $data);

        echo json_encode($result);
    }
}
