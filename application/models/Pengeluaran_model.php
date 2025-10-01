<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengeluaran_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('id_helper');
        $this->load->library('KasKecilService');
    }

    // 🔹 Create pengeluaran
    public function create($data)
    {
        $this->db->trans_start();

        $cabang_id = $data['cabang_id'];
        $tanggal   = $data['tanggal'];
        $total     = floatval($data['total'] ?? 0);

        // header
        $existing = $this->db->get_where('pengeluaran', [
            'cabang_id' => $cabang_id,
            'tanggal'   => $tanggal
        ])->row();

        if ($existing) {
            $id = $existing->id;
            $new_total = floatval($existing->total) + $total;
            $this->db->where('id', $id)->update('pengeluaran', [
                'total'      => $new_total,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $id = generate_id_with_date('EXP', 'pengeluaran');
            $this->db->insert('pengeluaran', [
                'id'        => $id,
                'cabang_id' => $cabang_id,
                'tanggal'   => $tanggal,
                'total'     => $total,
                'created_at'=> date('Y-m-d H:i:s')
            ]);
        }

        // details
        foreach ($data['items'] as $item) {
            $detail_id = generate_id_with_date('EXD', 'pengeluaran_detail');
            $this->db->insert('pengeluaran_detail', [
                'id'             => $detail_id,
                'pengeluaran_id' => $id,
                'kategori'       => $item['kategori'] ?? null,
                'keterangan'     => $item['keterangan'] ?? null,
                'jumlah'         => floatval($item['jumlah'] ?? 0)
            ]);
        }

        // update kas_kecil
        if ($total > 0) {
            $this->kaskecilservice->debit($cabang_id, $tanggal, $total);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) return false;
        return $id;
    }

    // 🔹 Update pengeluaran (full update)
    public function update($id, $data)
    {
        $this->db->trans_start();

        $header = $this->db->get_where('pengeluaran', ['id' => $id])->row();
        if (!$header) {
            $this->db->trans_complete();
            return false;
        }

        $old_total = floatval($header->total ?? 0);
        $new_total = floatval($data['total'] ?? $old_total);
        $cabang_id = $header->cabang_id;
        $tanggal   = $header->tanggal;

        // update header
        $this->db->where('id', $id)->update('pengeluaran', [
            'total'      => $new_total,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // reset details
        $this->db->where('pengeluaran_id', $id)->delete('pengeluaran_detail');
        foreach ($data['items'] as $item) {
            $detail_id = generate_id_with_date('EXD', 'pengeluaran_detail');
            $this->db->insert('pengeluaran_detail', [
                'id'             => $detail_id,
                'pengeluaran_id' => $id,
                'kategori'       => $item['kategori'] ?? null,
                'keterangan'     => $item['keterangan'] ?? null,
                'jumlah'         => floatval($item['jumlah'] ?? 0)
            ]);
        }

        // adjust kas kecil
        $this->kaskecilservice->adjust($cabang_id, $tanggal, $old_total, $new_total);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // 🔹 Update hanya 1 detail
    public function update_detail($detail_id, $data)
    {
        $this->db->trans_start();

        $detail = $this->db->get_where('pengeluaran_detail', ['id' => $detail_id])->row();
        if (!$detail) {
            $this->db->trans_complete();
            return false;
        }

        $pengeluaran = $this->db->get_where('pengeluaran', ['id' => $detail->pengeluaran_id])->row();
        if (!$pengeluaran) {
            $this->db->trans_complete();
            return false;
        }

        $old_jumlah = floatval($detail->jumlah ?? 0);
        $new_jumlah = isset($data['jumlah']) ? floatval($data['jumlah']) : $old_jumlah;

        // update detail (hanya field yang dikirim)
        $update_data = [];
        if (isset($data['kategori']))   $update_data['kategori']   = $data['kategori'];
        if (isset($data['keterangan'])) $update_data['keterangan'] = $data['keterangan'];
        if (isset($data['jumlah']))     $update_data['jumlah']     = $new_jumlah;

        if (!empty($update_data)) {
            $this->db->where('id', $detail_id)->update('pengeluaran_detail', $update_data);
        }

        // hitung ulang total header
        $total_new = $this->db->select_sum('jumlah')
                        ->where('pengeluaran_id', $pengeluaran->id)
                        ->get('pengeluaran_detail')
                        ->row()->jumlah;

        $old_total = floatval($pengeluaran->total ?? 0);
        $this->db->where('id', $pengeluaran->id)->update('pengeluaran', [
            'total'      => $total_new,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // adjust kas kecil
        $this->kaskecilservice->adjust(
            $pengeluaran->cabang_id,
            $pengeluaran->tanggal,
            $old_total,
            $total_new
        );

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // 🔹 Delete pengeluarann
    public function delete($id)
    {
        $this->db->trans_start();

        $header = $this->db->get_where('pengeluaran', ['id' => $id])->row();
        if (!$header) {
            $this->db->trans_complete();
            return false;
        }

        $cabang_id = $header->cabang_id;
        $tanggal   = $header->tanggal;
        $total     = floatval($header->total ?? 0);

        $this->db->where('pengeluaran_id', $id)->delete('pengeluaran_detail');
        $this->db->where('id', $id)->delete('pengeluaran');

        if ($total > 0) {
            $this->kaskecilservice->credit($cabang_id, $tanggal, $total);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}
