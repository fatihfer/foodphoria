<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url','form','id']); // ✅ helper id dipanggil
        $this->load->library(['session']);
        $this->load->model('User_model');
    }

    // ======================
    // Login
    // ======================
    public function login()
    {
        $data = json_decode($this->input->raw_input_stream, true);
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        $user = $this->User_model->get_by_username($username);

        if (!$user) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Username tidak ditemukan"
                ]));
        }

        if (!password_verify($password, $user->password)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Password salah"
                ]));
        }

        // set session
        $this->session->set_userdata([
            'user_id'   => $user->id,
            'username'  => $user->username,
            'role'      => $user->role,
            'cabang_id' => $user->cabang_id
        ]);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "status" => true,
                "message" => "Login berhasil",
                "data" => [
                    "username"  => $user->username,
                    "role"      => $user->role,
                    "cabang_id" => $user->cabang_id
                ]
            ]));
    }

    // ======================
    // Register (hanya super_admin)
    // ======================
    public function register()
    {
        if ($this->session->userdata('role') !== 'super_admin') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Hanya super_admin yang bisa register user baru"
                ]));
        }

        $data = json_decode($this->input->raw_input_stream, true);
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;
        $role     = $data['role'] ?? 'manager_resto'; // default manager
        $cabang_id= $data['cabang_id'] ?? null;

        // validasi
        if (!$username || !$password || !$role || !$cabang_id) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Data tidak lengkap"
                ]));
        }

        if ($this->User_model->get_by_username($username)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(409)
                ->set_output(json_encode([
                    "status" => false,
                    "message" => "Username sudah digunakan"
                ]));
        }

        // generate ID baru
        $last_id = $this->User_model->get_last_user_id();
        $new_id  = generate_user_id($last_id);

        $this->User_model->insert_user([
            'id'        => $new_id,
            'username'  => $username,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
            'role'      => $role,
            'cabang_id' => $cabang_id,
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s')
        ]);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "status" => true,
                "message" => "User berhasil dibuat",
                "data" => [
                    "id"        => $new_id,
                    "username"  => $username,
                    "role"      => $role,
                    "cabang_id" => $cabang_id
                ]
            ]));
    }
}
