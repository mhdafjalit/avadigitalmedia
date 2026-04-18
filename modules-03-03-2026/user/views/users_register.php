<?php 
/*Posted Values*/
$posted_user_name = set_value('user_name');
$posted_password = set_value('password');
$posted_confirm_password = set_value('confirm_password');
$posted_first_name = set_value('first_name');
$posted_mobile_number = set_value('mobile_number');
/*Posted Values Ends*/
$this->load->view('top_application',array('has_header'=>false,'has_body_class'=>'login_bg'));
$site_title_text = escape_chars($this->config->item('site_name'));?>
<div class="container-xxl">
	<div class="reg_box">
		<div class="reg_box_inner">
			<p class="text-center">
				<img src="<?php echo theme_url();?>images/auva.jpg" width="175" height="83" alt="<?php echo $site_title_text;?>">
			</p>
			<p class="text-center mt-2 fs-5 text-black fw-medium">Create an account</p>
			<?php
			echo error_message();
			echo form_open_multipart('','name="register_frm" id="register_frm" autocomplete="off"');?>
			<div class="ps-4 pe-4">
				<div class="row g-2 mt-1">
					<div class="col-sm-6">
						<div class="reg_field">
							<input name="first_name" id="first_name" value="<?php echo $posted_first_name;?>" type="text" class="border-0 bg-transparent w-100" placeholder="Name *">
						</div>
						<?php echo form_error('first_name');?>
					</div>
					<div class="col-sm-6">
						<div class="reg_field">
							<input type="text" id="mobile_number" name="mobile_number" value="<?php echo $posted_mobile_number;?>" class="border-0 bg-transparent w-100" placeholder="Mobile *">
						</div>
						<?php echo form_error('mobile_number');?>
					</div>
					<div class="col-12">
						<div class="reg_field">
							<input type="text" name="user_name" id="user_name" value="<?php echo $posted_user_name;?>" class="border-0 bg-transparent w-100" placeholder="Email Id *">
						</div>
						<?php echo form_error('user_name');?>
					</div>
					<div class="col-sm-6">
				    	<div class="reg_field position-relative">
            				<a href="#" class="login_eye" onclick="togglePasswordVisibility(event)">
            			        <img src="<?php echo theme_url();?>images/eye.svg" alt="Show Password" id="eye_icon">
            			    </a>
            				<input type="password" id="password" name="password" class="border-0 bg-transparent w-100" value="<?php echo $posted_password;?>" placeholder="Password *">
            			</div>
						<?php echo form_error('password');?>
						<small>[<?php echo $this->config->item('password.suggestion');?>]</small>
					</div>
					<div class="col-sm-6">
						<div class="reg_field">
							<input type="password" name="confirm_password" value="<?php echo $posted_confirm_password;?>" placeholder="Confirm Password *">
						</div>
						<?php echo form_error('confirm_password');?>
					</div>
				</div>
			</div>
			<div class="p-4 mt-3" style="background:#fef5ff;">
				<p class="fw-bold text-uppercase">Upload Documents</p>
				<div class="row g-2 mt-1">
					<div class="col-sm-6">
						<label class="mb-1 fw-medium fs-7">Aadhaar Card *</label>
						<input type="file" name="aadhar_doc" id="aadhar_doc" class="form-control">
						<?php echo form_error('aadhar_doc');?>
					</div>
					<div class="col-sm-6">
						<label class="mb-1 fw-medium fs-7">PAN Card *</label>
						<input type="file" name="pancard_doc" id="pancard_doc" class="form-control">
						<?php echo form_error('pancard_doc');?>
					</div>
					<div class="col-sm-6">
						<label class="mb-1 fw-medium fs-7">Bank Passbook *</label>
						<input type="file" name="bank_passbook" id="bank_passbook" class="form-control">
						<?php echo form_error('bank_passbook');?>
					</div>
				</div>
			</div>
			<div class="p-4">
				<p class="fw-bold text-uppercase">Bank Details</p>
				<div class="row g-2 mt-1">
					<div class="col-sm-6">
						<div class="reg_field">
							<input type="text" name="ac_holder_name" id="ac_holder_name" value="<?php echo set_value("ac_holder_name");?>" class="border-0 bg-transparent w-100" placeholder="Account Holder Name *">
						</div>
						<?php echo form_error('ac_holder_name');?>
					</div>
					<div class="col-sm-6">
						<div class="reg_field">
							<input type="text" name="account_no" id="account_no" value="<?php echo set_value("account_no");?>" class="border-0 bg-transparent w-100" placeholder="Account No. *">
						</div>
						<?php echo form_error('account_no');?>
					</div>
					<div class="col-sm-6">
						<div class="reg_field">
							<input type="text" name="ifsc_code" id="ifsc_code" class="border-0 bg-transparent w-100" placeholder="IFSC Code *" value="<?php echo set_value("ifsc_code");?>">
						</div>
						<?php echo form_error('ifsc_code');?>
					</div>
					<div class="col-sm-6">
						<div class="reg_field">
							<input type="text" name="bank_name" id="bank_name" value="<?php echo set_value("bank_name");?>" class="border-0 bg-transparent w-100" placeholder="Bank Name *">
						</div>
						<?php echo form_error('bank_name');?>
					</div>
				</div>
			</div>
			<div class="login_tab mt-1 text-center">
				<button type="submit" name="btn_sbt" value="register" class="text-white rounded-5 fw-bold d-inline-block trans_eff">Sign Up</button>
			</div>
			<?php echo form_close();?>
		</div>
	</div>
	<p class="clearfix"></p>
</div>
<script>
function togglePasswordVisibility(event) {
    event.preventDefault();
    var passwordInput = document.getElementById("password");
    var eyeIcon = document.getElementById("eye_icon");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeIcon.src = "<?php echo theme_url(); ?>images/eye2.svg";
    } else {
        passwordInput.type = "password";
        eyeIcon.src = "<?php echo theme_url(); ?>images/eye.svg";
    }
}
</script>
<?php $this->load->view('bottom_application',array('ws_page'=>'register','has_footer'=>false));?>