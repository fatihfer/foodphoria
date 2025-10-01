<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pendapatan_model extends CI_Model
{
    // ✅ Generate ID format
    private function generate_id($prefix)
    {
        $date = date('Ymd');
        $count = $this->db->like('id', $prefix . '-' . $date, 'after')->from($this->db->get_compiled_select())->count_all_results();
        $number = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . '-' . $date . '-' . $number;
    }

    // ✅ Insert pendapatan offline
    public function insert_offline($data)
    {
        $id = $this->generate_id("POF");
        $insert = [
            'id' => $id,
            'cabang_id' => $data['cabang_id'],
            'tanggal' => $data['tanggal'],
            'jumlah' => $data['jumlah'],
            'keterangan' => $data['keterangan'] ?? null
        ];
        $this->db->insert('pendapatan_offline', $insert);

        $this->update_total_omset($data['cabang_id'], $data['tanggal']);

        return ["status" => true, "id" => $id];
    }

    // ✅ Insert pendapatan online
    public function insert_online($data)
    {
        $id = $this->generate_id("PON");
        $insert = [
            'id' => $id,
            'cabang_id' => $data['cabang_id'],
            'tanggal' => $data['tanggal'],
            'jumlah' => $data['jumlah'],
            'platform' => $data['platform'] ?? null
        ];
        $this->db->insert('pendapatan_online', $insert);

        $this->update_total_omset($data['cabang_id'], $data['tanggal']);

        return ["status" => true, "id" => $id];
    }

    // ✅ Insert detail online
    public function insert_online_detail($data)
    {
        $id = $this->generate_id("POD");
        $insert = [
            'id' => $id,
            'pendapatan_online_id' => $data['pendapatan_online_id'],
            'jumlah' => $data['jumlah'],
            'keterangan' => $data['keterangan'] ?? null
        ];
        $this->db->insert('pendapatan_online_detail', $insert);

        // Update total pendapatan_online
        $this->update_online_total($data['pendapatan_online_id']);

        return ["status" => true, "id" => $id];
    }

    // ✅ Update detail online
    public function update_online_detail($id, $data)
    {
        $this->db->where('id', $id)->update('pendapatan_online_detail', [
            'jumlah' => $data['jumlah'],
            'keterangan' => $data['keterangan'] ?? null
        ]);

        // Ambil parent online
        $parent = $this->db->get_where('pendapatan_online', ['id' =>
            $data['pendapatan_online_id']])->row();

        if ($parent) {
            $this->update_online_total($parent->id);
        }

        return ["status" => true, "id" => $id];
    }

    // ✅ Hitung ulang total pendapatan_online
    private function update_online_total($online_id)
    {
        $total = $this->db->select_sum('jumlah')
            ->from('pendapatan_online_detail')
            ->where('pendapatan_online_id', $online_id)
            ->get()->row()->jumlah ?? 0;

        $this->db->where('id', $online_id)
            ->update('pendapatan_online', ['jumlah' => $total]);

        // Update juga total omset
        $online = $this->db->get_where('pendapatan_online', ['id' => $online_id])->row();
        if ($online) {
            $this->update_total_omset($online->cabang_id, $online->tanggal);
        }
    }

    // ✅ Update total omset
    public function update_total_omset($cabang_id, $tanggal)
    {
        if (empty($cabang_id)) {
            $cabang_id = "CBG-001"; // fallback default
        }

        $offline = $this->db->select_sum('jumlah')
            ->from('pendapatan_offline')
            ->where('cabang_id', $cabang_id)
            ->where('DATE(tanggal)', $tanggal)
            ->get()->row()->jumlah ?? 0;

        $online = $this->db->select_sum('jumlah')
            ->from('pendapatan_online')
            ->where('cabang_id', $cabang_id)
            ->where('DATE(tanggal)', $tanggal)
            ->get()->row()->jumlah ?? 0;

        $total = $offline + $online;

        $cek = $this->db->get_where('total_omset', [
            'cabang_id' => $cabang_id,
            'tanggal' => $tanggal
        ])->row();

        if ($cek) {
            $this->db->where('id', $cek->id)->update('total_omset', [
                'total_omset_offline' => $offline,
                'total_omset_online' => $online,
                'total_omset' => $total
            ]);
        } else {
            $this->db->insert('total_omset', [
                'cabang_id' => $cabang_id,
                'tanggal' => $tanggal,
                'total_omset_offline' => $offline,
                'total_omset_online' => $online,
                'total_omset' => $total
            ]);
        }
    }
}
