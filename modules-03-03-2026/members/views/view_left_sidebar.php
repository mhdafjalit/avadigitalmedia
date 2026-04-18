<?php // $member_type = $this->session->userdata('member_type'); trace($member_type);
$ci =&get_instance();
$menu_dashboard_active = !empty($ci->mem_top_menu_section) ? $ci->mem_top_menu_section : '';
$mem_name = trim($this->mres['first_name'].' '.$this->mres['last_name']);
$site_title_text = escape_chars($this->config->item('site_name'));
$seg = $this->uri->segment(2);?>
<div id="sidebar" class="float-start nav-collapse sticky-top">
	<div class="inner_logo">
		<a href="<?php echo site_url('admin');?>" title="<?php echo $site_title_text;?>">
			<img src="<?php echo theme_url();?>images/auva2.jpg" title="<?php echo $site_title_text;?>" alt="<?php echo $site_title_text;?>">
		</a>
	</div>
	<?php
	if($this->mres['member_type']!='1'){?>
	<div class="user_top d-flex justify-content-between position-relative">
		<p class="user_pic_top text-center overflow-hidden rounded-circle">
			<span class="align-middle table-cell">
				<img src="<?php echo get_image('profiles',$this->mres['profile_photo'],40,40,'AR');?>" alt="<?php echo $mem_name;?>" class="mw-100 mh-100">
			</span>
		</p> 
		<div class="user_info">
			<p class="user_name text-black fw-semibold overflow-hidden"><?php echo $mem_name;?></p>
			<p class="user_email text-black fw-semibold overflow-hidden">[ <?php echo $this->mres['sponsor_id'];?> ]</p>
			<p class="user_email text-black overflow-hidden"><?php echo $this->mres['user_name'];?></p>
		</div>
	</div>
	<?php } ?>
	<div class="acc_box position-relative mh-100 h-100 mt-3">
		<ul class="acc_links m-0 p-0 text-white">
			<li <?php echo ($menu_dashboard_active == 'dashboard')? 'class="acc_act"' : '';?>>
				<a href="<?php echo site_url('members');?>" title="Dashboard">
					<img src="<?php echo theme_url();?>images/lft-ico1.svg" alt="" class="acc_ico"> 
					<span class="trans_eff">Dashboard</span>
				</a>
			</li>

			 <li <?php echo ($seg == 'artists')? 'class="acc_act"' : '';?>>
				<a href="<?php echo site_url('admin/artists/add');?>" title="Artists Manage"><img src="<?php echo theme_url();?>images/upload_icon.svg" alt="" class="acc_ico" style="width: 26px;"> <span>ADM Artists</span></a>
			</li>
            <li <?php echo ($menu_dashboard_active == 'metas')? 'class="acc_act"' : '';?>>
				<a href="<?php echo site_url('admin/metas/');?>" title="Meta Manage"><img src="<?php echo theme_url();?>images/upload_icon.svg" alt="" class="acc_ico" style="width: 26px;"> <span>ADM Metas</span></a>
			</li>

			<li <?php echo ($menu_dashboard_active == 'labels')? 'class="acc_act"' : '';?>>
				<a href="<?php echo site_url('members/labels');?>" title="Label Manage"><img src="<?php echo theme_url();?>images/lft-ico6.svg" alt="" class="acc_ico"> <span>Labels</span></a>
			</li>

			<li <?php echo ($menu_dashboard_active == 'meta_data')? 'class="acc_act"' : '';?>>
				<a href="<?php echo site_url('members/meta_data');?>" title="Meta Data"><img src="<?php echo theme_url();?>images/lft-ico7.svg" alt="" class="acc_ico"> <span>Meta Data</span></a>
			</li>
			<li <?php echo ($menu_dashboard_active == 'channel_add_request')? 'class="acc_act"' : '';?>>
				<a href="<?php echo site_url('members/channel_add_request');?>" title="Youtube Request"><img src="<?php echo theme_url();?>images/lft-ico10.svg" alt="" class="acc_ico"> <span>Youtube Request</span></a>
			</li>
			<li <?php echo ($menu_dashboard_active == 'change_password')? 'class="acc_act"' : '';?>>
				<a href="<?php echo site_url('members/change_password');?>" title="Change Password"><img src="<?php echo theme_url();?>images/setting.svg" alt="" class="acc_ico"> <span>Change Password</span></a>
			</li>
		</ul>
	</div>
	<p class="acc_copyright text-center mt-4">
		Copyright &copy; <?php echo date('Y');?> <br><span class="fs-6"><?php echo $site_title_text;?></span><br>All rights reserved.
	</p>
</div>