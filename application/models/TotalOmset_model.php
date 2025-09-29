<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TotalOmset_model extends CI_Model
{
    private $view = "total_omset";

    public function get_by_date($cabang_id, $tanggal)
    {
        return $this->db->get_where($this->view, [
            "cabang_id" => $cabang_id,
            "tanggal"   => $tanggal
        ])->row();
    }

    public function get_bulanan($cabang_id, $periode)
    {
        $this->db->where("cabang_id", $cabang_id);
        $this->db->where("DATE_FORMAT(tanggal, '%Y-%m') =", $periode);
        return $this->db->get($this->view)->result();
    }
}
