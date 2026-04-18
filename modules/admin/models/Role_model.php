<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Get all roles with pagination and filters
     */
    public function get_roles($limit = 10, $offset = 0, $keyword = '', $status = '') {
        $this->db->select('*');
        $this->db->from('wl_roles');
        $this->db->order_by('role_id', 'ACS');        
        // Apply search filter
        if(!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('role_name', $keyword);
            $this->db->or_like('role_description', $keyword);
            $this->db->group_end();
        }
        
        // Apply status filter
        if($status !== '' && $status !== NULL) {
            $this->db->where('status', $status);
        }
        
        // Order by role_id descending
        $this->db->order_by('role_id', 'DESC');
        
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
                'role_id' => $row->role_id,
                'role_name' => $row->role_name,
                'role_description' => $row->role_description,
                'status' => $row->status,
                'created_by' => $row->created_by,
                'created_date' => $row->created_date,
                'updated_date' => $row->updated_date
            );
        }
        
        return $data;
    }
    
    /**
     * Get all roles (simple list without pagination)
     */
    public function get_all_roles_simple() {
        $this->db->select('role_id, role_name');
        $this->db->from('wl_roles');
        $this->db->order_by('role_name', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Count total roles with filters
     */
    public function count_roles($keyword = '', $status = '') {
        $this->db->from('wl_roles');
        
        // Apply search filter
        if(!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('role_name', $keyword);
            $this->db->or_like('role_description', $keyword);
            $this->db->group_end();
        }
        
        // Apply status filter
        if($status !== '' && $status !== NULL) {
            $this->db->where('status', $status);
        }
        
        return $this->db->count_all_results();
    }
    
    /**
     * Get single role by ID
     */
    public function get_role_by_id($role_id) {
        $this->db->select('*');
        $this->db->from('wl_roles');
        $this->db->where('role_id', $role_id);
        $query = $this->db->get();
        
        if($query->num_rows() > 0) {
            return $query->row();
        }
        
        return null;
    }
    
    /**
     * Get role by name
     */
    public function get_role_by_name($role_name) {
        $this->db->select('*');
        $this->db->from('wl_roles');
        $this->db->where('role_name', $role_name);
        $query = $this->db->get();
        
        if($query->num_rows() > 0) {
            return $query->row();
        }
        
        return null;
    }
    
    /**
     * Insert new role
     */
    public function insert_role($data) {
        if($this->db->insert('wl_roles', $data)) {
            return $this->db->insert_id();
        }
        
        return false;
    }
    
    /**
     * Update role
     */
    public function update_role($role_id, $data) {
        $this->db->where('role_id', $role_id);
        return $this->db->update('wl_roles', $data);
    }
    
    /**
     * Update role status only
     */
    public function update_role_status($role_id, $status) {
        $data = array(
            'status' => $status,
            'updated_date' => date('Y-m-d H:i:s')
        );
        
        $this->db->where('role_id', $role_id);
        return $this->db->update('wl_roles', $data);
    }
    
    /**
     * Delete role
     */
    public function delete_role($role_id) {
        $this->db->where('role_id', $role_id);
        return $this->db->delete('wl_roles');
    }
    
    /**
     * Check for duplicate role name
     */
    public function check_duplicate_role_name($role_name, $exclude_id = null) {
        $this->db->from('wl_roles');
        $this->db->where('role_name', $role_name);
        
        if($exclude_id) {
            $this->db->where('role_id !=', $exclude_id);
        }
        
        $count = $this->db->count_all_results();
        
        return $count > 0;
    }
    
    /**
     * Get active roles
     */
    public function get_active_roles() {
        $this->db->select('*');
        $this->db->from('wl_roles');
        $this->db->where('status', 1);
        $this->db->order_by('role_name', 'ASC');
        $query = $this->db->get();
        
        return $query->result();
    }
}
?>