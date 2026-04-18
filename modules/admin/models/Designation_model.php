<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Designation_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Get all designations with department info (grouped by department)
     */
    public function get_designations_grouped($keyword = '', $status = '') {
        $this->db->select('
            d.designation_id,
            d.department_id,
            d.designation_name,
            d.designation_description,
            d.status,
            d.created_date,
            dep.department_name,
            dep.department_description
        ');
        $this->db->from('wl_designation d');
        $this->db->join('wl_departments dep', 'dep.department_id = d.department_id', 'left');
        
        if(!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('d.designation_name', $keyword);
            $this->db->or_like('d.designation_description', $keyword);
            $this->db->or_like('dep.department_name', $keyword);
            $this->db->group_end();
        }
        
        if($status !== '' && $status !== NULL) {
            $this->db->where('d.status', $status);
        }
        
        $this->db->order_by('dep.department_name', 'ASC');
        $this->db->order_by('d.designation_name', 'ASC');
        
        $query = $this->db->get();
        $result = $query->result();
        $data = array();
        
        foreach($result as $row) {
            $data[] = array(
                'designation_id' => $row->designation_id,
                'department_id' => $row->department_id,
                'department_name' => $row->department_name,
                'department_description' => $row->department_description,
                'designation_name' => $row->designation_name,
                'designation_description' => $row->designation_description,
                'status' => $row->status,
                'created_date' => $row->created_date
            );
        }
        
        return $data;
    }
    
    /**
     * Get all designations simple
     */
    public function get_all_designations_simple() {
        $this->db->select('designation_id, designation_name');
        $this->db->from('wl_designation');
        $this->db->order_by('designation_name', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get single designation by ID
     */
    public function get_designation_by_id($designation_id) {
        $this->db->select('*');
        $this->db->from('wl_designation');
        $this->db->where('designation_id', $designation_id);
        $query = $this->db->get();
        
        if($query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }
    
    /**
     * Insert new designation
     */
    public function insert_designation($data) {
        if($this->db->insert('wl_designation', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }
    
    /**
     * Update designation
     */
    public function update_designation($designation_id, $data) {
        $this->db->where('designation_id', $designation_id);
        return $this->db->update('wl_designation', $data);
    }
    
    /**
     * Update designation status
     */
    public function update_designation_status($designation_id, $status) {
        $data = array('status' => $status);
        $this->db->where('designation_id', $designation_id);
        return $this->db->update('wl_designation', $data);
    }
    
    /**
     * Delete designation
     */
    public function delete_designation($designation_id) {
        $this->db->where('designation_id', $designation_id);
        return $this->db->delete('wl_designation');
    }
    
    /**
     * Check for duplicate designation name
     */
    public function check_duplicate_designation_name($designation_name, $department_id, $exclude_id = null) {
        $this->db->from('wl_designation');
        $this->db->where('designation_name', $designation_name);
        $this->db->where('department_id', $department_id);
        
        if($exclude_id) {
            $this->db->where('designation_id !=', $exclude_id);
        }
        
        $count = $this->db->count_all_results();
        return $count > 0;
    }
}
?>