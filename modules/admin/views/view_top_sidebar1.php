<?php
$show_max_notifications = 2;
$notification_limits = 3;
$where_notification="nc.customer_id='".$this->userId."' AND wn.status='1'";
$params_notification = array(	
			'fields'=>'wn.notification_title,wn.description,nc.created_at,nc.read_status,nc.id,wn.url_hint,wn.url_params,wn.notification_id',
			'from'=>'wl_notification as wn',
			'limit'=>$notification_limits,
			'orderby'=>'nc.created_at DESC',
			'exjoin'=>array(
				array('tbl'=>'wl_notification_customer as nc','condition'=>'nc.notification_id=wn.notification_id')
			),
			'where'=>$where_notification,
			'debug'=>FALSE
			);
$res_notifications   = $this->utils_model->custom_query_builder($params_notification);
$total_notifications = $this->utils_model->total_rec_found;
?>
<header>
	<div class="header sticky">
		<div class="nav_pos hand float-start">
			<a class="tog_menu">
				<img src="<?php echo theme_url();?>images/bar.svg" alt="Navigation">
			</a>
		</div>
		<div class="dashboard_right float-end d-flex justify-content-between">
			<div class="search_top d-inline-block align-top">
				<?php
				/*
				<p class="srch_mob d-block d-lg-none shownext">
					<img src="<?php echo theme_url();?>images/search.svg" alt="Search">
				</p>
				<div class="srch_form rounded-5 mob_hid">
					<input name="" type="text" placeholder="Search Keyword" class="w-100 border-0 bg-transparent">
					<button type="submit" class="border-0 bg-transparent text-center position-absolute"><img src="<?php echo theme_url();?>images/search.svg" alt="Search"></button>
				</div>
				*/?>
			</div>
			<div>
				<?php
				if($this->mres['member_type']=='3'){?>
				<div class="dropdown alert_top d-inline-block align-top position-relative">
					<button type="button" class="btn border-0 p-0 rounded-circle d-block text-center trans_eff" data-bs-toggle="dropdown" aria-expanded="false"><img src="<?php echo theme_url();?>images/bell.svg" alt=""><img src="<?php echo theme_url();?>images/dot.svg" alt="" class="alert_top_dot position-absolute rounded-circle"></button>
					<div class="dropdown-menu alert_box">
						<?php 
						if(is_array($res_notifications) && !empty($res_notifications)){
							foreach($res_notifications as $key=>$val){
								echo '<a href="javascript:void(0);">'.$val['notification_title'].'</a>';
							}
							if($total_notifications>$show_max_notifications){
							echo '<a href="'.site_url('members/notifications').'" class="font-weight-bold">View All</a>';
							}
						}
						else{ 
							echo "<a href='javascript:void(0);'>You don't have latest notifications.</a>";
						} ?>
					</div>
				</div>
				<?php }?>
				               
                <a href="<?php echo site_url('admin/release');?>" title="Create Releases" class="create_top text-white rounded-5 fw-medium trans_eff align-middle"><img src="<?php echo theme_url();?>images/create.svg" width="26" alt="" class="me-0 me-md-1"> <span class=" d-none d-sm-inline-block ">Create Releases</span></a>
                
                
				<?php
				if($this->mres['member_type']!='1'){?>
				<p class="setting_top d-inline-block align-top">
					<a href="<?php echo site_url('admin/edit_sub_admin/'.md5($this->userId));?>" title="Setting" class="rounded-circle d-block text-center trans_eff">
						<img src="<?php echo theme_url();?>images/setting.svg" alt="Setting">
					</a>
				</p>
				<?php }?>
				<p class="logout_top d-inline-block align-top">
					<a href="<?php echo site_url('logout');?>" title="Logout" class="rounded-circle d-block text-center trans_eff">
						<img src="<?php echo theme_url();?>images/logout.svg" alt="Logout">
					</a>
				</p>
			</div>
		</div>
	</div>
</header>