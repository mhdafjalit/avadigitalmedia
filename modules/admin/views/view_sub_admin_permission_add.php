<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
    array('heading'=>'Sub User Manage','url'=>'admin/sub_admins'),
    array('heading'=>'Dashboard','url'=>'admin')
);
$uerId = $this->uri->segment(3);

// Get member type from session
$member_type = $this->session->userdata('member_type');

// Get member data for display
$mres = array();
if (isset($this->mres) && !empty($this->mres)) {
    $mres = $this->mres;
} else {
    $mres = array(
        'first_name' => $this->session->userdata('first_name'),
        'last_name' => $this->session->userdata('last_name'),
        'user_name' => $this->session->userdata('user_name'),
        'member_type' => $this->session->userdata('member_type')
    );
}

$admin_name = trim(($mres['first_name'] ?? '') . ' ' . ($mres['last_name'] ?? ''));
if(empty($admin_name)) {
    $admin_name = $mres['user_name'] ?? 'Administrator';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permission Settings | Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --primary-light: #eef2ff;
            --secondary: #06d6a0;
            --secondary-dark: #05c091;
            --secondary-light: #e0faf2;
            --danger: #ef476f;
            --danger-dark: #d63e5e;
            --danger-light: #feeff3;
            --warning: #ffd166;
            --warning-dark: #f7b731;
            --warning-light: #fff9e6;
            --info: #4ecdc4;
            --info-dark: #3aa9a1;
            --info-light: #e3f6f4;
            --dark: #2b2d42;
            --dark-light: #4a4e69;
            --gray: #6c757d;
            --gray-light: #e9ecef;
            --gray-lighter: #f8f9fa;
            --white: #ffffff;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.02);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.05);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.05);
            --shadow-xl: 0 20px 25px rgba(0,0,0,0.05);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fb;
            color: var(--dark);
            line-height: 1.5;
        }

        /* Layout */
        .dash_outer {
            min-height: 100vh;
        }

        .dash_container {
            display: flex;
            min-height: 100vh;
        }

        #main-content {
            flex: 1;
            
            transition: var(--transition);
        }

        /* Header Section */
        .page-header {
            background: var(--white);
            padding: 20px 30px;
            border-bottom: 1px solid var(--gray-light);
            box-shadow: var(--shadow-sm);
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray);
            font-size: 14px;
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb a:hover {
            color: var(--primary-dark);
        }

        .breadcrumb i {
            font-size: 12px;
            color: var(--gray);
        }

        /* Content Area */
        .content-area {
            padding: 30px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            border: 1px solid var(--gray-light);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-light);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-icon.primary {
            background: var(--primary-light);
            color: var(--primary);
        }

        .stat-icon.secondary {
            background: var(--secondary-light);
            color: var(--secondary);
        }

        .stat-icon.warning {
            background: var(--warning-light);
            color: var(--warning-dark);
        }

        .stat-icon.danger {
            background: var(--danger-light);
            color: var(--danger);
        }

        .stat-info h3 {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .stat-info p {
            color: var(--gray);
            font-size: 14px;
            font-weight: 500;
        }

        /* Admin Profile Card */
        .admin-profile-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: var(--radius-lg);
            padding: 24px;
            color: var(--white);
            margin-bottom: 30px;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .profile-avatar {
            width: 64px;
            height: 64px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 600;
            border: 3px solid rgba(255,255,255,0.3);
        }

        .profile-text h4 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .profile-text p {
            opacity: 0.9;
            font-size: 14px;
        }

        .profile-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 20px;
            border-radius: 100px;
            font-weight: 500;
            font-size: 14px;
            border: 1px solid rgba(255,255,255,0.3);
            backdrop-filter: blur(10px);
        }

        /* Permission Card */
        .permission-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .permission-header {
            padding: 24px;
            border-bottom: 1px solid var(--gray-light);
            background: linear-gradient(to right, var(--white), var(--gray-lighter));
        }

        .permission-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .permission-header h2 i {
            color: var(--primary);
        }

        .permission-header p {
            color: var(--gray);
            font-size: 14px;
        }

        .permission-body {
            padding: 24px;
        }

        /* Module Grid */
        .module-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .module-card {
            background: var(--white);
            border: 1px solid var(--gray-light);
            border-radius: var(--radius-md);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .module-card:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-md);
        }

        .module-card.selected {
            border: 2px solid var(--primary);
            box-shadow: 0 0 0 4px var(--primary-light);
        }

        .module-header {
            padding: 16px;
            background: linear-gradient(to right, var(--gray-lighter), var(--white));
            border-bottom: 1px solid var(--gray-light);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .module-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-sm);
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .module-title {
            flex: 1;
        }

        .module-title h3 {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .module-title span {
            font-size: 12px;
            color: var(--gray);
        }

        .module-badge {
            padding: 4px 8px;
            background: var(--gray-lighter);
            border-radius: 100px;
            font-size: 11px;
            font-weight: 500;
            color: var(--gray);
        }

        .module-body {
            padding: 16px;
        }

        /* Permission Buttons */
        .permission-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .perm-btn {
            position: relative;
        }

        .perm-btn input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .perm-btn label {
            display: inline-block;
            padding: 8px 16px;
            background: var(--gray-lighter);
            border: 1px solid var(--gray-light);
            border-radius: 100px;
            font-size: 13px;
            font-weight: 500;
            color: var(--gray);
            cursor: pointer;
            transition: var(--transition);
            user-select: none;
        }

        .perm-btn input[type="checkbox"]:checked + label {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--white);
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
        }

        .perm-btn label i {
            margin-right: 6px;
            font-size: 12px;
        }

        .perm-btn.view input:checked + label {
            background: var(--info);
            border-color: var(--info);
        }

        .perm-btn.add input:checked + label {
            background: var(--secondary);
            border-color: var(--secondary);
        }

        .perm-btn.edit input:checked + label {
            background: var(--warning);
            border-color: var(--warning);
            color: var(--dark);
        }

        .perm-btn.delete input:checked + label {
            background: var(--danger);
            border-color: var(--danger);
        }

        .perm-btn.all input:checked + label {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
        }

        /* Action Bar */
        .action-bar {
            background: var(--white);
            border-top: 1px solid var(--gray-light);
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 100px;
            font-weight: 500;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--gray);
            border: 1px solid var(--gray-light);
        }

        .btn-secondary:hover {
            background: var(--gray-lighter);
            border-color: var(--gray);
        }

        .btn-danger {
            background: var(--white);
            color: var(--danger);
            border: 1px solid var(--danger-light);
        }

        .btn-danger:hover {
            background: var(--danger);
            color: var(--white);
            border-color: var(--danger);
        }

        .btn-success {
            background: var(--white);
            color: var(--secondary);
            border: 1px solid var(--secondary-light);
        }

        .btn-success:hover {
            background: var(--secondary);
            color: var(--white);
            border-color: var(--secondary);
        }

        /* Alerts */
        .alert {
            padding: 16px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 4px solid transparent;
        }

        .alert-info {
            background: var(--info-light);
            border-left-color: var(--info);
            color: var(--info-dark);
        }

        .alert-warning {
            background: var(--warning-light);
            border-left-color: var(--warning);
            color: var(--warning-dark);
        }

        .alert i {
            font-size: 20px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--white);
            border-radius: var(--radius-lg);
        }

        .empty-state i {
            font-size: 64px;
            color: var(--gray-light);
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--gray);
            max-width: 400px;
            margin: 0 auto;
        }

        /* Loading Spinner */
        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--gray-light);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            #main-content {
                margin-left: 0;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .module-grid {
                grid-template-columns: 1fr;
            }
            
            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .action-buttons {
                justify-content: center;
            }
            
            .profile-info {
                flex-direction: column;
                text-align: center;
            }
        }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .module-card {
            animation: slideIn 0.3s ease forwards;
        }

        .module-card:nth-child(n) {
            animation-delay: calc(0.05s * var(--i, 1));
        }

        /* Tooltip */
        [data-tooltip] {
            position: relative;
            cursor: help;
        }

        [data-tooltip]:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 6px 12px;
            background: var(--dark);
            color: var(--white);
            font-size: 12px;
            border-radius: var(--radius-sm);
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 10;
        }

        [data-tooltip]:hover:before {
            opacity: 1;
            visibility: visible;
            bottom: 120%;
        }
    </style>
