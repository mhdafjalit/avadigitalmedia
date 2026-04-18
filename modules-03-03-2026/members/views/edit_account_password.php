<?php 
	$this->load->view('top_application'); 
	$bdcm_array = array(
		array('heading'=>'Dashboard','url'=>'admin')
	);
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
        			<?php echo error_message();?>
        			<?php echo form_open(current_url_query_string(),'name="pwd_frm" class="pwd_frm" id="pwd_frm" autocomplete="off"');?>
        			<div class="row g-3 mt-2">
        				<div class="col-sm-6 col-lg-4">
        					<label for="old" class="form-label">Old Password *</label>
        					<input type="password" class="form-control" name="old_password" id="old_password">
        					<?php echo form_error('old_password');?>
        				</div>
        				<div class="col-sm-6 col-lg-4">
        					<label for="New" class="form-label">New Password *</label>
    				    	<div class="position-relative">
                				<a href="#" class="login_eye" onclick="togglePasswordVisibility(event)">
                			        <img src="<?php echo theme_url();?>images/eye.svg" alt="Show Password" id="eye_icon">
                			    </a>
                				<input type="password" class="form-control" name="new_password" id="new_password" >
                			</div>
    						<?php echo form_error('new_password');?>
    						<small>[<?php echo $this->config->item('password.suggestion');?>]</small>
        				</div>
        				<div class="col-sm-6 col-lg-4">
        					<label for="Confirm" class="form-label">Confirm Password</label>
        					<input type="password" class="form-control" name="confirm_password" id="confirm_password">
        					<?php echo form_error('confirm_password');?>
        				</div>
        				<div class="col-12">
        					<input type="hidden" name="action" value="change_password">
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
<script>
function togglePasswordVisibility(event) {
    event.preventDefault();
    var passwordInput = document.getElementById("new_password");
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
<?php $this->load->view("bottom_application");?>