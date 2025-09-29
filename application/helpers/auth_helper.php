<?php
defined('BASEPATH') or exit('No direct script access allowed');

function require_role($role)
{
    $CI =& get_instance();

    // Example: role stored in session
    $user_role = $CI->session->userdata('role');

    if ($user_role !== $role) {
        $CI->output
            ->set_status_header(403)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "status" => false,
                "message" => "Access denied. Only $role can access this endpoint."
            ]))
            ->_display();
        exit; // stop execution
    }
}
