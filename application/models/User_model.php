<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    protected $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    // ======================
    // Ambil user by username
    // ======================
    public function get_by_username($username)
    {
        return $this->db->get_where($this->table, ['username' => $username])->row();
    }

    // ======================
    // Ambil user by ID
    // ======================
    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    // ======================
    // Ambil semua user
    // ======================
    public function get_all()
    {
        return $this->db->get($this->table)->result();
    }

    // ======================
    // Insert user baru
    // ======================
    public function insert_user($data)
    {
        return $this->db->insert($this->table, $data);
    }

    // ======================
    // Update user
    // ======================
    public function update_user($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    // ======================
    // Hapus user
    // ======================
    public function delete_user($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    // ======================
    // Ambil ID terakhir (buat generate USR-xxx)
    // ======================
    public function get_last_user_id()
    {
        $this->db->select('id');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get($this->table);
        return $query->row() ? $query->row()->id : null;
    }
}
