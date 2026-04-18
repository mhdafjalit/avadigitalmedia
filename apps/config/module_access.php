<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Module access control based on member type
$config['module_access'] = array(
    // Admin (Type 1) - Full access to everything
    1 => array(
        'dashboard' => true,
        'user_manage' => true,
        'members' => true,
        'sub_admins' => true,
        'staff_user' => true,
        'artists' => true,
        'labels' => true,
        'wallet' => true,
        'meta_data' => true,
        'channel_add_request' => true,
        'notifications' => true,
        'app_settings' => true,
        'list_role' => true,
        'list_department' => true,
        'list_designation' => true,
        'location_manage' => true,
        'change_password' => true,
    ),
    
    // Subadmin (Type 2) - Limited access
    2 => array(
        'dashboard' => true,
        'user_manage' => true,
        'members' => true,
        'sub_admins' => false,  // Cannot manage sub admins
        'staff_user' => true,
        'artists' => true,
        'labels' => true,
        'wallet' => false,       // No financial reports
        'meta_data' => true,
        'channel_add_request' => true,
        'notifications' => false, // Cannot manage notifications
        'app_settings' => true,
        'list_role' => true,
        'list_department' => true,
        'list_designation' => false, // Cannot manage designations
        'location_manage' => true,
        'change_password' => true,
    ),
    
    // User (Type 3) - Very limited access (frontend user)
    3 => array(
        'dashboard' => true,
        'user_manage' => false,
        'members' => false,
        'sub_admins' => false,
        'staff_user' => false,
        'artists' => true,
        'labels' => true,
        'wallet' => false,
        'meta_data' => false,
        'channel_add_request' => false,
        'notifications' => false,
        'app_settings' => false,
        'list_role' => false,
        'list_department' => false,
        'list_designation' => false,
        'location_manage' => false,
        'change_password' => true,
    ),
    
    // Staff (Type 4) - Staff specific access
    4 => array(
        'dashboard' => true,
        'user_manage' => false,
        'members' => false,
        'sub_admins' => false,
        'staff_user' => false,    // Cannot manage other staff
        'artists' => true,
        'labels' => true,
        'wallet' => false,
        'meta_data' => true,
        'channel_add_request' => true,
        'notifications' => false,
        'app_settings' => false,
        'list_role' => false,
        'list_department' => false,
        'list_designation' => false,
        'location_manage' => false,
        'change_password' => true,
    ),
);

// Module display names and icons
$config['module_details'] = array(
    'dashboard' => array(
        'name' => 'Dashboard',
        'icon' => 'lft-ico1.svg',
        'url' => 'admin'
    ),
    'user_manage' => array(
        'name' => 'User Manage',
        'icon' => 'lft-ico2.svg',
        'is_parent' => true,
        'children' => array('members', 'sub_admins', 'staff_user')
    ),
    'members' => array(
        'name' => 'Manage Users',
        'icon' => 'lft-ico2.svg',
        'url' => 'admin/members',
        'parent' => 'user_manage'
    ),
    'sub_admins' => array(
        'name' => 'Manage Sub Users',
        'icon' => 'lft-ico2.svg',
        'url' => 'admin/sub_admins',
        'parent' => 'user_manage'
    ),
    'staff_user' => array(
        'name' => 'Manage Staff Users',
        'icon' => 'lft-ico2.svg',
        'url' => 'admin/staff_user',
        'parent' => 'user_manage'
    ),
    'artists' => array(
        'name' => 'ADM Artists',
        'icon' => 'upload_icon.svg',
        'url' => 'admin/artists/add'
    ),
    'labels' => array(
        'name' => 'Labels',
        'icon' => 'lft-ico6.svg',
        'url' => 'admin/labels'
    ),
    'wallet' => array(
        'name' => 'Financial Report',
        'icon' => 'lft-ico6.svg',
        'url' => 'admin/wallet'
    ),
    'meta_data' => array(
        'name' => 'Meta Data',
        'icon' => 'lft-ico7.svg',
        'url' => 'admin/meta_data'
    ),
    'channel_add_request' => array(
        'name' => 'Youtube Request',
        'icon' => 'lft-ico10.svg',
        'url' => 'admin/channel_add_request'
    ),
    'notifications' => array(
        'name' => 'Manage Notifications',
        'icon' => 'bell.svg',
        'url' => 'admin/notifications'
    ),
    'app_settings' => array(
        'name' => 'App Setting',
        'icon' => 'lft-ico2.svg',
        'is_parent' => true,
        'children' => array('list_role', 'list_department', 'list_designation', 'location_manage')
    ),
    'list_role' => array(
        'name' => 'Manage Role',
        'icon' => 'lft-ico2.svg',
        'url' => 'admin/list_role',
        'parent' => 'app_settings'
    ),
    'list_department' => array(
        'name' => 'Manage Department',
        'icon' => 'lft-ico2.svg',
        'url' => 'admin/list_department',
        'parent' => 'app_settings'
    ),
    'list_designation' => array(
        'name' => 'Manage Designation',
        'icon' => 'lft-ico2.svg',
        'url' => 'admin/list_designation',
        'parent' => 'app_settings'
    ),
    'location_manage' => array(
        'name' => 'Manage Location',
        'icon' => 'lft-ico2.svg',
        'url' => 'admin/location_manage',
        'parent' => 'app_settings'
    ),
    'change_password' => array(
        'name' => 'Change Password',
        'icon' => 'setting.svg',
        'url' => 'admin/change_password'
    ),
);