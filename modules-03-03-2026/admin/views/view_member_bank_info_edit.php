<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
	array('heading'=>'Manage Members','url'=>'admin/members'),
	array('heading'=>'Dashboard','url'=>'admin')
);
$uerId= $this->uri->segment(3);
$bank_account_type = set_value('bank_account_type',$mres['bank_account_type']); 
$bank_service_provider = set_value('bank_service_provider',$mres['bank_service_provider']); 
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
				  	<a class="nav-link active" href="<?php echo site_url('admin/edit_member_bank_info/'.$uerId);?>">Tax & Bank Info</a>
				  </li>
				  <li class="nav-item">
				  	<a class="nav-link" href="<?php echo site_url('admin/edit_member_rate/'.$uerId);?>">User Rate</a>
				  </li>
				  <li class="nav-item">
				  	<a class="nav-link" href="<?php echo site_url('admin/member_profile/'.$uerId);?>">Profile</a>
				  </li>
				</ul>
				<p class="border-bottom"></p>
				<?php echo error_message();?>
				<?php echo form_open_multipart(current_url_query_string(),'name="edit_bankinfo_frm" class="edit_bankinfo_frm" id="edit_bankinfo_frm" autocomplete="off"');
				$is_gst = set_value('is_gst',$mres['is_gst']);
				?>
				<div class="row g-3 mt-3">
					<div class="col-sm-6 col-lg-4">
						<label class="form-label d-block">GST *</label>
						<label class="me-4">
							<input type="radio" name="is_gst" value="1" onclick="$('.gst').show();" <?php echo ($is_gst==1)? 'checked':'';?>> <b class="fw-medium">Yes</b>
						</label>
						<label>
							<input type="radio" name="is_gst" value="0" onclick="$('.gst').hide();" <?php echo ($is_gst==0 || $is_gst=='')? 'checked':'';?>> <b class="fw-medium">No</b>
						</label>
						<?php echo form_error('is_gst');?>
					</div>
					<div class="col-sm-6 col-lg-4 gst <?php echo ($is_gst==1)? '' : ' dn';?>">
						<label for="gst_number" class="form-label">GST Number</label>
						<input type="text" class="form-control" name="gst_number" id="gst_number" value="<?php echo set_value('gst_number',$mres['gst_number']);?>">
						<?php echo form_error('gst_number');?>
					</div>

					<div class="col-sm-6 col-lg-4">
						<label for="pan_number" class="form-label">PAN Number *</label>
						<input type="text" class="form-control" name="pan_number" id="pan_number" value="<?php echo set_value('pan_number',$mres['pan_number']);?>">
						<?php echo form_error('pan_number');?>
					</div>
				</div>

				<p class="fw-semibold mt-3 mb-2">Account Type</p>
				<label class="me-4">
					<input type="radio" name="bank_account_type" class="tabs" value="0" title="form_1" <?php echo ($bank_account_type=='0' || $bank_account_type=='') ? 'checked="checked"' : '';?> /> <b class="fw-medium">India</b></label>
				<label>
					<input type="radio" name="bank_account_type" class="tabs" value="1" title="form_2" <?php echo ($bank_account_type=='1') ? 'checked="checked"' : '';?> /> <b class="fw-medium">Outside India</b></label>

				<div class="form_box form_1 mt-3 <?php echo ($bank_account_type=='1') ? 'dn' : '';?>">
					<div class="row g-3">
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">Bank Name *</label>
							<input type="text" name="bank_name" class="form-control" id="bank_name" value="<?php echo set_value("bank_name",$mres['bank_name']);?>">
								<?php echo form_error('bank_name');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label  class="form-label">Account Number *</label>
							<input type="text" name="account_no" class="form-control" id="account_no" value="<?php echo set_value("account_no",$mres['account_no']);?>">
							<?php echo form_error('account_no');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">IFSC Code *</label>
							<input type="text" name="ifsc_code" class="form-control" id="ifsc_code" value="<?php echo set_value("ifsc_code",$mres['ifsc_code']);?>">
							<?php echo form_error('ifsc_code');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">Account Holder Name *</label>
							<input type="text" class="form-control" name="ac_holder_name" id="ac_holder_name" value="<?php echo set_value("ac_holder_name",$mres['ac_holder_name']);?>">
							<?php echo form_error('ac_holder_name');?>
						</div>
						<div class="col-sm-6 col-lg-8">
							<label class="form-label">Branch Address *</label>
							<input type="text" class="form-control" name="bank_address" id="bank_address" value="<?php echo set_value("bank_address",$mres['bank_address']);?>">
							<?php echo form_error('bank_address');?>
						</div>
					</div>
				</div>
				<div class="form_box form_2 mt-3 <?php echo ($bank_account_type=='1') ? '' : 'dn';?>">
					<div class="row g-3">
						<div class="col-12">
							<input type="radio" class="btn-check" name="bank_service_provider" id="Paypal" value="Paypal" <?php echo ($bank_service_provider=='Paypal') ? 'checked="checked"' : '';?>>
							<label class="btn btn-outline-secondary" for="Paypal">Paypal</label>
							<input type="radio" class="btn-check" name="bank_service_provider" id="Payomeer" value="Payoneer" <?php echo ($bank_service_provider=='Payoneer') ? 'checked="checked"' : '';?>>
							<label class="btn btn-outline-secondary" for="Payomeer">Payoneer</label>
							<?php echo form_error('bank_service_provider');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label  class="form-label">Bank Email Id *</label>
							<input type="text" class="form-control" name="bank_email" id="bank_email" value="<?php echo set_value('bank_email',$mres['bank_email']);?>">
							<?php echo form_error('bank_email');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">Bank Customer Id *</label>
							<input type="text" class="form-control" name="bank_customer_id" id="bank_customer_id" value="<?php echo set_value('bank_customer_id',$mres['bank_customer_id']);?>">
							<?php echo form_error('bank_customer_id');?>
						</div>
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