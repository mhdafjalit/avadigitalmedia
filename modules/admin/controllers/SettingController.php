<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SettingController extends Private_Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load all required models
        $this->load->model('admin/Role_model', 'role_model');
        $this->load->model('admin/Department_model', 'department_model');
        $this->load->model('admin/Designation_model', 'designation_model');
        $this->load->model('admin/Location_model', 'location_model');
        
        $this->load->library('pagination');
    }

    // ==================== ROLES MANAGEMENT ====================
    
    /**
     * List all roles
     * URL: admin/settingcontroller/list_role
     */
    public function list_role() {
        // Get filter parameters
        $keyword = $this->input->get('keyword', TRUE);
        $status = $this->input->get('status', TRUE);
        $this->mem_top_menu_section = 'app_setting';
        // Pagination configuration
        $config = array();
        $config["base_url"] = site_url('admin/settingcontroller/list_role');
        $config["total_rows"] = $this->role_model->count_roles($keyword, $status);
        $config["per_page"] = 10;
        $config["uri_segment"] = 4;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = FALSE;
        
        // Custom pagination styling
        $config['full_tag_open'] = '<div class="pagination-container"><ul class="pagination">';
        $config['full_tag_close'] = '</ul></div>';
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['next_link'] = '&gt;';
        $config['prev_link'] = '&lt;';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        
        $this->pagination->initialize($config);
        
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 1;
        $offset = ($page - 1) * $config["per_page"];
        
        // Get roles with pagination
        $data["res"] = $this->role_model->get_roles($config["per_page"], $offset, $keyword, $status);
        $data["page_links"] = $this->pagination->create_links();
        
        // Set page data
        $data['heading_title'] = 'Role Management';
        $data['status_options'] = array('' => 'All', '1' => 'Active', '0' => 'Inactive');
        
        // Load views
        $this->load->view('top_application', $data);
        $this->load->view('settings/list_role', $data);
        $this->load->view('bottom_application');
    }
    
    /**
     * Create new role
     * URL: admin/settingcontroller/create_role
     */
    public function create_role() {
        // Check if form submitted
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            
            // Set validation rules
            $this->form_validation->set_rules('role_name', 'Role Name', 'required|trim');
            $this->form_validation->set_rules('role_description', 'Role Description', 'trim');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('admin/settingcontroller/list_role');
            }
            
            // Check for duplicate role name
            $role_name = $this->input->post('role_name', TRUE);
            if($this->role_model->check_duplicate_role_name($role_name)) {
                $this->session->set_flashdata('error', 'Role name already exists.');
                redirect('admin/settingcontroller/list_role');
            }
            
            // Get status (checkbox returns 'on' or null)
            $status_value = $this->input->post('status');
            $status = ($status_value == '1' || $status_value == 'on') ? 1 : 0;
            
            // Prepare data for insertion
            $role_data = array(
                'role_name' => $role_name,
                'role_description' => $this->input->post('role_description', TRUE),
                'status' => $status,
                'created_by' => $this->session->userdata('admin_id'),
                'created_date' => date('Y-m-d H:i:s')
            );
            
            // Insert role
            $role_id = $this->role_model->insert_role($role_data);
            
            if ($role_id) {
                $this->session->set_flashdata('success', 'Role created successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to create role. Please try again.');
            }
        } else {
            $this->session->set_flashdata('error', 'Invalid request method.');
        }
        
        redirect('admin/settingcontroller/list_role');
    }
    
    /**
     * Update role
     * URL: admin/settingcontroller/update_role
     */
    public function update_role() {
        // Check if form submitted
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            
            $role_id = $this->input->post('role_id', TRUE);
            
            // Check if role exists
            $existing_role = $this->role_model->get_role_by_id($role_id);
            if(!$existing_role) {
                $this->session->set_flashdata('error', 'Role not found.');
                redirect('admin/settingcontroller/list_role');
            }
            
            // Set validation rules
            $this->form_validation->set_rules('role_name', 'Role Name', 'required|trim');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('admin/settingcontroller/list_role');
            }
            
            // Check for duplicate role name (excluding current role)
            $role_name = $this->input->post('role_name', TRUE);
            if($this->role_model->check_duplicate_role_name($role_name, $role_id)) {
                $this->session->set_flashdata('error', 'Role name already exists.');
                redirect('admin/settingcontroller/list_role');
            }
            
            // Get status (checkbox returns 'on' or null)
            $status_value = $this->input->post('status');
            $status = ($status_value == '1' || $status_value == 'on') ? 1 : 0;
            
            // Prepare data for update
            $role_data = array(
                'role_name' => $role_name,
                'role_description' => $this->input->post('role_description', TRUE),
                'status' => $status,
                'updated_date' => date('Y-m-d H:i:s')
            );
            
            // Update role
            $updated = $this->role_model->update_role($role_id, $role_data);
            
            if ($updated) {
                $this->session->set_flashdata('success', 'Role updated successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to update role.');
            }
        } else {
            $this->session->set_flashdata('error', 'Invalid request method.');
        }
        
        redirect('admin/settingcontroller/list_role');
    }
    
    /**
     * Edit role - Get role data for AJAX
     * URL: admin/settingcontroller/edit_role/{encrypted_id}
     */
    public function edit_role($encrypted_id) {
        // Decrypt the role ID
        $role_id = $this->decrypt_role_id($encrypted_id);
        
        if(!$role_id) {
            echo json_encode(array('error' => 'Role not found'));
            return;
        }
        
        // Get role data
        $role = $this->role_model->get_role_by_id($role_id);
        
        if($role) {
            $response = array(
                'role_id' => $role->role_id,
                'role_name' => $role->role_name,
                'role_description' => $role->role_description,
                'status' => $role->status
            );
            
            echo json_encode($response);
        } else {
            echo json_encode(array('error' => 'Role not found'));
        }
    }
    
    /**
     * Delete role
     * URL: admin/settingcontroller/delete_role/{encrypted_id}
     */
    public function delete_role($encrypted_id) {
        // Decrypt the role ID
        $role_id = $this->decrypt_role_id($encrypted_id);
        
        if(!$role_id) {
            $this->session->set_flashdata('error', 'Invalid role ID.');
            redirect('admin/settingcontroller/list_role');
        }
        
        // Check if role exists
        $role = $this->role_model->get_role_by_id($role_id);
        if(!$role) {
            $this->session->set_flashdata('error', 'Role not found.');
            redirect('admin/settingcontroller/list_role');
        }
        
        // Prevent deletion of system roles by ID
        $system_role_ids = array(12, 13); // Super Admin, Admin role IDs from your table
        
        if (in_array($role->role_id, $system_role_ids)) {
            $this->session->set_flashdata('error', 'System roles cannot be deleted.');
            redirect('admin/settingcontroller/list_role');
        }
        
        // Delete role
        $deleted = $this->role_model->delete_role($role_id);
        
        if ($deleted) {
            $this->session->set_flashdata('success', 'Role deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete role.');
        }
        
        redirect('admin/settingcontroller/list_role');
    }
    
    /**
     * Change role status (Active/Inactive)
     * URL: admin/settingcontroller/status_role/{encrypted_id}?u_status=active/deactive
     */
    public function status_role($encrypted_id) {
        // Decrypt the role ID
        $role_id = $this->decrypt_role_id($encrypted_id);
        
        if(!$role_id) {
            $this->session->set_flashdata('error', 'Invalid role ID.');
            redirect('admin/settingcontroller/list_role');
        }
        
        $status_param = $this->input->get('u_status', TRUE);
        $new_status = ($status_param == 'active') ? 1 : 0;
        
        // Update status
        $updated = $this->role_model->update_role_status($role_id, $new_status);
        
        if ($updated) {
            $this->session->set_flashdata('success', 'Role status updated successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to update role status.');
        }
        
        redirect('admin/settingcontroller/list_role');
    }
    
    /**
     * Helper function to decrypt MD5 encrypted role ID
     */
    private function decrypt_role_id($encrypted_id) {
        // Get all roles and find matching MD5
        $roles = $this->role_model->get_all_roles_simple();
        
        foreach($roles as $role) {
            if(md5($role->role_id) == $encrypted_id) {
                return $role->role_id;
            }
        }
        
        return false;
    }

    // ==================== DEPARTMENTS MANAGEMENT ====================
    
    /**
     * List all departments
     * URL: admin/settingcontroller/list_department
     */
    public function list_department() {
        // Get filter parameters
        $keyword = $this->input->get('keyword', TRUE);
        $status = $this->input->get('status', TRUE);
         $this->mem_top_menu_section = 'app_setting';
        
        // Pagination configuration
        $config = array();
        $config["base_url"] = site_url('admin/settingcontroller/list_department');
        $config["total_rows"] = $this->department_model->count_departments($keyword, $status);
        $config["per_page"] = 10;
        $config["uri_segment"] = 4;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = FALSE;
        
        // Custom pagination styling
        $config['full_tag_open'] = '<div class="pagination-container"><ul class="pagination">';
        $config['full_tag_close'] = '</ul></div>';
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['next_link'] = '&gt;';
        $config['prev_link'] = '&lt;';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        
        $this->pagination->initialize($config);
        
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 1;
        $offset = ($page - 1) * $config["per_page"];
        
        // Get departments with pagination
        $data["res"] = $this->department_model->get_departments($config["per_page"], $offset, $keyword, $status);
        $data["page_links"] = $this->pagination->create_links();
        
        // Set page data
        $data['heading_title'] = 'Department Management';
        
        // Load views
        $this->load->view('top_application', $data);
        $this->load->view('settings/list_department', $data);
        $this->load->view('bottom_application');
    }
    
    /**
     * Create new department
     * URL: admin/settingcontroller/create_department (POST)
     */
    public function create_department() {
        // Check if form submitted
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            
            // Set validation rules
            $this->form_validation->set_rules('department_name', 'Department Name', 'required|trim');
            $this->form_validation->set_rules('department_description', 'Description', 'trim');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('admin/settingcontroller/list_department');
            }
            
            // Check for duplicate department name
            $department_name = $this->input->post('department_name', TRUE);
            if($this->department_model->check_duplicate_department_name($department_name)) {
                $this->session->set_flashdata('error', 'Department name already exists.');
                redirect('admin/settingcontroller/list_department');
            }
            
            // Get status (checkbox returns 'on' or null)
            $status_value = $this->input->post('status');
            $status = ($status_value == '1' || $status_value == 'on') ? 1 : 0;
            
            // Prepare data for insertion
            $dept_data = array(
                'department_name' => $department_name,
                'department_description' => $this->input->post('department_description', TRUE),
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s')
            );
            
            // Insert department
            $dept_id = $this->department_model->insert_department($dept_data);
            
            if ($dept_id) {
                $this->session->set_flashdata('success', 'Department created successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to create department. Please try again.');
            }
        } else {
            $this->session->set_flashdata('error', 'Invalid request method.');
        }
        
        redirect('admin/settingcontroller/list_department');
    }
    
    /**
     * Update department
     * URL: admin/settingcontroller/update_department (POST)
     */
    public function update_department() {
        // Check if form submitted
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            
            $department_id = $this->input->post('department_id', TRUE);
            
            // Check if department exists
            $existing_dept = $this->department_model->get_department_by_id($department_id);
            if(!$existing_dept) {
                $this->session->set_flashdata('error', 'Department not found.');
                redirect('admin/settingcontroller/list_department');
            }
            
            // Set validation rules
            $this->form_validation->set_rules('department_name', 'Department Name', 'required|trim');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('admin/settingcontroller/list_department');
            }
            
            // Check for duplicate department name (excluding current)
            $department_name = $this->input->post('department_name', TRUE);
            if($this->department_model->check_duplicate_department_name($department_name, $department_id)) {
                $this->session->set_flashdata('error', 'Department name already exists.');
                redirect('admin/settingcontroller/list_department');
            }
            
            // Get status (checkbox returns 'on' or null)
            $status_value = $this->input->post('status');
            $status = ($status_value == '1' || $status_value == 'on') ? 1 : 0;
            
            // Prepare data for update
            $dept_data = array(
                'department_name' => $department_name,
                'department_description' => $this->input->post('department_description', TRUE),
                'status' => $status
            );
            
            // Update department
            $updated = $this->department_model->update_department($department_id, $dept_data);
            
            if ($updated) {
                $this->session->set_flashdata('success', 'Department updated successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to update department.');
            }
        } else {
            $this->session->set_flashdata('error', 'Invalid request method.');
        }
        
        redirect('admin/settingcontroller/list_department');
    }
    
    /**
     * Edit department - Get department data for AJAX
     * URL: admin/settingcontroller/edit_department/{encrypted_id}
     */
    public function edit_department($encrypted_id) {
        // Decrypt the department ID
        $department_id = $this->decrypt_department_id($encrypted_id);
        
        if(!$department_id) {
            echo json_encode(array('error' => 'Department not found'));
            return;
        }
        
        // Get department data
        $department = $this->department_model->get_department_by_id($department_id);
        
        if($department) {
            $response = array(
                'department_id' => $department->department_id,
                'department_name' => $department->department_name,
                'department_description' => $department->department_description,
                'status' => $department->status
            );
            
            echo json_encode($response);
        } else {
            echo json_encode(array('error' => 'Department not found'));
        }
    }
    
    /**
     * Delete department
     * URL: admin/settingcontroller/delete_department/{encrypted_id}
     */
    public function delete_department($encrypted_id) {
        // Decrypt the department ID
        $department_id = $this->decrypt_department_id($encrypted_id);
        
        if(!$department_id) {
            $this->session->set_flashdata('error', 'Invalid department ID.');
            redirect('admin/settingcontroller/list_department');
        }
        
        // Check if department exists
        $department = $this->department_model->get_department_by_id($department_id);
        if(!$department) {
            $this->session->set_flashdata('error', 'Department not found.');
            redirect('admin/settingcontroller/list_department');
        }
        
        // Delete department
        $deleted = $this->department_model->delete_department($department_id);
        
        if ($deleted) {
            $this->session->set_flashdata('success', 'Department deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete department.');
        }
        
        redirect('admin/settingcontroller/list_department');
    }
    
    /**
     * Change department status (Active/Inactive)
     * URL: admin/settingcontroller/status_department/{encrypted_id}?u_status=active/deactive
     */
    public function status_department($encrypted_id) {
        // Decrypt the department ID
        $department_id = $this->decrypt_department_id($encrypted_id);
        
        if(!$department_id) {
            $this->session->set_flashdata('error', 'Invalid department ID.');
            redirect('admin/settingcontroller/list_department');
        }
        
        $status_param = $this->input->get('u_status', TRUE);
        $new_status = ($status_param == 'active') ? 1 : 0;
        
        // Update status
        $updated = $this->department_model->update_department_status($department_id, $new_status);
        
        if ($updated) {
            $this->session->set_flashdata('success', 'Department status updated successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to update department status.');
        }
        
        redirect('admin/settingcontroller/list_department');
    }
    
    /**
     * Helper function to decrypt MD5 encrypted department ID
     */
    private function decrypt_department_id($encrypted_id) {
        // Get all departments and find matching MD5
        $departments = $this->department_model->get_all_departments_simple();
        
        foreach($departments as $dept) {
            if(md5($dept->department_id) == $encrypted_id) {
                return $dept->department_id;
            }
        }
        
        return false;
    }

    // ==================== DESIGNATIONS MANAGEMENT ====================
    
    /**
     * List all designations
     * URL: admin/settingcontroller/list_designation
     */
    public function list_designation() {
       $this->mem_top_menu_section = 'app_setting';
        try {
            // Get filter parameters
            $keyword = $this->input->get('keyword', TRUE);
            $status = $this->input->get('status', TRUE);
            
            // Get all departments for dropdown
            $data['department'] = $this->department_model->get_active_departments();
            
            // Get designations with grouping
            $data["res"] = $this->designation_model->get_designations_grouped($keyword, $status);
            
            // Set page data
            $data['heading_title'] = 'Designation Management';
            
            // Load views
            $this->load->view('top_application', $data);
            $this->load->view('settings/list_designation', $data);
            $this->load->view('bottom_application');
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            log_message('error', $e->getMessage());
        }
    }
    
    /**
     * Create new designation
     * URL: admin/settingcontroller/create_designation (POST)
     */
    public function create_designation() {
        // Check if form submitted
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            
            // Set validation rules
            $this->form_validation->set_rules('department_id', 'Department', 'required|trim');
            $this->form_validation->set_rules('designation_name', 'Designation Name', 'required|trim');
            $this->form_validation->set_rules('designation_description', 'Description', 'trim');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('admin/settingcontroller/list_designation');
            }
            
            $department_id = $this->input->post('department_id', TRUE);
            $designation_name = $this->input->post('designation_name', TRUE);
            
            // Check for duplicate designation name in same department
            if($this->designation_model->check_duplicate_designation_name($designation_name, $department_id)) {
                $this->session->set_flashdata('error', 'Designation name already exists in this department.');
                redirect('admin/settingcontroller/list_designation');
            }
            
            // Get status (checkbox returns 'on' or null)
            $status_value = $this->input->post('status');
            $status = ($status_value == '1' || $status_value == 'on') ? 1 : 0;
            
            // Prepare data for insertion
            $desig_data = array(
                'department_id' => $department_id,
                'designation_name' => $designation_name,
                'designation_description' => $this->input->post('designation_description', TRUE),
                'status' => $status,
                'created_date' => date('Y-m-d H:i:s')
            );
            
            // Insert designation
            $desig_id = $this->designation_model->insert_designation($desig_data);
            
            if ($desig_id) {
                $this->session->set_flashdata('success', 'Designation created successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to create designation. Please try again.');
            }
        } else {
            $this->session->set_flashdata('error', 'Invalid request method.');
        }
        
        redirect('admin/settingcontroller/list_designation');
    }
    
    /**
     * Update designation
     * URL: admin/settingcontroller/update_designation (POST)
     */
    public function update_designation() {
        // Check if form submitted
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            
            $designation_id = $this->input->post('designation_id', TRUE);
            
            // Check if designation exists
            $existing_desig = $this->designation_model->get_designation_by_id($designation_id);
            if(!$existing_desig) {
                $this->session->set_flashdata('error', 'Designation not found.');
                redirect('admin/settingcontroller/list_designation');
            }
            
            // Set validation rules
            $this->form_validation->set_rules('department_id', 'Department', 'required|trim');
            $this->form_validation->set_rules('designation_name', 'Designation Name', 'required|trim');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('admin/settingcontroller/list_designation');
            }
            
            $department_id = $this->input->post('department_id', TRUE);
            $designation_name = $this->input->post('designation_name', TRUE);
            
            // Check for duplicate designation name (excluding current)
            if($this->designation_model->check_duplicate_designation_name($designation_name, $department_id, $designation_id)) {
                $this->session->set_flashdata('error', 'Designation name already exists in this department.');
                redirect('admin/settingcontroller/list_designation');
            }
            
            // Get status (checkbox returns 'on' or null)
            $status_value = $this->input->post('status');
            $status = ($status_value == '1' || $status_value == 'on') ? 1 : 0;
            
            // Prepare data for update
            $desig_data = array(
                'department_id' => $department_id,
                'designation_name' => $designation_name,
                'designation_description' => $this->input->post('designation_description', TRUE),
                'status' => $status
            );
            
            // Update designation
            $updated = $this->designation_model->update_designation($designation_id, $desig_data);
            
            if ($updated) {
                $this->session->set_flashdata('success', 'Designation updated successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to update designation.');
            }
        } else {
            $this->session->set_flashdata('error', 'Invalid request method.');
        }
        
        redirect('admin/settingcontroller/list_designation');
    }
    
    /**
     * Edit designation - Get designation data for AJAX
     * URL: admin/settingcontroller/edit_designation/{encrypted_id}
     */
    public function edit_designation($encrypted_id) {
        // Decrypt the designation ID
        $designation_id = $this->decrypt_designation_id($encrypted_id);
        
        if(!$designation_id) {
            echo json_encode(array('error' => 'Designation not found'));
            return;
        }
        
        // Get designation data
        $designation = $this->designation_model->get_designation_by_id($designation_id);
        
        if($designation) {
            $response = array(
                'designation_id' => $designation->designation_id,
                'department_id' => $designation->department_id,
                'designation_name' => $designation->designation_name,
                'designation_description' => $designation->designation_description,
                'status' => $designation->status
            );
            
            echo json_encode($response);
        } else {
            echo json_encode(array('error' => 'Designation not found'));
        }
    }
    
    /**
     * Delete designation
     * URL: admin/settingcontroller/delete_designation/{encrypted_id}
     */
    public function delete_designation($encrypted_id) {
        // Decrypt the designation ID
        $designation_id = $this->decrypt_designation_id($encrypted_id);
        
        if(!$designation_id) {
            $this->session->set_flashdata('error', 'Invalid designation ID.');
            redirect('admin/settingcontroller/list_designation');
        }
        
        // Check if designation exists
        $designation = $this->designation_model->get_designation_by_id($designation_id);
        if(!$designation) {
            $this->session->set_flashdata('error', 'Designation not found.');
            redirect('admin/settingcontroller/list_designation');
        }
        
        // Delete designation
        $deleted = $this->designation_model->delete_designation($designation_id);
        
        if ($deleted) {
            $this->session->set_flashdata('success', 'Designation deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete designation.');
        }
        
        redirect('admin/settingcontroller/list_designation');
    }
    
    /**
     * Change designation status (Active/Inactive)
     * URL: admin/settingcontroller/status_designation/{encrypted_id}?u_status=active/deactive
     */
    public function status_designation($encrypted_id) {
        // Decrypt the designation ID
        $designation_id = $this->decrypt_designation_id($encrypted_id);
        
        if(!$designation_id) {
            $this->session->set_flashdata('error', 'Invalid designation ID.');
            redirect('admin/settingcontroller/list_designation');
        }
        
        $status_param = $this->input->get('u_status', TRUE);
        $new_status = ($status_param == 'active') ? 1 : 0;
        
        // Update status
        $updated = $this->designation_model->update_designation_status($designation_id, $new_status);
        
        if ($updated) {
            $this->session->set_flashdata('success', 'Designation status updated successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to update designation status.');
        }
        
        redirect('admin/settingcontroller/list_designation');
    }
    
    /**
     * Helper function to decrypt MD5 encrypted designation ID
     */
    private function decrypt_designation_id($encrypted_id) {
        // Get all designations and find matching MD5
        $designations = $this->designation_model->get_all_designations_simple();
        
        foreach($designations as $desig) {
            if(md5($desig->designation_id) == $encrypted_id) {
                return $desig->designation_id;
            }
        }
        
        return false;
    }

    // ==================== CSRF TOKEN ====================
    
    /**
     * Get fresh CSRF token for AJAX
     * URL: admin/settingcontroller/get_csrf_token
     */
    public function get_csrf_token() {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'csrf_token_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            )));
    }

    // ==================== LOCATION MANAGEMENT ====================
    
    /**
     * Location Management - Countries, States, Cities
     * URL: admin/settingcontroller/location_manage
     */
    public function location_manage() {
       $this->mem_top_menu_section = 'app_setting';
        // Pagination settings
        $per_page = 24;
        $segment = 4;
        $page = $this->uri->segment($segment) ? $this->uri->segment($segment) : 1;
        $offset = ($page - 1) * $per_page;
        
        // Get filter parameters
        $keyword = $this->input->get('keyword', TRUE);
        $status = $this->input->get('status', TRUE);
        
        // Get countries with pagination
        $data['countries'] = $this->location_model->get_countries($per_page, $offset, $keyword, $status);
        $data['countriesData'] = $this->location_model->get_all_countries_simple();
        $data['total_countries'] = $this->location_model->count_countries($keyword, $status);
        
        // Country pagination
        $this->load->library('pagination');
        $config['base_url'] = site_url('admin/settingcontroller/location_manage');
        $config['total_rows'] = $data['total_countries'];
        $config['per_page'] = $per_page;
        $config['uri_segment'] = $segment;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;
        $config['full_tag_open'] = '<nav><ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['next_link'] = '&gt;';
        $config['prev_link'] = '&lt;';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item"><a class="page-link" href="#">';
        $config['num_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        
        $this->pagination->initialize($config);
        $data['country_pagination'] = $this->pagination->create_links();
        
        // Get states (without pagination for grouped view)
        $data['states'] = $this->location_model->get_all_states();
        $data['total_states'] = $this->location_model->count_states();
        
        // Get cities (without pagination for grouped view)
        $data['cities'] = $this->location_model->get_all_cities();
        $data['total_cities'] = $this->location_model->count_cities();
        
        $data['heading_title'] = 'Location Management';
        
        $this->load->view('top_application', $data);
        $this->load->view('settings/location_manage', $data);
        $this->load->view('bottom_application');
    }

    // ==================== COUNTRY METHODS ====================

    public function create_country() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->form_validation->set_rules('country_name', 'Country Name', 'required|trim');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('admin/settingcontroller/location_manage');
            }
            
            $data = array(
                'country_name' => $this->input->post('country_name', TRUE),
                'country_temp_name' => $this->input->post('country_temp_name', TRUE),
                'country_iso_code_2' => $this->input->post('country_iso_code_2', TRUE),
                'country_iso_code_3' => $this->input->post('country_iso_code_3', TRUE),
                'address_format_id' => $this->input->post('address_format_id', TRUE),
                'cont_currency' => $this->input->post('cont_currency', TRUE),
                'TimeZone' => $this->input->post('TimeZone', TRUE),
                'UTC_offset' => $this->input->post('UTC_offset', TRUE),
                'is_feature' => $this->input->post('is_feature') ? 1 : 0,
                'premimum_ads_avl' => $this->input->post('premimum_ads_avl') ? 1 : 0,
                'status' => $this->input->post('status') ? 1 : 0
            );
            
            if ($this->location_model->insert_country($data)) {
                $this->session->set_flashdata('success', 'Country added successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to add country.');
            }
        }
        redirect('admin/settingcontroller/location_manage');
    }

    public function update_country() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $id = $this->input->post('id', TRUE);
            
            $data = array(
                'country_name' => $this->input->post('country_name', TRUE),
                'country_temp_name' => $this->input->post('country_temp_name', TRUE),
                'country_iso_code_2' => $this->input->post('country_iso_code_2', TRUE),
                'country_iso_code_3' => $this->input->post('country_iso_code_3', TRUE),
                'address_format_id' => $this->input->post('address_format_id', TRUE),
                'cont_currency' => $this->input->post('cont_currency', TRUE),
                'TimeZone' => $this->input->post('TimeZone', TRUE),
                'UTC_offset' => $this->input->post('UTC_offset', TRUE),
                'is_feature' => $this->input->post('is_feature') ? 1 : 0,
                'premimum_ads_avl' => $this->input->post('premimum_ads_avl') ? 1 : 0,
                'status' => $this->input->post('status') ? 1 : 0
            );
            
            if ($this->location_model->update_country($id, $data)) {
                $this->session->set_flashdata('success', 'Country updated successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to update country.');
            }
        }
        redirect('admin/settingcontroller/location_manage');
    }

    public function edit_country($id) {
        $country = $this->location_model->get_country_by_id($id);
        echo json_encode($country);
    }

    public function delete_country($id) {
        if ($this->location_model->delete_country($id)) {
            $this->session->set_flashdata('success', 'Country deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete country.');
        }
        redirect('admin/settingcontroller/location_manage');
    }

    public function toggle_featured_country($id) {
        $country = $this->location_model->get_country_by_id($id);
        $new_value = ($country['is_feature'] == 1) ? 0 : 1;
        $this->location_model->update_country($id, array('is_feature' => $new_value));
        redirect('admin/settingcontroller/location_manage');
    }

    // ==================== STATE METHODS ====================

    public function create_state() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->form_validation->set_rules('title', 'State Name', 'required|trim');
            $this->form_validation->set_rules('country_id', 'Country', 'required|trim');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('admin/settingcontroller/location_manage');
            }
            
            $data = array(
                'country_id' => $this->input->post('country_id', TRUE),
                'title' => $this->input->post('title', TRUE),
                'temp_title' => $this->input->post('temp_title', TRUE),
                'is_state_popular' => $this->input->post('is_state_popular') ? 1 : 0,
                'status' => $this->input->post('status') ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s')
            );
            
            if ($this->location_model->insert_state($data)) {
                $this->session->set_flashdata('success', 'State added successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to add state.');
            }
        }
        redirect('admin/settingcontroller/location_manage');
    }

    public function update_state() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $id = $this->input->post('id', TRUE);
            
            $data = array(
                'country_id' => $this->input->post('country_id', TRUE),
                'title' => $this->input->post('title', TRUE),
                'temp_title' => $this->input->post('temp_title', TRUE),
                'is_state_popular' => $this->input->post('is_state_popular') ? 1 : 0,
                'status' => $this->input->post('status') ? 1 : 0
            );
            
            if ($this->location_model->update_state($id, $data)) {
                $this->session->set_flashdata('success', 'State updated successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to update state.');
            }
        }
        redirect('admin/settingcontroller/location_manage');
    }

    public function edit_state($id) {
        $state = $this->location_model->get_state_by_id($id);
        echo json_encode($state);
    }

    public function delete_state($id) {
        if ($this->location_model->delete_state($id)) {
            $this->session->set_flashdata('success', 'State deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete state.');
        }
        redirect('admin/settingcontroller/location_manage');
    }

    public function toggle_popular_state($id) {
        $state = $this->location_model->get_state_by_id($id);
        $new_value = ($state['is_state_popular'] == 1) ? 0 : 1;
        $this->location_model->update_state($id, array('is_state_popular' => $new_value));
        redirect('admin/settingcontroller/location_manage');
    }

    public function get_states_by_country($country_id) {
        $states = $this->location_model->get_states_by_country($country_id);
        echo json_encode($states);
    }

    // ==================== CITY METHODS ====================

    public function create_city() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->form_validation->set_rules('title', 'City Name', 'required|trim');
            $this->form_validation->set_rules('country_id', 'Country', 'required|trim');
            $this->form_validation->set_rules('state_id', 'State', 'required|trim');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('admin/settingcontroller/location_manage');
            }
            
            $data = array(
                'country_id' => $this->input->post('country_id', TRUE),
                'state_id' => $this->input->post('state_id', TRUE),
                'title' => $this->input->post('title', TRUE),
                'temp_title' => $this->input->post('temp_title', TRUE),
                'city_group_id' => $this->input->post('city_group_id', TRUE),
                'premimum_ads_avl' => $this->input->post('premimum_ads_avl') ? 1 : 0,
                'is_city_popular' => $this->input->post('is_city_popular') ? 1 : 0,
                'is_othercity_popular' => $this->input->post('is_othercity_popular') ? 1 : 0,
                'status' => $this->input->post('status') ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s')
            );
            
            if ($this->location_model->insert_city($data)) {
                $this->session->set_flashdata('success', 'City added successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to add city.');
            }
        }
        redirect('admin/settingcontroller/location_manage');
    }

    public function update_city() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $id = $this->input->post('id', TRUE);
            
            $data = array(
                'country_id' => $this->input->post('country_id', TRUE),
                'state_id' => $this->input->post('state_id', TRUE),
                'title' => $this->input->post('title', TRUE),
                'temp_title' => $this->input->post('temp_title', TRUE),
                'city_group_id' => $this->input->post('city_group_id', TRUE),
                'premimum_ads_avl' => $this->input->post('premimum_ads_avl') ? 1 : 0,
                'is_city_popular' => $this->input->post('is_city_popular') ? 1 : 0,
                'is_othercity_popular' => $this->input->post('is_othercity_popular') ? 1 : 0,
                'status' => $this->input->post('status') ? 1 : 0
            );
            
            if ($this->location_model->update_city($id, $data)) {
                $this->session->set_flashdata('success', 'City updated successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to update city.');
            }
        }
        redirect('admin/settingcontroller/location_manage');
    }

    public function edit_city($id) {
        $city = $this->location_model->get_city_by_id($id);
        echo json_encode($city);
    }

    public function delete_city($id) {
        if ($this->location_model->delete_city($id)) {
            $this->session->set_flashdata('success', 'City deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete city.');
        }
        redirect('admin/settingcontroller/location_manage');
    }

    public function toggle_premium_city($id) {
        $city = $this->location_model->get_city_by_id($id);
        $new_value = ($city['premimum_ads_avl'] == 1) ? 0 : 1;
        $this->location_model->update_city($id, array('premimum_ads_avl' => $new_value));
        redirect('admin/settingcontroller/location_manage');
    }

    public function toggle_popular_city($id) {
        $city = $this->location_model->get_city_by_id($id);
        $new_value = ($city['is_city_popular'] == 1) ? 0 : 1;
        $this->location_model->update_city($id, array('is_city_popular' => $new_value));
        redirect('admin/settingcontroller/location_manage');
    }

    // ==================== STATUS TOGGLE ====================

    public function status_location($type, $id) {
        $status_param = $this->input->get('u_status', TRUE);
        $new_status = ($status_param == 'active') ? 1 : 0;
        
        switch($type) {
            case 'country':
                $this->location_model->update_country($id, array('status' => $new_status));
                break;
            case 'state':
                $this->location_model->update_state($id, array('status' => $new_status));
                break;
            case 'city':
                $this->location_model->update_city($id, array('status' => $new_status));
                break;
        }
        
        $this->session->set_flashdata('success', ucfirst($type) . ' status updated successfully!');
        redirect('admin/settingcontroller/location_manage');
    }

    // ==================== EXPORT METHODS ====================

    public function export_countries() {
        $countries = $this->location_model->get_all_countries_export();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=countries_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'Country Name', 'ISO Code 2', 'ISO Code 3', 'Currency', 'TimeZone', 'Status'));
        
        foreach($countries as $country) {
            fputcsv($output, array(
                $country->id,
                $country->country_name,
                $country->country_iso_code_2,
                $country->country_iso_code_3,
                $country->cont_currency,
                $country->TimeZone,
                $country->status == 1 ? 'Active' : 'Inactive'
            ));
        }
        
        fclose($output);
        exit();
    }

    public function export_states() {
        $states = $this->location_model->get_all_states_export();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=states_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'State Name', 'Country', 'Popular', 'Status'));
        
        foreach($states as $state) {
            fputcsv($output, array(
                $state->id,
                $state->title,
                $state->country_name,
                $state->is_state_popular == 1 ? 'Yes' : 'No',
                $state->status == 1 ? 'Active' : 'Inactive'
            ));
        }
        
        fclose($output);
        exit();
    }

    public function export_cities() {
        $cities = $this->location_model->get_all_cities_export();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=cities_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'City Name', 'State', 'Country', 'Premium Ads', 'Popular', 'Status'));
        
        foreach($cities as $city) {
            fputcsv($output, array(
                $city->id,
                $city->title,
                $city->state_name,
                $city->country_name,
                $city->premimum_ads_avl == 1 ? 'Yes' : 'No',
                $city->is_city_popular == 1 ? 'Yes' : 'No',
                $city->status == 1 ? 'Active' : 'Inactive'
            ));
        }
        
        fclose($output);
        exit();
    }
}