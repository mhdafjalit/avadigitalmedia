<?php $this->load->view('top_application');
$this->load->view("banner/top_inner_banner");
echo navigation_breadcrumb($heading_title);
$admin_address='';
if($this->admin_info->address !=''){
	$admin_address.=$this->admin_info->address;
}
if($this->admin_info->city !=''){
	$admin_address.=', '.$this->admin_info->city;
}
if($this->admin_info->state !=''){
	$admin_address.=', '.$this->admin_info->state;
}
if($this->admin_info->zipcode !=''){
	$admin_address.=' - '.$this->admin_info->zipcode;
}
if($this->admin_info->country !=''){
	$admin_address.=', ('.strtoupper($this->admin_info->country).')';
}
$default_poster_name="";
$default_mail="";
$default_mobile_number="";
if($this->userId>0){
	$my_ci =& get_instance();
	if(!empty($my_ci->mres)){
		$default_poster_name = trim($my_ci->mres['first_name']." ".$my_ci->mres['last_name']);
		$default_mail = $my_ci->mres['user_name'];
		$default_mobile_number = $my_ci->mres['mobile_number'];
	}
}
?>
<!-- MIDDLE STARTS -->
<div class="container mid_area">
	<h1><?php echo $heading_title;?></h1>
	<div class="row">	
		<div class="col-lg-4">
			<div class="contact_area">
				<p class="cnt_heading"><span class="fas fa-map-marker-alt" aria-hidden="true"></span> Address</p>
				<div class="sec_cnt"><?php echo $admin_address;?></div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="contact_area">
			<p class="cnt_heading"><span class="fas fa-phone-volume" aria-hidden="true"></span> Phone</p>
				<div class="sec_cnt black">
					<p class="mt-2">
					 <a href="tel:<?php echo $this->admin_info->phone;?>"><?php echo $this->admin_info->phone;?></a>
					</p>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="contact_area">
				<p class="cnt_heading"><span class="fas fa-envelope" aria-hidden="true"></span> Email</p>
				<div class="sec_cnt mt-3">
					<a href="mailto:<?php echo $this->admin_info->admin_email;?>"> <?php echo $this->admin_info->admin_email;?></a>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="contact_bg mt-3">
	<div class=" container">
		<div class="contact_sec">
			<div class="contact_sec1">
				<h2 class="font-weight-bold">HAVE A QUERY?</h2>
				<p>Just Fill the Below Information:</p>
				<?php echo error_message();?>
				<?php echo form_open('');?>
				<div class="mt-3 contact_form">
					<p class="mt-2">
						<label>
							<input type="text" name="first_name" value="<?php echo set_value('first_name',$default_poster_name);?>" autocomplete="off"  id="first_Name" placeholder="Name *"><?php echo form_error('first_name');?>
						</label>
					</p>
					<p class="mt-2">
						<label>
							<input type="text" name="email" value="<?php echo set_value('email',$default_mail);?>" autocomplete="off"  id="contact-email" placeholder="Email ID *">
							<?php echo form_error('email');?>
						</label>
					</p>
					<p class="clearfix"></p>
					<p class="mt-2">
					<label>
						<?php /*
						<input type="tel" id="mobile_number" name="mobile_number" value="<?php echo set_value('mobile_number',$default_mobile_number);?>" placeholder="Mobile No. *"> */?>
						<input type="text" id="mobile_number" name="mobile_number" value="<?php echo set_value('mobile_number',$default_mobile_number);?>" placeholder="Mobile No. *"><?php echo form_error('mobile_number');?>
					</label>
					</p>
					<p class="mt-2">
						<label>
							<textarea name="comment" id="contact-message" cols="30" rows="5" placeholder="Comment *"><?php echo set_value('comment');?></textarea>
							<?php echo form_error('comment');?>
						</label>
					</p>
					<p class="clearfix"></p>
					<p class="mt-2">
						<input type="text" id="verification_code" name="verification_code" placeholder="Enter Code *" style="width:120px;" autocomplete="off">
						<img src="<?php echo base_url();?>captcha/normal/contact" alt="" id="captcha_img_fp"> 
						<a href="#" class="captcha_refresh" data-src="<?php echo base_url();?>captcha/normal/contact" data-cont="#captcha_img_fp" title="Refresh Code"><img src="<?php echo theme_url();?>images/ref2.png" alt="" class="ml-3"></a>
						<?php echo form_error('verification_code')?>
					</p>
					<div class="mt-3">
						<p>
							<?php /*
							<input type="hidden" name="country" id="country_id" value="<?php echo set_value('country');?>">
							<input type="hidden" name="country_code" id="country_code" value="<?php echo set_value('country_code');?>">
							*/?>
							<input type="hidden" name="action" value="Y">
							<input type="submit" name="submit" id="submit" value="Submit" class=" view_btn"></p>
						<p class="clearfix"></p>
					</div>
				</div>
				<?php echo form_close();?>
			</div>
			<div class="contact_sec2 hidden-xs"><img src="<?php echo theme_url();?>images/contact-img.png" class="mw_98" alt=""></div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php if($this->admin_info->google_map_code!=''){ ?>
<div class="container">
	<div class="map_w pt-3">
		<h2>Google Map</h2>
		<iframe src="<?php echo $this->admin_info->google_map_code;?>" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
	</div>
</div>
<?php } ?>
<!-- MIDDLE ENDS -->
<?php /*
<link rel="stylesheet" href="<?php echo theme_url();?>css/intlTelInput.css">
<script src="<?php echo resource_url();?>Scripts/intlTelInput.js"></script>
<script>
$(window).load(function(e){
	var country_code = $('#country_code').val();
	country_code = country_code=='' ? 'in' : country_code;
	$("#mobile_number").bind("keydown",function(e){
		-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||65==e.keyCode&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&e.keyCode<=40||(e.shiftKey||e.keyCode<48||57<e.keyCode)&&(e.keyCode<96||105<e.keyCode)&&e.preventDefault()}).intlTelInput({initialCountry:country_code});
	$(".country-list li").click(function(){$(this).attr("data-country-code").toUpperCase();var e=$(this).attr("data-dial-code");var code=$(this).attr("data-country-code");$(this).closest("form").find("input[name=country]").val(e);$('#country_code').val(code)});
});
</script>
*/?>
<?php $this->load->view("bottom_application");?>