<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
	array('heading'=>'Sub User Manage','url'=>'admin/sub_admins'),
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
					  	<a class="nav-link active" aria-current="page" href="<?php echo site_url('admin/edit_sub_admin/'.$uerId);?>">Profile Setting</a>
					  </li>
					  <li class="nav-item">
					  	<a class="nav-link" href="<?php echo site_url('admin/edit_bank_info/'.$uerId);?>">Tax & Bank Info</a>
					  </li>
					  <li class="nav-item">
					  	<a class="nav-link" href="<?php echo site_url('admin/edit_user_rate/'.$uerId);?>">User Rate</a>
					  </li>
					  <li class="nav-item">
					  	<a class="nav-link" href="<?php echo site_url('admin/view_profile/'.$uerId);?>">Profile</a>
					  </li>
					</ul>
					<p class="border-bottom"></p>
					<?php echo error_message();?>
					<?php echo form_open_multipart(current_url_query_string(),'name="edit_subadmin_frm" class="edit_subadmin_frm" id="edit_subadmin_frm" autocomplete="off"');?>
					<div class="row g-3 mt-3">
						<div class="col-sm-6 col-lg-4">
							<label for="Name" class="form-label">Name *</label>
							<input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo set_value('first_name',$mres['first_name']);?>">
							<?php echo form_error('first_name');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label for="user_name" class="form-label">Email Id *</label>
							<input type="text" class="form-control" name="user_name" id="user_name" value="<?php echo set_value('user_name',$mres['user_name']);?>" <?php if($this->member_type != '1'){?> style="background-color: #CCC;" readonly <?php } ?>>
							<?php echo form_error('user_name');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label for="Phone" class="form-label">Phone No. *</label>
							<input type="text" class="form-control" name="mobile_number" id="mobile_number" value="<?php echo set_value('mobile_number',$mres['mobile_number']);?>" <?php if($this->member_type != '1'){?> style="background-color: #CCC;" readonly <?php } ?>>
							<?php echo form_error('mobile_number');?>
						</div>
						<div class="col-sm-6 col-lg-8">
							<label for="address" class="form-label">Address *</label>
							<input type="text" class="form-control" name="address" id="address" value="<?php echo set_value('address',$mres['address']);?>">
							<?php echo form_error('address');?>
						</div>

						<div class="col-sm-6 col-lg-4">
							<label for="Country" class="form-label"> Country </label>
							<?php echo CountrySelectBox(array('name'=>'country','id'=>'country','opt_val_fld'=>'id','format'=>'data-fld-key="country_id" class="form-select overlay_enable_transparent" data-next-sel-val="'.(set_value('state',$mres['state'])).'"  data-class="ajx_location" data-url="remote/load_states"','current_selected_val'=>set_value('country',($mres['country'] ?? 99))));?>
							<?php echo form_error('country');?>
						</div>

						<div class="col-sm-6 col-lg-4">
							<label for="State" class="form-label">State </label>
							<?php /*?>
							<select name="state" data-fld-key="state_id" id="state" data-next-sel-val="<?php echo set_value('city');?>"  class="form-select" data-class="ajx_location" data-url="remote/load_cities">
							 <?php */?>
                            <select name="state" data-fld-key="state_id" id="state" class="form-select" data-class="ajx_location" >
        				          <option value="">State </option>
        			        </select>
			                <?php echo form_error('state');?>
						</div>

						<div class="col-sm-6 col-lg-4">
							<label for="city" class="form-label">City </label>
							<input type="text" name="city" id="city" class="form-control" value="<?php echo set_value('city',$mres['city_name']);?>">
			                <?php echo form_error('city');?>
						</div>

						<div class="col-sm-6 col-lg-4">
							<label for="Postcode" class="form-label">Postcode </label>
							<input type="text" class="form-control" name="pin_code" id="pin_code" value="<?php echo set_value('pin_code',$mres['pin_code']);?>">
							<?php echo form_error('pin_code');?>
						</div>
						<?php /*
						<div class="col-sm-6 col-lg-4">
							<label for="Role" class="form-label"> Role *</label>
							<select class="form-select" id="Role">
							<option>Select</option>
							</select>
						</div>

						<div class="col-sm-6 col-lg-4">
							<label for="Reason" class="form-label">Role Change Reason * </label>
							<input type="text" class="form-control" id="Reason">
						</div>
						*/?>
						<div class="col-sm-6 col-lg-6">
						<label class="form-label"> Upload User Agreement * </label>
						<input type="file" name="agreement_doc" id="agreement_doc" class="form-control">
							<?php 
							if($mres['agreement_doc']!='' && file_exists(UPLOAD_DIR.'/members/'.$mres['agreement_doc'])){?>
              	<p class="mt-2"><a href="<?php echo base_url().'uploaded_files/members/'.$mres['agreement_doc'];?>" class="text-info pop1 me-2"> View</a> | 
								<input type="checkbox" name="agreement_doc_delete" value="Y" /> Delete </p>
            		<?php 
            	}?>
							<p class="mt-1 fs-8">[ <?php echo $this->config->item('member_doc.best.image.view');?> ]</p>
							<?php echo form_error('agreement_doc');?>
						</div>

						<div class="col-sm-6 col-lg-6">
							<label class="form-label"> Upload Government Id * </label>
							<input type="file" name="aadhar_doc" id="aadhar_doc" class="form-control">
							<?php 
							if($mres['aadhar_doc']!='' && file_exists(UPLOAD_DIR.'/members/'.$mres['aadhar_doc'])){?>
              	<p class="mt-2"><a href="<?php echo base_url().'uploaded_files/members/'.$mres['aadhar_doc'];?>" class="text-info pop1 me-2"> View</a> | 
  							<input type="checkbox" name="aadhar_doc_delete" value="Y" /> Delete </p>
            		<?php 
            	}?>
							<p class="mt-1 fs-8">[ <?php echo $this->config->item('member_doc.best.image.view');?> ]</p>
							<?php echo form_error('aadhar_doc');?>
						</div>
						<div class="col-sm-6 mt-4">
							<div class="dtl_upload_img">
								<div class="float-start me-3 upload_thm">
									<?php
									if($mres['profile_photo']!='' && file_exists(UPLOAD_DIR.'/profiles/'.$mres['profile_photo'])){
										$profile_photo = base_url().'uploaded_files/profiles/'.$mres['profile_photo'];
									}else{
										$profile_photo = theme_url().'images/no-img.jpg';
									}?>
									<img id="documentUpload1" document-up="" src="<?php echo $profile_photo;?>" alt="Profile img">
								</div>
								<input name="profile_photo" id="profile_photo" class="dg_custom_file" type="file" onchange="readURL(this,1);" accept="image/*">
								<p class="attach_btn mt-2 text-uppercase">
									<a href="javascript:void(0)" class="text-primary fw-medium">Upload Image *</a>
								</p>
								<p class="mt-2 fs-8">[ <?php echo $this->config->item('profile_pic.best.image.view');?> ]</p>
								<p class="clearfix"></p>
							</div>
							<?php echo form_error('profile_photo');?>
						</div>
						<div class="col-sm-6 mt-4"></div>
						</div>
						<div class="mt-3">
							<input type="hidden" name="action" value="subadmin">
							<input name="submit" type="submit" class="btn btn-purple" value="Update">
						</div>
					</div>
					<?php echo form_close();?>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- MIDDLE ENDS -->
<?php
$pr_country_id = 156;
$data_trigger =  $pr_country_id>0 ? 'Y' : 'N';
?>
<script type="text/javascript" src="<?php echo base_url();?>assets/developers/js/multichange_dn.js"></script>
<script>
var jq_dn_group = {'ajx_location':{}};
$.multichange_selectbox(jq_dn_group,'<?php echo $data_trigger;?>','Y');
</script>
<script type="text/javascript">
function readURL(input, option) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      $("#documentUpload" + option).attr('src', e.target.result);
    };
    reader.readAsDataURL(input.files[0]);
  }
}
$('.attach_btn').click(function() {
  $(this).prev().trigger('change');
});

$('.dg_custom_file').change(function() {
  $(this).parent().children('b.file_url').text($(this).attr('value'));
});
</script>
<?php $this->load->view("bottom_application");?>