</head>
<body>
<div class="dash_outer">
    <div class="dash_container">
        <?php $this->load->view('view_left_sidebar'); ?>
        
        <div id="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1><?php echo $heading_title ?? 'Permission Settings'; ?></h1>
                <div class="breadcrumb">
                    <a href="<?php echo site_url('admin'); ?>">Dashboard</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="<?php echo site_url('admin/sub_admins'); ?>">Sub User Manage</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Permissions</span>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $total_modules ?? count($section_res ?? []); ?></h3>
                            <p>Total Modules</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon secondary">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $selected_modules ?? 0; ?></h3>
                            <p>Selected Permissions</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $pending_modules ?? 0; ?></h3>
                            <p>Pending Setup</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon danger">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $member_type == 1 ? 'Full' : 'Limited'; ?></h3>
                            <p>Access Level</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Profile Card -->
                <div class="admin-profile-card">
                    <div class="profile-info">
                        <div class="profile-avatar">
                            <?php echo strtoupper(substr($admin_name, 0, 1)); ?>
                        </div>
                        <div class="profile-text">
                            <h4><?php echo $admin_name; ?></h4>
                            <p><i class="fas fa-envelope me-2"></i><?php echo $mres['user_name'] ?? 'admin@example.com'; ?></p>
                        </div>
                    </div>
                    <div class="profile-badge">
                        <i class="fas fa-crown me-2"></i>
                        <?php 
                        $member_type_labels = [
                            1 => 'Administrator',
                            2 => 'Sub-Administrator',
                            3 => 'User',
                            4 => 'Staff'
                        ];
                        echo $member_type_labels[$member_type] ?? 'User';
                        ?>
                    </div>
                </div>

                <!-- Messages -->
                <?php if(error_message() != ''): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div><?php echo error_message(); ?></div>
                </div>
                <?php endif; ?>

                <?php if(validation_message() != ''): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <div><?php echo validation_message(); ?></div>
                </div>
                <?php endif; ?>

                <!-- Permission Card -->
                <div class="permission-card">
                    <div class="permission-header">
                        <h2>
                            <i class="fas fa-lock"></i>
                            Module Permissions
                        </h2>
                        <p>Select the modules and actions this user can access. Hover over permissions for more details.</p>
                    </div>

                    <?php echo form_open(current_url_query_string(), 'name="permission_frm" autocomplete="off" class="permission-form"'); ?>
                    
                    <div class="permission-body">
                        <?php
                        echo form_error('user_permission');
                        $values_posted_back = $this->input->post() ? TRUE : FALSE;
                        $posted_arr = $this->input->post('sec_id') ?: [];
                        
                        // Load module access configuration
                        $this->config->load('module_access', TRUE);
                        $module_access = $this->config->item('module_access');
                        $module_details = $this->config->item('module_details');
                        
                        // Fix: Check if section_res is set and is an array
                        $section_res = isset($section_res) ? $section_res : array();
                        
                        if (!empty($section_res) && is_array($section_res)) {
                            $config_prvg_arr = $this->config->item('subadmin_privileges');
                            $module_count = 0;
                            
                            echo '<div class="module-grid">';
                            
                            foreach ($section_res as $value) {
                                if (!isset($value['section_controller']) || !isset($value['actual_privilege'])) {
                                    continue;
                                }
                                
                                $module_count++;
                                $controller_identifier = $value['section_controller'];
                                $actual_privileges = explode(",", $value['actual_privilege']);
                                
                                // Check if this module is accessible based on member type
                                $module_key = str_replace(['_controller', 'Controller'], '', $controller_identifier);
                                $has_access = true;
                                
                                // For non-admin users, check module access
                                if ($member_type != 1) {
                                    $found = false;
                                    if (isset($module_access[$member_type]) && is_array($module_access[$member_type])) {
                                        foreach ($module_access[$member_type] as $key => $access) {
                                            if (strpos($controller_identifier, $key) !== false || strpos($key, $module_key) !== false) {
                                                $has_access = $access;
                                                $found = true;
                                                break;
                                            }
                                        }
                                    }
                                    
                                    if (!$found) {
                                        $admin_only_modules = ['wallet', 'notifications', 'sub_admins', 'list_designation'];
                                        $has_access = !in_array($module_key, $admin_only_modules);
                                    }
                                }
                                
                                // Only show modules that are accessible
                                if ($has_access && !empty($actual_privileges)) { 
                                    // Get module display name
                                    $module_display_name = $value['section_title'] ?? ucwords(str_replace('_', ' ', $controller_identifier));
                                    if (!empty($module_details) && is_array($module_details)) {
                                        foreach ($module_details as $key => $detail) {
                                            if (strpos($controller_identifier, $key) !== false) {
                                                $module_display_name = $detail['name'] ?? $module_display_name;
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // Check if module has any selected permissions
                                    $has_selected = false;
                                    foreach ($actual_privileges as $val) {
                                        $ckbox_val = "{$value['id']}_0_{$val}";
                                        $ckbox_name = $controller_identifier;
                                        $posted_vals = $values_posted_back 
                                            ? (array)$this->input->post($ckbox_name) 
                                            : (array)(isset($db_saved_data[$ckbox_name]) ? $db_saved_data[$ckbox_name] : []);
                                        if (in_array($ckbox_val, $posted_vals)) {
                                            $has_selected = true;
                                            break;
                                        }
                                    }
                                    ?>
                                    <div class="module-card <?php echo $has_selected ? 'selected' : ''; ?>" style="--i: <?php echo $module_count; ?>">
                                        <div class="module-header">
                                            <div class="module-icon">
                                                <i class="fas fa-<?php 
                                                    echo match(true) {
                                                        strpos($controller_identifier, 'user') !== false => 'users',
                                                        strpos($controller_identifier, 'payment') !== false => 'credit-card',
                                                        strpos($controller_identifier, 'report') !== false => 'chart-bar',
                                                        strpos($controller_identifier, 'setting') !== false => 'cog',
                                                        default => 'cube'
                                                    };
                                                ?>"></i>
                                            </div>
                                            <div class="module-title">
                                                <h3><?php echo htmlspecialchars($module_display_name); ?></h3>
                                                <span><?php echo count($actual_privileges); ?> permissions</span>
                                            </div>
                                            <div class="module-badge">
                                                <?php echo strtoupper(substr($controller_identifier, 0, 3)); ?>
                                            </div>
                                        </div>
                                        <div class="module-body">
                                            <div class="permission-group">
                                                <?php 
                                                foreach ($actual_privileges as $val) {
                                                    if (empty($val)) continue;
                                                    
                                                    $ckbox_val = "{$value['id']}_0_{$val}";
                                                    $ckbox_name = $controller_identifier;
                                                    $posted_vals = $values_posted_back 
                                                        ? (array)$this->input->post($ckbox_name) 
                                                        : (array)(isset($db_saved_data[$ckbox_name]) ? $db_saved_data[$ckbox_name] : []);
                                                    $checked = in_array($ckbox_val, $posted_vals) ? 'checked="checked"' : '';
                                                    
                                                    // Get permission label and class
                                                    $perm_label = $config_prvg_arr[$val] ?? 'Permission';
                                                    $perm_class = '';
                                                    $perm_icon = 'fa-check';
                                                    
                                                    switch ($val) {
                                                        case '1':
                                                            $perm_class = 'view';
                                                            $perm_icon = 'fa-eye';
                                                            $perm_label = 'View';
                                                            break;
                                                        case '2':
                                                            $perm_class = 'add';
                                                            $perm_icon = 'fa-plus-circle';
                                                            $perm_label = 'Add';
                                                            break;
                                                        case '3':
                                                            $perm_class = 'edit';
                                                            $perm_icon = 'fa-edit';
                                                            $perm_label = 'Edit';
                                                            break;
                                                        case '4':
                                                            $perm_class = 'delete';
                                                            $perm_icon = 'fa-trash';
                                                            $perm_label = 'Delete';
                                                            break;
                                                        case '12':
                                                            $perm_class = 'all';
                                                            $perm_icon = 'fa-star';
                                                            $perm_label = 'All';
                                                            break;
                                                    }
                                                    ?>
                                                    <div class="perm-btn <?php echo $perm_class; ?>" data-tooltip="<?php echo $perm_label; ?> permission">
                                                        <input type="checkbox" 
                                                               name="<?php echo htmlspecialchars($ckbox_name); ?>[]" 
                                                               value="<?php echo htmlspecialchars($ckbox_val); ?>" 
                                                               <?php echo $checked; ?> 
                                                               class="xprv <?php echo $perm_class == 'all' ? 'x_all' : ''; ?> <?php echo $perm_class == 'view' ? 'x_view' : ''; ?> x_sibling" 
                                                               id="<?php echo htmlspecialchars($ckbox_val); ?>">
                                                        <label for="<?php echo htmlspecialchars($ckbox_val); ?>">
                                                            <i class="fas <?php echo $perm_icon; ?>"></i>
                                                            <?php echo $perm_label; ?>
                                                        </label>
                                                    </div>
                                                    <?php 
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                                }
                            }
                            
                            echo '</div>';
                        } else { ?>
                            <div class="empty-state">
                                <i class="fas fa-cubes"></i>
                                <h3>No Permission Modules Found</h3>
                                <p>There are no permission sections configured in the system. Please contact your system administrator to set up permission modules.</p>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Action Bar -->
                    <div class="action-bar">
                        <input type="hidden" name="user_permission" id="permission" value="<?php echo (isset($db_saved_data) && !empty($db_saved_data)) ? '1' : ''; ?>">
                        
                        <div class="action-buttons">
                            <button type="submit" name="action" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Save Permissions
                            </button>
                            
                            <?php if($member_type == 1): ?>
                            <button type="button" class="btn btn-success" onclick="setAllPermissions('grant')">
                                <i class="fas fa-check-circle"></i>
                                Grant All
                            </button>
                            <button type="button" class="btn btn-danger" onclick="setAllPermissions('revoke')">
                                <i class="fas fa-times-circle"></i>
                                Revoke All
                            </button>
                            <?php endif; ?>
                        </div>
                        
                        <a href="<?php echo site_url('admin/sub_admins'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Back to Sub Admins
                        </a>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permission Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Update module card selection state
    function updateModuleSelection() {
        $('.module-card').each(function() {
            var hasChecked = $(this).find('.xprv:checked').length > 0;
            if(hasChecked) {
                $(this).addClass('selected');
            } else {
                $(this).removeClass('selected');
            }
        });
        
        // Update hidden permission field
        var anyChecked = $('.xprv:checked').length > 0;
        $('#permission').val(anyChecked ? '1' : '');
    }
    
    // Handle permission clicks
    $('.xprv').on('change', function(e) {
        var cobj = $(this);
        var parent_card = cobj.closest('.module-card');
        
        if(cobj.prop('checked')) {
            // Handle "All" permission
            if(cobj.hasClass('x_all')) {
                parent_card.find('.xprv').not(cobj).prop('checked', true);
            } 
            // Handle view permission dependency
            else if(!cobj.hasClass('x_view')) {
                parent_card.find('.x_view').prop('checked', true);
            }
            
            // Check if all siblings are checked
            var uncheckedSibling = parent_card.find('.x_sibling:not(:checked)').not('.x_all');
            if(!uncheckedSibling.length) {
                parent_card.find('.x_all').prop('checked', true);
            }
        } else {
            if(cobj.hasClass('x_all')) {
                parent_card.find('.xprv').not(cobj).prop('checked', false);
            } else {
                var checkedSibling = parent_card.find('.x_sibling:checked').not('.x_all');
                if(!checkedSibling.length) {
                    parent_card.find('.x_all').prop('checked', false);
                } else {
                    if(cobj.hasClass('x_view')) {
                        cobj.prop('checked', true);
                    } else {
                        parent_card.find('.x_all').prop('checked', false);
                    }
                }
            }
        }
        
        updateModuleSelection();
    });
    
    // Initialize module selection on page load
    updateModuleSelection();
    
    // Form submit confirmation
    $('.permission-form').on('submit', function(e) {
        var checkedCount = $('.xprv:checked').length;
        if(checkedCount === 0) {
            return confirm('No permissions selected. Are you sure you want to continue?');
        }
        return true;
    });
});

// Function to grant/revoke all permissions
function setAllPermissions(action) {
    if(action === 'grant') {
        if(confirm('Are you sure you want to grant ALL permissions? This will give full access to all modules.')) {
            $('.xprv').prop('checked', true);
            
            // Handle dependencies
            $('.module-card').each(function() {
                var hasView = $(this).find('.x_view').length > 0;
                var hasAll = $(this).find('.x_all').length > 0;
                
                if(hasView && hasAll) {
                    $(this).find('.x_all').prop('checked', true);
                }
            });
            
            $('#permission').val('1');
            $('.module-card').addClass('selected');
            
            // Show success message
            showNotification('All permissions granted successfully!', 'success');
        }
    } else if(action === 'revoke') {
        if(confirm('Are you sure you want to revoke ALL permissions?')) {
            $('.xprv').prop('checked', false);
            $('#permission').val('');
            $('.module-card').removeClass('selected');
            
            // Show success message
            showNotification('All permissions revoked successfully!', 'info');
        }
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = $('<div class="alert alert-' + type + '" style="position: fixed; top: 20px; right: 20px; z-index: 9999; animation: slideIn 0.3s;">' +
        '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'info-circle') + '"></i>' +
        '<div>' + message + '</div>' +
        '</div>');
    
    $('body').append(notification);
    
    setTimeout(function() {
        notification.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

// Keyboard shortcuts
$(document).keydown(function(e) {
    // Ctrl+S to save
    if(e.ctrlKey && e.keyCode === 83) {
        e.preventDefault();
        $('button[type="submit"]').click();
    }
});
</script>

<?php $this->load->view('bottom_application'); ?>
</body>
</html>