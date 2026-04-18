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
$this->load->view('top_application',array('has_header'=>false,'ws_page'=>'notification_add','is_popup'=>true,'has_body_style'=>'padding:0'));?>
<div class="p-3 bg-light text-center border-bottom">
	<h1><?php echo $heading_title;?></h1>
</div>
<div class="p-3">
	<?php echo error_message();
 	echo form_open(current_url_query_string(),'name="notification_frm" autocomplete="off"');
 	?>
	<div class="mb-2">
		<label for="notification_title" class="form-label">Title *</label>
		<input type="text" name="notification_title" id="notification_title" class="form-control" value="<?php echo set_value('notification_title');?>">
		<?php echo form_error('notification_title');?>
	</div>
	<div class="mb-2">
		<label for="description" class="form-label">Description *</label>
		<textarea id="description" class="form-control" name="description" rows="6"><?php echo set_value('description');?></textarea>
		<?php echo form_error('description');?>
	</div>
	<div class="text-center">
		<input type="hidden" name="action" value="Y">
		<input name="submit" type="submit" class="btn btn-purple" value="Add">
	</div>
	<?php echo form_close();?>
</div>
</body>
</html>