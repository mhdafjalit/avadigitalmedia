<?php
$this->load->view('top_application',array('has_header'=>false,'ws_page'=>'permission_view','is_popup'=>true,'has_body_style'=>''));?>
<div class="p-3 bg-light  border-bottom">
	<h1><?php echo $page_heading;?></h1>
</div>
<div class="row g-0 p-3 fs-7">
  <div class="col-sm-6 mb-3">
  	<b class="d-block text-danger text-uppercase">ID</b>
  	<div class="mt-2"><?php echo $mres['sponsor_id'];?></div>
  </div>
	<div class="col-sm-6 mb-3">
		<b class="d-block text-danger text-uppercase">User</b>
		<div class="mt-2"><?php echo $mres['first_name'];?></div>
	</div>
	<div class="col-sm-12 mb-3">
		<?php
		$config_prvg_arr = $this->config->item('subadmin_privileges');
		if(is_array($db_saved_data) && !empty($db_saved_data)){?>
			<b class="d-block text-danger text-uppercase mb-3">Permission</b>
			<?php
			foreach ($db_saved_data as $key => $val) {
				$privileges = explode(",", $val['permission']);
					echo '<div class="col-sm-12 mb-3">
					<span class="mb-1 fw-semibold">'.$val['section_title'].' : &nbsp;&nbsp;</span>';
					foreach ($privileges as $pval){
						echo ' <span>'.$config_prvg_arr[$pval].'</span>, &nbsp;&nbsp;';
					}
				echo '</div><hr>'; 
			}
		}else{
      echo '<p class="mt-1 text-danger fw-semibold">Permission is not assigned.</p>';
    }?>
	</div>
</div>
<?php $this->load->view("bottom_application",array('has_footer'=>false,'ws_page'=>'permission_view','is_popup'=>true));?>