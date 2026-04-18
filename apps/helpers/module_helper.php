<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('has_module_access')) {
    function has_module_access($module_key) {
        $CI =& get_instance();
        
        // Load module access config if not already loaded
        $CI->config->load('module_access', TRUE);
        $module_access = $CI->config->item('module_access');
        $module_details = $CI->config->item('module_details');
        
        // Get current member type from session
        $member_type = $CI->session->userdata('member_type');
        
        // Admin (type 1) always has access
        if ($member_type == 1) {
            return true;
        }
        
        // Check if module exists and has access
        if (isset($module_access[$member_type][$module_key])) {
            return $module_access[$member_type][$module_key];
        }
        
        return false;
    }
}

if (!function_exists('get_module_url')) {
    function get_module_url($module_key) {
        $CI =& get_instance();
        $CI->config->load('module_access', TRUE);
        $module_details = $CI->config->item('module_details');
        
        return isset($module_details[$module_key]['url']) ? site_url($module_details[$module_key]['url']) : '#';
    }
}

if (!function_exists('get_module_icon')) {
    function get_module_icon($module_key) {
        $CI =& get_instance();
        $CI->config->load('module_access', TRUE);
        $module_details = $CI->config->item('module_details');
        
        return isset($module_details[$module_key]['icon']) ? theme_url() . 'images/' . $module_details[$module_key]['icon'] : '';
    }
}

if (!function_exists('get_module_name')) {
    function get_module_name($module_key) {
        $CI =& get_instance();
        $CI->config->load('module_access', TRUE);
        $module_details = $CI->config->item('module_details');
        
        return isset($module_details[$module_key]['name']) ? $module_details[$module_key]['name'] : '';
    }
}

if (!function_exists('is_parent_module')) {
    function is_parent_module($module_key) {
        $CI =& get_instance();
        $CI->config->load('module_access', TRUE);
        $module_details = $CI->config->item('module_details');
        
        return isset($module_details[$module_key]['is_parent']) && $module_details[$module_key]['is_parent'] === true;
    }
}

if (!function_exists('get_child_modules')) {
    function get_child_modules($parent_key) {
        $CI =& get_instance();
        $CI->config->load('module_access', TRUE);
        $module_details = $CI->config->item('module_details');
        $module_access = $CI->config->item('module_access');
        
        $member_type = $CI->session->userdata('member_type');
        $children = array();
        
        if (isset($module_details[$parent_key]['children'])) {
            foreach ($module_details[$parent_key]['children'] as $child) {
                // Check if user has access to this child module
                if (isset($module_access[$member_type][$child]) && $module_access[$member_type][$child]) {
                    $children[] = $child;
                }
            }
        }
        
        return $children;
    }
}