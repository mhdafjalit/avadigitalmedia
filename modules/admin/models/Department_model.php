<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Get all departments with pagination and filters
     */
    public function get_departments($limit = 10, $offset = 0, $keyword = '', $status = '') {
        $this->db->select('*');
        $this->db->from('wl_departments');
        
        // Apply search filter
        if(!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('department_name', $keyword);
            $this->db->or_like('department_description', $keyword);
            $this->db->group_end();
        }
        
        // Apply status filter
        if($status !== '' && $status !== NULL) {
            $this->db->where('status', $status);
        }
        
        // Order by department_id descending
        $this->db->order_by('department_id', 'DESC');
        
        // Apply pagination
        if($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        
        // Convert to array
        $result = $query->result();
        $data = array();
        
        foreach($result as $row) {
            $data[] = array(
                'department_id' => $row->department_id,
                'department_name' => $row->department_name,
                'department_description' => $row->department_description,
                'status' => $row->status,
                'created_at' => $row->created_at
            );
        }
        
        return $data;
    }
    
    /**
     * Get all departments (simple list without pagination)
     */
    public function get_all_departments_simple() {
        $this->db->select('department_id, department_name');
        $this->db->from('wl_departments');
        $this->db->order_by('department_name', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get active departments only
     */
    public function get_active_departments() {
        $this->db->select('department_id, department_name');
        $this->db->from('wl_departments');
        $this->db->where('status', 1);
        $this->db->order_by('department_name', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Count total departments with filters
     */
    public function count_departments($keyword = '', $status = '') {
        $this->db->from('wl_departments');
        
        // Apply search filter
        if(!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('department_name', $keyword);
            $this->db->or_like('department_description', $keyword);
            $this->db->group_end();
        }
        
        // Apply status filter
        if($status !== '' && $status !== NULL) {
            $this->db->where('status', $status);
        }
        
        return $this->db->count_all_results();
    }
    
    /**
     * Get single department by ID
     */
    public function get_department_by_id($department_id) {
        $this->db->select('*');
        $this->db->from('wl_departments');
        $this->db->where('department_id', $department_id);
        $query = $this->db->get();
        
        if($query->num_rows() > 0) {
            return $query->row();
        }
        
        return null;
    }
    
    /**
     * Get department by name
     */
    public function get_department_by_name($department_name) {
        $this->db->select('*');
        $this->db->from('wl_departments');
        $this->db->where('department_name', $department_name);
        $query = $this->db->get();
        
        if($query->num_rows() > 0) {
            return $query->row();
        }
        
        return null;
    }
    
    /**
     * Insert new department
     */
    public function insert_department($data) {
        if($this->db->insert('wl_departments', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }
    
    /**
     * Update department
     */
    public function update_department($department_id, $data) {
        $this->db->where('department_id', $department_id);
        return $this->db->update('wl_departments', $data);
    }
    
    /**
     * Update department status only
     */
    public function update_department_status($department_id, $status) {
        $data = array('status' => $status);
        $this->db->where('department_id', $department_id);
        return $this->db->update('wl_departments', $data);
    }
    
    /**
     * Delete department
     */
    public function delete_department($department_id) {
        $this->db->where('department_id', $department_id);
        return $this->db->delete('wl_departments');
    }
    
    /**
     * Check for duplicate department name
     */
    public function check_duplicate_department_name($department_name, $exclude_id = null) {
        $this->db->from('wl_departments');
        $this->db->where('department_name', $department_name);
        
        if($exclude_id) {
            $this->db->where('department_id !=', $exclude_id);
        }
        
        $count = $this->db->count_all_results();
        return $count > 0;
    }
    
    /**
     * Get departments by status
     */
    public function get_departments_by_status($status) {
        $this->db->select('*');
        $this->db->from('wl_departments');
        $this->db->where('status', $status);
        $this->db->order_by('department_name', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Bulk update department status
     */
    public function bulk_update_status($department_ids, $status) {
        if(empty($department_ids)) {
            return false;
        }
        
        $data = array('status' => $status);
        $this->db->where_in('department_id', $department_ids);
        return $this->db->update('wl_departments', $data);
    }
    
    /**
     * Count users in department (if users table exists)
     */
    public function count_users_in_department($department_id) {
        // Check if users table exists
        if($this->db->table_exists('wl_users')) {
            $this->db->from('wl_users');
            $this->db->where('department_id', $department_id);
            return $this->db->count_all_results();
        }
        return 0;
    }
    
    /**
     * Get department statistics
     */
    public function get_department_stats() {
        $this->db->select('
            COUNT(*) as total,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as inactive
        ');
        $this->db->from('wl_departments');
        $query = $this->db->get();
        return $query->row();
    }
}
?>