<?php 
$member_type = $this->session->userdata('member_type');

// Load member data safely
$mres = array();
if (isset($this->mres) && !empty($this->mres)) {
    $mres = $this->mres;
} else {
    // Fallback to session data
    $mres = array(
        'first_name' => $this->session->userdata('first_name'),
        'last_name' => $this->session->userdata('last_name'),
        'sponsor_id' => $this->session->userdata('sponsor_id'),
        'user_name' => $this->session->userdata('user_name'),
        'profile_photo' => $this->session->userdata('profile_photo'),
        'member_type' => $this->session->userdata('member_type'),
        'customers_id' => $this->session->userdata('customers_id')
    );
}



if($member_type=='3'){
    $this->load->view('members/view_left_sidebar');
} else {
    $ci =&get_instance();
    $menu_dashboard_active = !empty($ci->mem_top_menu_section) ? $ci->mem_top_menu_section : '';
    
    // Build member name
    $first_name = $mres['first_name'] ?? '';
    $last_name = $mres['last_name'] ?? '';
    $mem_name = trim($first_name . ' ' . $last_name);
    if(empty($mem_name)) {
        $mem_name = $mres['user_name'] ?? 'User';
    }
    
    $site_title_text = escape_chars($this->config->item('site_name'));
    $seg = $this->uri->segment(2);
?>
<div id="sidebar" class="float-start nav-collapse sticky-top">
    <div class="inner_logo">
        <a href="<?php echo site_url('admin');?>" title="<?php echo $site_title_text;?>">
            <img src="<?php echo theme_url();?>images/auva2.jpg" title="<?php echo $site_title_text;?>" alt="<?php echo $site_title_text;?>">
        </a>
    </div>
    
    <?php if(($mres['member_type'] ?? '') != '1'){?>
    <div class="user_top d-flex justify-content-between position-relative">
        <p class="user_pic_top text-center overflow-hidden rounded-circle">
            <span class="align-middle table-cell">
                <img src="<?php echo get_image('profiles', ($mres['profile_photo'] ?? ''), 40, 40, 'AR');?>" alt="<?php echo $mem_name;?>" class="mw-100 mh-100">
            </span>
        </p> 
        <div class="user_info">
            <p class="user_name text-black fw-semibold overflow-hidden"><?php echo $mem_name;?> </p>
            <p class="user_email text-black fw-semibold overflow-hidden">[ <?php echo $mres['sponsor_id'] ?? 'N/A';?> ]</p>
            <p class="user_email text-black overflow-hidden"><?php echo $mres['user_name'] ?? 'N/A';?></p>
        </div>
    </div>
    <?php } ?>
    
    <div class="acc_box position-relative mh-100 h-100 mt-3">
        <ul class="acc_links m-0 p-0 text-white">
            
            <!-- Dashboard -->
            <li <?php echo ($menu_dashboard_active == 'dashboard')? 'class="acc_act"' : '';?>>
                <a href="<?php echo site_url('admin');?>" title="Dashboard">
                    <img src="<?php echo theme_url();?>images/lft-ico1.svg" alt="" class="acc_ico"> 
                    <span class="trans_eff">Dashboard</span>
                </a>
            </li>
            
            <?php if($member_type != '3'){ ?>
            <li>
                <a href="javascript:void(0)" title="User Manage">
                    <img src="<?php echo theme_url();?>images/lft-ico2.svg" alt="" class="acc_ico"> 
                    <span>
                        User Manage 
                        <img src="<?php echo theme_url();?>images/arr-down.svg" class="float-end mt-2" alt="">
                    </span>
                </a>

                <div class="dashboard_sub_list dash_sub_list <?php echo ($menu_dashboard_active == 'sub_admins') ? '' : 'dn';?>">
                    <a href="<?php echo site_url('admin/members');?>"
                       <?php echo ($seg == 'members') ? 'class="acc_act"' : '';?>>
                       Manage Users
                    </a>

                    <?php if($member_type != '2'){ ?>
                    <a href="<?php echo site_url('admin/sub_admins');?>"
                       <?php echo ($seg == 'sub_admins') ? 'class="acc_act"' : '';?>>
                       Manage Sub Users
                    </a>

                    <a href="<?php echo site_url('admin/staff_user');?>"
                       <?php echo ($seg == 'staff_user') ? 'class="acc_act"' : '';?>>
                       Manage Staff Users
                    </a>
                    <?php } ?>
                </div>
            </li>
            <?php } ?>
            
            <!-- Artists -->
            <li <?php echo ($seg == 'artists')? 'class="acc_act"' : '';?>>
                <a href="<?php echo site_url('admin/artists/add');?>" title="Artists Manage">
                    <img src="<?php echo theme_url();?>images/upload_icon.svg" alt="" class="acc_ico" style="width: 26px;"> 
                    <span>ADM Artists</span>
                </a>
            </li>
            
            <!-- Labels -->
            <li <?php echo ($menu_dashboard_active == 'labels')? 'class="acc_act"' : '';?>>
                <a href="<?php echo site_url('admin/labels');?>" title="Label Manage">
                    <img src="<?php echo theme_url();?>images/lft-ico6.svg" alt="" class="acc_ico"> 
                    <span>Labels</span>
                </a>
            </li>  
            
            <?php if($member_type == '1'){?>
            <!-- Wallet -->
            <li <?php echo ($menu_dashboard_active == 'wallet')? 'class="acc_act"' : '';?>>
                <a href="<?php echo site_url('admin/wallet');?>" title="Financial Report">
                    <img src="<?php echo theme_url();?>images/lft-ico6.svg" alt="" class="acc_ico"> 
                    <span>Financial Report</span>
                </a>
            </li>
            <?php } ?>
            
            <!-- Meta Data -->
            <li <?php echo ($menu_dashboard_active == 'meta_data')? 'class="acc_act"' : '';?>>
                <a href="<?php echo site_url('admin/meta_data');?>" title="Meta Data">
                    <img src="<?php echo theme_url();?>images/lft-ico7.svg" alt="" class="acc_ico"> 
                    <span>Meta Data</span>
                </a>
            </li>
            
            <!-- Youtube Request -->
            <li <?php echo ($menu_dashboard_active == 'channel_add_request')? 'class="acc_act"' : '';?>>
                <a href="<?php echo site_url('admin/channel_add_request');?>" title="Youtube Request">
                    <img src="<?php echo theme_url();?>images/lft-ico10.svg" alt="" class="acc_ico"> 
                    <span>Youtube Request</span>
                </a>
            </li>
            
            <?php if($member_type == '1'){?>
            <!-- Notifications -->
            <li <?php echo ($menu_dashboard_active == 'notifications')? 'class="acc_act"' : '';?>>
                <a href="<?php echo site_url('admin/notifications');?>" title="Manage Notifications">
                    <img src="<?php echo theme_url();?>images/bell.svg" alt="" class="acc_ico"> 
                    <span>Manage Notifications</span>
                </a>
            </li>
            <?php } ?>
            
            <?php if($member_type != '3'){ ?>
            <!-- App Settings -->
            <li>
                <a href="javascript:void(0)" title="App Settings">
                    <img src="<?php echo theme_url();?>images/lft-ico2.svg" alt="" class="acc_ico"> 
                    <span>
                        App Setting 
                        <img src="<?php echo theme_url();?>images/arr-down.svg" class="float-end mt-2" alt="">
                    </span>
                </a>

                <div class="dashboard_sub_list dash_sub_list <?php echo ($menu_dashboard_active == 'app_setting') ? '' : 'dn';?>">
                    <a href="<?php echo site_url('admin/settingcontroller/list_role');?>"
                       <?php echo ($seg == 'list_role') ? 'class="acc_act"' : '';?>>
                       Manage Role
                    </a>

                    <a href="<?php echo site_url('admin/settingcontroller/list_department');?>"
                       <?php echo ($seg == 'list_department') ? 'class="acc_act"' : '';?>>
                       Manage Department
                    </a>

                    <?php if($member_type != '2'){ ?>
                    <a href="<?php echo site_url('admin/settingcontroller/list_designation');?>"
                       <?php echo ($seg == 'list_designation') ? 'class="acc_act"' : '';?>>
                       Manage Designation
                    </a>
                    <?php } ?>

                    <a href="<?php echo site_url('admin/settingcontroller/location_manage');?>"
                       <?php echo ($seg == 'location_manage') ? 'class="acc_act"' : '';?>>
                       Manage Location
                    </a>
                </div>
            </li>
            <?php } ?>
            <?php if($member_type == '1'){?>
            <?php $trash_count = get_trash_count(); ?>

<li <?php echo ($menu_dashboard_active == 'trash')? 'class="acc_act"' : '';?>>
    <a href="<?php echo site_url('admin/album/trash');?>" title="Trash">

     <img src="<?php echo theme_url();?>images/trash.svg" class="acc_ico">

        <span>Trash</span>

        <?php if($trash_count > 0){ ?>
            <span class="badge bg-danger" style="margin-left:8px;">
                <?= $trash_count; ?>
            </span>
        <?php } ?>

    </a>
</li>
<style>
    .badge {
    padding: 5px 10px;
    font-size: 14px;
    border-radius: 10px;
    background: red;
    color: #fff;
}
</style>

<?php  } ?>

            <!-- Change Password -->
            <li <?php echo ($menu_dashboard_active == 'change_password')? 'class="acc_act"' : '';?>>
                <a href="<?php echo site_url('admin/change_password');?>" title="Change Password">
                    <img src="<?php echo theme_url();?>images/setting.svg" alt="" class="acc_ico"> 
                    <span>Change Password</span>
                </a>
            </li>


          
        </ul>
    </div>
    
    <p class="acc_copyright text-center mt-4">
        Copyright &copy; <?php echo date('Y');?> <br>
        <span class="fs-6"><?php echo $site_title_text;?></span><br>
        All rights reserved.
    </p>
</div>
<?php }?>