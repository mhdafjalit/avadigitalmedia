<?php
$default_poster_name="";
$default_mail="";
if($this->userId>0){
	$my_ci =& get_instance();
	if(!empty($my_ci->mres)){
		$default_poster_name= trim($my_ci->mres['first_name']." ".$my_ci->mres['last_name']);
		$default_mail= $my_ci->mres['user_name'];
	}
}
$this->load->view('top_application',array('has_header'=>false,'ws_page'=>'refer_fr','is_popup'=>true,'has_body_style'=>'padding:0'));?>
<img src="<?php echo theme_url();?>images/refer-friend-img.jpg" class="w-100" alt="">
<div class="p-3 text-center">
	<h1>Refer to Friend</h1>
	<?php echo error_message();
	 echo form_open(current_url_query_string(),'name="refer_frm" autocomplete="off"');
	 ?>
	<div class="mt-4">
		<input name="your_name" id="your_name" type="text"  placeholder="Name *" value="<?php echo set_value('your_name',$default_poster_name);?>" class="p-2 w-100">
		<?php echo form_error('your_name');?>
	</div>
	<div class="mt-1">
		<input name="your_email" id="your_email" type="text"  placeholder="Email ID *" value="<?php echo set_value('your_email',$default_mail);?>" class="p-2 w-100">
		<?php echo form_error('your_email');?>
	</div>
	<div class="mt-1">
		<input name="friend_name" id="friend_name" type="text"  placeholder="Friend's Name *" value="<?php echo set_value('friend_name');?>" class="p-2 w-100">
		<?php echo form_error('friend_name');?>
	</div>
	<div class="mt-1">
		<input name="friend_email" id="friend_email" type="text" value="<?php echo set_value('friend_email');?>"  placeholder="Friend's Email ID *" class="p-2 w-100">
		<?php echo form_error('friend_email');?>
	</div>
	<p class="mt-2"><input name="verification_code" id="verification_code"  type="text" class="p-2" placeholder="Enter Code *" style="width:105px;"> <img src="<?php echo base_url();?>captcha/normal/refer" class="vam" alt="" title="" id="captcha_img_refer"> <a href="#" class="captcha_refresh" data-src="<?php echo base_url();?>captcha/normal/refer" data-cont="#captcha_img_refer" title="Refresh Code"><img src="<?php echo theme_url();?>images/refresh.png" class="vam" alt="" title="" ></a></p>
	<?php echo form_error('verification_code');?>
	<p class="clearfix"></p>
	<div class="mt-2">
		<input name="btn_sbt" type="submit" class="btn btn-primary" value="Submit"> 
	</div>
	<?php echo form_close();?>
</div>
</body>
</html>