<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('generate_id_with_date')) {
    /**
     * Generate ID with date-based prefix.
     * Example: EXP-20250929-0001 or EXD-20250929-0001
     * 
     * @param string $prefix EXP or EXD
     * @param string $table table name to query last ID
     * @param string $column column name (default 'id')
     * @return string new ID
     */
    function generate_id_with_date($prefix, $table, $column = 'id')
    {
        $CI =& get_instance();
        $CI->load->database();

        $date_str = date('Ymd'); // current date
        $full_prefix = $prefix . '-' . $date_str . '-';

        // select last ID that starts with today's prefix
        $CI->db->select($column);
        $CI->db->like($column, $full_prefix, 'after');
        $CI->db->order_by($column, 'DESC');
        $CI->db->limit(1);
        $query = $CI->db->get($table);

        if ($query->num_rows() > 0) {
            $last_id = $query->row()->$column;
            $num = (int) substr($last_id, strlen($full_prefix));
            $next = $num + 1;
        } else {
            $next = 1;
        }

        return $full_prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}

// ────────────── PENDAPATAN SPECIFIC IDs ────────────── //

if (!function_exists('generate_pof_id')) {
    function generate_pof_id($date)
    {
        $CI =& get_instance();
        $CI->load->database();
        return generate_id_with_date('POF', 'pendapatan_offline', 'id');
    }
}

if (!function_exists('generate_pon_id')) {
    function generate_pon_id($date)
    {
        $CI =& get_instance();
        $CI->load->database();
        return generate_id_with_date('PON', 'pendapatan_online', 'id');
    }
}

if (!function_exists('generate_pod_id')) {
    function generate_pod_id($date)
    {
        $CI =& get_instance();
        $CI->load->database();
        return generate_id_with_date('POD', 'pendapatan_online_detail', 'id');
    }
}
