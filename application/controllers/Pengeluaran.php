<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengeluaran extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'auth', 'id_helper']);
        $this->load->library(['session', 'KasKecilService']);
        $this->load->model('Pengeluaran_model');
    }

    // ... create, index, show, update, delete tetap sama

    // 🔹 Update satu detail
    public function update_detail($detail_id)
    {
        require_role("manager_resto");

        $data = json_decode($this->input->raw_input_stream, true);
        if (!$data) return $this->output_json(false, "No data provided");

        $ok = $this->Pengeluaran_model->update_detail($detail_id, $data);
        if (!$ok) return $this->output_json(false, "Failed to update detail");

        return $this->output_json(true, "Detail updated", [
            "detail_id" => $detail_id,
            "updated"   => $data
        ]);
    }

    private function output_json($status,$message=null,$data=null)
    {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(compact('status','message','data')));
    }
}
