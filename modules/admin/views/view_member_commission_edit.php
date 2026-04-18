<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
	array('heading'=>'Manage Members','url'=>'admin/members'),
	array('heading'=>'Dashboard','url'=>'admin')
);
$uerId= $this->uri->segment(3);
?>
<div class="dash_outer">
	<div class="dash_container">
    <?php $this->load->view('view_left_sidebar'); ?>
    <div id="main-content" class="h-100">
    	<?php $this->load->view('view_top_sidebar');?>
    	<div class="top_sec d-flex justify-content-between">
      	<h1 class="mt-4"><?php echo $heading_title;?></h1>
        <?php echo navigation_breadcrumb($heading_title,$bdcm_array); ?>
    	</div>
    	<p class="clearfix"></p>
    	<div class="main-content-inner">
    		<div class="dash_box p-4">
    			<ul class="nav nav-underline tabber_style">
				  <li class="nav-item">
				  	<a class="nav-link" aria-current="page" href="<?php echo site_url('admin/member_edit/'.$uerId);?>">Profile Setting</a>
				  </li>
				  <li class="nav-item">
				  	<a class="nav-link" href="<?php echo site_url('admin/edit_member_bank_info/'.$uerId);?>">Tax & Bank Info</a>
				  </li>
				  <li class="nav-item">
				  	<a class="nav-link active" href="<?php echo site_url('admin/edit_member_rate/'.$uerId);?>">User Rate</a>
				  </li>
				  <li class="nav-item">
				  	<a class="nav-link" href="<?php echo site_url('admin/member_profile/'.$uerId);?>">Profile</a>
				  </li>
				</ul>
				<p class="border-bottom"></p>
				<?php echo error_message();?>
				<?php echo form_open(current_url_query_string(),'name="edit_rate_frm" class="edit_rate_frm" id="edit_rate_frm" autocomplete="off"');
				?>
				<div class="row g-3 mt-3">
					<div class="col-sm-6 col-lg-4">
						<label for="gst_number" class="form-label">User Id</label>
						<input type="text" class="form-control" name="sponsor_id" id="sponsor_id" value="<?php echo set_value('sponsor_id',$mres['sponsor_id']);?>" readonly>
					</div>

					<div class="col-sm-6 col-lg-4">
						<label for="commission" class="form-label">User Rate % *</label>
						<input type="text" class="form-control" name="commission" id="commission" value="<?php echo set_value('commission',$mres['commission']);?>" <?php if($this->member_type != '1'){?> style="background-color: #CCC;" readonly <?php } ?>>
						<?php echo form_error('commission');?>
					</div>
				</div>
				<div class="mt-3">
					<input type="hidden" name="action" value="subadmin">
					<input name="submit" type="submit" class="btn btn-purple" value="Update">
				</div>
				<?php echo form_close();?>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php $this->load->view("bottom_application");?>