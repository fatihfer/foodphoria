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

    // 🔹 Create pengeluaran + detail + kas_kecil
    public function create()
    {
        require_role("manager_resto");

        $data = json_decode($this->input->raw_input_stream, true);
        if (!$data || !isset($data['cabang_id']) || !isset($data['tanggal']) || empty($data['items'])) {
            return $this->output_json(false, "Invalid data. Must include cabang_id, tanggal, and items[]");
        }

        $id = $this->Pengeluaran_model->create($data);
        if (!$id) return $this->output_json(false, "Failed to save pengeluaran");

        return $this->output_json(true, "Pengeluaran created", [
            "id" => $id,
            "cabang_id" => $data['cabang_id'],
            "tanggal" => $data['tanggal'],
            "total" => $data['total'] ?? 0,
            "items" => $data['items']
        ]);
    }

    // 🔹 List all pengeluaran (header only)
    public function index()
    {
        require_role("manager_resto");

        $result = $this->db->order_by("tanggal","DESC")->get("pengeluaran")->result();
        return $this->output_json(true, null, $result);
    }

    // 🔹 Show pengeluaran + details by ID
    public function show($id)
    {
        require_role("manager_resto");

        $header = $this->db->get_where('pengeluaran', ['id'=>$id])->row_array();
        if (!$header) return $this->output_json(false, "Data not found");

        $details = $this->db->get_where('pengeluaran_detail', ['pengeluaran_id'=>$id])->result_array();
        return $this->output_json(true, null, ['header'=>$header,'details'=>$details]);
    }

    // 🔹 Update pengeluaran + details + kas_kecil adjustment
    public function update($id)
    {
        require_role("manager_resto");

        $data = json_decode($this->input->raw_input_stream,true);
        if (!$data || !isset($data['items'])) return $this->output_json(false,"Invalid data");

        $ok = $this->Pengeluaran_model->update($id,$data);
        if (!$ok) return $this->output_json(false,"Failed to update pengeluaran");

        return $this->output_json(true,"Pengeluaran updated",["id"=>$id,"total"=>$data['total'] ?? 0,"items"=>$data['items']]);
    }

    // 🔹 Delete pengeluaran + details + kas_kecil restore
    public function delete($id)
    {
        require_role("manager_resto");

        $ok = $this->Pengeluaran_model->delete($id);
        if (!$ok) return $this->output_json(false,"Failed to delete pengeluaran");

        return $this->output_json(true,"Pengeluaran deleted");
    }

    // 🔹 Endpoint helper to top-up kas_kecil
    public function topup()
    {
        require_role("manager_resto");

        $data = json_decode($this->input->raw_input_stream,true);
        if (!$data || !isset($data['cabang_id']) || !isset($data['periode']) || !isset($data['amount'])) {
            return $this->output_json(false,"Invalid data. Must include cabang_id, periode, amount");
        }

        $row = $this->kaskecilservice->add_uang_masuk($data['cabang_id'],$data['periode'],$data['amount']);
        return $this->output_json(true,"Top-up successful",$row);
    }

    // 🔹 Helper for consistent JSON output
    private function output_json($status,$message=null,$data=null)
    {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(compact('status','message','data')));
    }
}
