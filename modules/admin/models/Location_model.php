<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Location_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    // ==================== COUNTRY METHODS ====================
    
    public function get_countries($limit, $offset, $keyword = '', $status = '') {
        $this->db->select('*');
        $this->db->from('wl_countries');
        
        if(!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('country_name', $keyword);
            $this->db->or_like('country_iso_code_2', $keyword);
            $this->db->group_end();
        }
        
        if($status !== '' && $status !== NULL) {
            $this->db->where('status', $status);
        }
        
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        
        $result = $query->result();
        $data = array();
        foreach($result as $row) {
            $data[] = array(
                'id' => $row->id,
                'country_name' => $row->country_name,
                'country_temp_name' => $row->country_temp_name ?? '',
                'country_iso_code_2' => $row->country_iso_code_2 ?? '',
                'country_iso_code_3' => $row->country_iso_code_3 ?? '',
                'address_format_id' => $row->address_format_id ?? 0,
                'cont_currency' => $row->cont_currency ?? '',
                'TimeZone' => $row->TimeZone ?? '',
                'UTC_offset' => $row->UTC_offset ?? '',
                'is_feature' => $row->is_feature ?? 0,
                'premimum_ads_avl' => $row->premimum_ads_avl ?? 0,
                'status' => $row->status ?? 1
            );
        }
        return $data;
    }
    
    public function get_all_countries_simple() {
        $this->db->select('id, country_name, cont_currency, TimeZone, is_feature, premimum_ads_avl, status');
        $this->db->from('wl_countries');
        $this->db->order_by('country_name', 'ASC');
        $query = $this->db->get();
        
        $result = $query->result();
        $data = array();
        foreach($result as $row) {
            $data[] = array(
                'id' => $row->id,
                'country_name' => $row->country_name,
                'country_temp_name' => '',
                'country_iso_code_2' => '',
                'country_iso_code_3' => '',
                'cont_currency' => $row->cont_currency ?? '',
                'TimeZone' => $row->TimeZone ?? '',
                'is_feature' => $row->is_feature ?? 0,
                'premimum_ads_avl' => $row->premimum_ads_avl ?? 0,
                'status' => $row->status ?? 1
            );
        }
        return $data;
    }
    
    public function get_all_countries_export() {
        $this->db->select('id, country_name, country_iso_code_2, country_iso_code_3, cont_currency, TimeZone, status');
        $this->db->from('wl_countries');
        $this->db->order_by('country_name', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function count_countries($keyword = '', $status = '') {
        $this->db->from('wl_countries');
        if(!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('country_name', $keyword);
            $this->db->or_like('country_iso_code_2', $keyword);
            $this->db->group_end();
        }
        if($status !== '' && $status !== NULL) {
            $this->db->where('status', $status);
        }
        return $this->db->count_all_results();
    }
    
    public function get_country_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('wl_countries');
        $row = $query->row();
        if($row) {
            return array(
                'id' => $row->id,
                'country_name' => $row->country_name,
                'country_temp_name' => $row->country_temp_name ?? '',
                'country_iso_code_2' => $row->country_iso_code_2 ?? '',
                'country_iso_code_3' => $row->country_iso_code_3 ?? '',
                'address_format_id' => $row->address_format_id ?? 0,
                'cont_currency' => $row->cont_currency ?? '',
                'TimeZone' => $row->TimeZone ?? '',
                'UTC_offset' => $row->UTC_offset ?? '',
                'is_feature' => $row->is_feature ?? 0,
                'premimum_ads_avl' => $row->premimum_ads_avl ?? 0,
                'status' => $row->status ?? 1
            );
        }
        return null;
    }
    
    public function insert_country($data) {
        return $this->db->insert('wl_countries', $data);
    }
    
    public function update_country($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('wl_countries', $data);
    }
    
    public function delete_country($id) {
        $this->db->where('id', $id);
        return $this->db->delete('wl_countries');
    }
    
    // ==================== STATE METHODS ====================
    
    public function get_all_states() {
        $this->db->select('s.*, c.country_name');
        $this->db->from('wl_states s');
        $this->db->join('wl_countries c', 'c.id = s.country_id', 'left');
        $this->db->order_by('c.country_name', 'ASC');
        $this->db->order_by('s.title', 'ASC');
        $query = $this->db->get();
        
        $result = $query->result();
        $data = array();
        foreach($result as $row) {
            $data[] = array(
                'id' => $row->id,
                'country_id' => $row->country_id,
                'country_name' => $row->country_name ?? 'Unknown',
                'title' => $row->title,
                'temp_title' => $row->temp_title ?? '',
                'is_state_popular' => $row->is_state_popular ?? 0,
                'status' => $row->status ?? 1,
                'created_at' => $row->created_at ?? date('Y-m-d H:i:s')
            );
        }
        return $data;
    }
    
    public function get_states_by_country($country_id) {
        $this->db->select('id, title');
        $this->db->from('wl_states');
        $this->db->where('country_id', $country_id);
        $this->db->where('status', 1);
        $this->db->order_by('title', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_all_states_export() {
        $this->db->select('s.*, c.country_name');
        $this->db->from('wl_states s');
        $this->db->join('wl_countries c', 'c.id = s.country_id', 'left');
        $this->db->order_by('c.country_name', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function count_states() {
        return $this->db->count_all('wl_states');
    }
    
    public function get_state_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('wl_states');
        $row = $query->row();
        if($row) {
            return array(
                'id' => $row->id,
                'country_id' => $row->country_id,
                'title' => $row->title,
                'temp_title' => $row->temp_title ?? '',
                'is_state_popular' => $row->is_state_popular ?? 0,
                'status' => $row->status ?? 1,
                'created_at' => $row->created_at ?? date('Y-m-d H:i:s')
            );
        }
        return null;
    }
    
    public function insert_state($data) {
        return $this->db->insert('wl_states', $data);
    }
    
    public function update_state($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('wl_states', $data);
    }
    
    public function delete_state($id) {
        $this->db->where('id', $id);
        return $this->db->delete('wl_states');
    }
    
    // ==================== CITY METHODS ====================
    
    public function get_all_cities() {
        $this->db->select('ci.*, s.title as state_name, c.country_name');
        $this->db->from('wl_cities ci');
        $this->db->join('wl_states s', 's.id = ci.state_id', 'left');
        $this->db->join('wl_countries c', 'c.id = ci.country_id', 'left');
        $this->db->order_by('c.country_name', 'ASC');
        $this->db->order_by('s.title', 'ASC');
        $this->db->order_by('ci.title', 'ASC');
        $query = $this->db->get();
        
        $result = $query->result();
        $data = array();
        foreach($result as $row) {
            $data[] = array(
                'id' => $row->id,
                'country_id' => $row->country_id,
                'country_name' => $row->country_name ?? 'Unknown',
                'state_id' => $row->state_id,
                'state_name' => $row->state_name ?? 'Unknown',
                'title' => $row->title,
                'temp_title' => $row->temp_title ?? '',
                'city_group_id' => $row->city_group_id ?? '',
                'premimum_ads_avl' => $row->premimum_ads_avl ?? 0,
                'is_city_popular' => $row->is_city_popular ?? 0,
                'is_othercity_popular' => $row->is_othercity_popular ?? 0,
                'status' => $row->status ?? 1,
                'created_at' => $row->created_at ?? date('Y-m-d H:i:s')
            );
        }
        return $data;
    }
    
    public function get_all_cities_export() {
        $this->db->select('ci.*, s.title as state_name, c.country_name');
        $this->db->from('wl_cities ci');
        $this->db->join('wl_states s', 's.id = ci.state_id', 'left');
        $this->db->join('wl_countries c', 'c.id = ci.country_id', 'left');
        $this->db->order_by('c.country_name', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function count_cities() {
        return $this->db->count_all('wl_cities');
    }
    
    public function get_city_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('wl_cities');
        $row = $query->row();
        if($row) {
            return array(
                'id' => $row->id,
                'country_id' => $row->country_id,
                'state_id' => $row->state_id,
                'title' => $row->title,
                'temp_title' => $row->temp_title ?? '',
                'city_group_id' => $row->city_group_id ?? '',
                'premimum_ads_avl' => $row->premimum_ads_avl ?? 0,
                'is_city_popular' => $row->is_city_popular ?? 0,
                'is_othercity_popular' => $row->is_othercity_popular ?? 0,
                'status' => $row->status ?? 1,
                'created_at' => $row->created_at ?? date('Y-m-d H:i:s')
            );
        }
        return null;
    }
    
    public function insert_city($data) {
        return $this->db->insert('wl_cities', $data);
    }
    
    public function update_city($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('wl_cities', $data);
    }
    
    public function delete_city($id) {
        $this->db->where('id', $id);
        return $this->db->delete('wl_cities');
    }
}
?>