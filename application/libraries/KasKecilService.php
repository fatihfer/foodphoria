<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KasKecilService
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    // 🔹 Debit kas kecil (pengeluaran)
    public function debit($cabang_id, $tanggal, $amount)
    {
        $periode = date('Y-m', strtotime($tanggal));
        $this->add_uang_keluar($cabang_id, $periode, $amount);
    }

    // 🔹 Credit kas kecil (restore)
    public function credit($cabang_id, $tanggal, $amount)
    {
        $periode = date('Y-m', strtotime($tanggal));
        $this->add_uang_masuk($cabang_id, $periode, $amount);
    }

    // 🔹 Adjust kas kecil when pengeluaran is updated
    public function adjust($cabang_id, $tanggal, $old_total, $new_total)
    {
        $diff = $new_total - $old_total;
        $periode = date('Y-m', strtotime($tanggal));
        if ($diff > 0) {
            $this->add_uang_keluar($cabang_id, $periode, $diff);
        } elseif ($diff < 0) {
            $this->add_uang_masuk($cabang_id, $periode, abs($diff));
        }
    }

    // 🔹 Add uang masuk (top-up or restore)
    public function add_uang_masuk($cabang_id, $periode, $amount)
    {
        $row = $this->CI->db->get_where('kas_kecil', [
            'cabang_id' => $cabang_id,
            'periode' => $periode
        ])->row();

        if ($row) {
            $this->CI->db->where(['cabang_id' => $cabang_id, 'periode' => $periode])
                ->update('kas_kecil', [
                    'uang_masuk' => $row->uang_masuk + $amount,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
        } else {
            $this->CI->db->insert('kas_kecil', [
                'cabang_id' => $cabang_id,
                'periode' => $periode,
                'uang_masuk' => $amount,
                'uang_keluar' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        return $this->CI->db->get_where('kas_kecil', [
            'cabang_id' => $cabang_id,
            'periode' => $periode
        ])->row_array();
    }

    // 🔹 Add uang keluar (pengeluaran)
    public function add_uang_keluar($cabang_id, $periode, $amount)
    {
        $row = $this->CI->db->get_where('kas_kecil', [
            'cabang_id' => $cabang_id,
            'periode' => $periode
        ])->row();

        if ($row) {
            $this->CI->db->where(['cabang_id' => $cabang_id, 'periode' => $periode])
                ->update('kas_kecil', [
                    'uang_keluar' => $row->uang_keluar + $amount,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
        } else {
            $this->CI->db->insert('kas_kecil', [
                'cabang_id' => $cabang_id,
                'periode' => $periode,
                'uang_masuk' => 0,
                'uang_keluar' => $amount,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        return $this->CI->db->get_where('kas_kecil', [
            'cabang_id' => $cabang_id,
            'periode' => $periode
        ])->row_array();
    }

    // 🔹 Get kas kecil balance
    public function get_balance($cabang_id, $periode)
    {
        $row = $this->CI->db->get_where('kas_kecil', [
            'cabang_id' => $cabang_id,
            'periode' => $periode
        ])->row();

        if ($row) {
            return floatval($row->uang_masuk) - floatval($row->uang_keluar);
        }
        return 0;
    }
}
