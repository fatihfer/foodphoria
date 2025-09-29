<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pendapatan_model extends CI_Model
{
    // 🔹 List all pendapatan (offline + online with details)
    public function get_all()
    {
        $offline = $this->db->order_by('tanggal', 'DESC')->get('pendapatan_offline')->result_array();
        $online = $this->db->order_by('tanggal', 'DESC')->get('pendapatan_online')->result_array();

        foreach ($online as &$o) {
            $o['details'] = $this->db
                ->where('pendapatan_online_id', $o['id'])
                ->get('pendapatan_online_detail')
                ->result_array();
        }

        return [
            'offline' => $offline,
            'online' => $online
        ];
    }

    // 🔹 Insert offline pendapatan
    public function insert_offline($data)
    {
        $data['id'] = generate_pof_id($data['tanggal']);
        return $this->db->insert('pendapatan_offline', $data);
    }

    // 🔹 Insert online pendapatan with details
    public function insert_online($data)
    {
        $this->db->trans_start();

        $online_id = generate_pon_id($data['tanggal']);
        $total = 0;

        foreach ($data['details'] as $d) {
            $detail_data = [
                'id' => generate_pod_id($data['tanggal']),
                'pendapatan_online_id' => $online_id,
                'sumber' => $d['sumber'],
                'keterangan' => $d['keterangan'] ?? null,
                'jumlah' => $d['jumlah']
            ];
            $total += $d['jumlah'];
            $this->db->insert('pendapatan_online_detail', $detail_data);
        }

        $this->db->insert('pendapatan_online', [
            'id' => $online_id,
            'tanggal' => $data['tanggal'],
            'total' => $total,
            'cabang_id' => $data['cabang_id']
        ]);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    // 🔹 Update total_omset automatically
    public function update_total_omset($cabang_id, $tanggal)
    {
        $offline = $this->db->select_sum('penjualan_cash')
                            ->where(['cabang_id'=>$cabang_id, 'tanggal'=>$tanggal])
                            ->get('pendapatan_offline')
                            ->row()->penjualan_cash ?? 0;

        $online = $this->db->select_sum('total')
                            ->where(['cabang_id'=>$cabang_id, 'tanggal'=>$tanggal])
                            ->get('pendapatan_online')
                            ->row()->total ?? 0;

        $total = $offline + $online;

        $existing = $this->db->get_where('total_omset', ['cabang_id'=>$cabang_id, 'tanggal'=>$tanggal])->row();

        if ($existing) {
            $this->db->update('total_omset', [
                'total_omset_offline' => $offline,
                'total_omset_online' => $online,
                'total_omset' => $total
            ], ['id'=>$existing->id]);
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
