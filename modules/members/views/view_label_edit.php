<?php 
	$this->load->view('top_application'); 
	$bdcm_array = array(
		array('heading'=>'Manage Labels','url'=>'members/labels'),
		array('heading'=>'Dashboard','url'=>'members')
	);
?>
<div class="dash_outer">
	<div class="dash_container">
    <?php $this->load->view('view_left_sidebar'); ?>
    <div id="main-content" class="h-100">
    	<?php $this->load->view('view_top_sidebar');?>
    	<div class="top_sec d-flex justify-content-between">
      	<h1 class="mt-4"><?php echo $heading_title;?></h1>
        <?php echo navigation_breadcrumb($heading_title,$bdcm_array);?>
    	</div>
    	<p class="clearfix"></p>
    	<div class="main-content-inner">
				<div class="dash_box p-4">
					<?php 
					echo error_message();
					echo form_open_multipart(current_url_query_string(),'name="label_frm" id="label_frm" autocomplete="off"');?>
					<div class="row g-3">
						<div class="col-sm-6 col-lg-4">
							<label for="channel_name" class="form-label">User/Channel Name *</label>
							<input type="text" class="form-control" name="channel_name" id="channel_name" value="<?php echo set_value('channel_name',$res['channel_name']);?>">
							<?php echo form_error('channel_name');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label for="channel_url" class="form-label">Channel URL *</label>
							<input type="text" class="form-control" name="channel_url" id="channel_url" value="<?php echo set_value('channel_url',$res['channel_url']);?>">
							<?php echo form_error('channel_url');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label for="email" class="form-label">Email Id *</label>
							<input type="text" class="form-control" name="email" id="email" value="<?php echo set_value('email',$res['email']);?>">
							<?php echo form_error('email');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label for="phone" class="form-label">Phone No. *</label>
							<input type="text" class="form-control" name="phone" id="phone" value="<?php echo set_value('phone',$res['phone']);?>">
							<?php echo form_error('phone');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label for="user_rate" class="form-label">User Rate % *</label>
							<input type="text" class="form-control" name="user_rate" id="user_rate" value="<?php echo set_value('user_rate',$res['user_rate']);?>">
							<?php echo form_error('user_rate');?>
						</div>
						<div class="col-12">
							<label for="Email" class="form-label mt-3 text-uppercase text-black">Agreement Period</label>
							<div class="row g-0 mt-2">
								<div class="col-6 col-sm-4 pe-1">
									<small>From: *</small> 
									<input name="agreement_from" id="agreement_from" type="text" class="form-control start_date1" value="<?php echo set_value('agreement_from',$res['agreement_from']);?>" placeholder="yyyy-mm-dd" readonly="readonly">
									<?php echo form_error('agreement_from');?>
								</div>
								<div class="col-6 col-sm-4 ps-1">
									<small>To: *</small> 
									<input name="agreement_to" id="agreement_to" type="text" class="form-control end_date1" value="<?php echo set_value('agreement_to',$res['agreement_to']);?>" placeholder="yyyy-mm-dd" readonly="readonly">
									<?php echo form_error('agreement_to');?>
								</div>
							</div>
						</div>
						<div class="col-12"><label for="Email" class="form-label mt-3 text-uppercase text-black">Upload</label></div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label"> Upload User Agreement * </label>
							<input type="file" class="form-control" name="agreement_doc" id="agreement_doc">
							<?php 
							if($res['agreement_doc']!='' && file_exists(UPLOAD_DIR.'/labels/'.$res['agreement_doc'])){?>
				              	<p class="mt-2"><a href="<?php echo base_url().'uploaded_files/labels/'.$res['agreement_doc'];?>" class="text-info pop1 me-2"> View</a> | 
	      						<input type="checkbox" name="agreement_doc_delete" value="Y" /> Delete </p>
			              		<?php 
			              	}?>
							<p class="mt-1 fs-8">[ <?php echo $this->config->item('member_doc.best.image.view');?> ]</p>
							<?php echo form_error('agreement_doc');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label"> Upload Government Id * </label>
							<input type="file" class="form-control" name="government_doc" id="government_doc">
							<?php 
							if($res['government_doc']!='' && file_exists(UPLOAD_DIR.'/labels/'.$res['government_doc'])){?>
				              	<p class="mt-2"><a href="<?php echo base_url().'uploaded_files/labels/'.$res['government_doc'];?>" class="text-info pop1 me-2"> View</a> | 
	      						<input type="checkbox" name="government_doc_delete" value="Y" /> Delete </p>
			              		<?php 
			              	}?>
							<p class="mt-1 fs-8">[ <?php echo $this->config->item('member_doc.best.image.view');?> ]</p>
							<?php echo form_error('government_doc');?>
						</div>
					</div>
					<div class="mt-3">
						<input type="hidden" name="action" value="subadmin">
						<input name="submit" type="submit" class="btn btn-purple" value="Update">
					</div>
					<?php echo form_close();?>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/developers/js/multichange_dn.js"></script>
<?php
$default_date = $this->config->item('site_start_date');
$posted_agreement_from = $this->input->post('agreement_from');
?>
<script>
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
	$(document).ready(function(){
	  $('[id ^="per_page"]').on('change',function(){
	    $(':hidden[name="end_date"]','#ord_frm').val($(this).val());
	    $('#ord_frm').submit();
	  });
	  $(document).on('click','.start_date,.end_date',function(e){
	    e.preventDefault();
	    cls = $(this).hasClass('start_date') ? 'start_date1' : 'end_date1';
	    $('.'+cls+':eq(0)').focus();
	  });
	  $(document).on('focus','.start_date1',function(){
	    $(this).datepicker({
	      showOn: "focus",
	      dateFormat: 'yy-mm-dd',
	      changeMonth: true,
	      changeYear: true,
	      defaultDate: 'y',
	      buttonText:'',
	      minDate:'<?php echo $default_date;?>' ,
	      maxDate:'<?php echo date('Y-m-d',strtotime(date('Y-m-d',time())));?>',
	      yearRange: "c-100:c+100",
	      buttonImageOnly: true,
	      onSelect: function(dateText, inst) {
	        $('.start_date1').val(dateText);
	        $(".end_date1").datepicker("option",{
	          minDate:dateText ,
	          maxDate:'<?php echo date('Y-m-d',strtotime('+365 days'));?>',
	        });
	      }
	    });
	  });
	  $(document).on('focus','.end_date1',function(){
	    $(this).datepicker({
	      showOn: "focus",
	      dateFormat: 'yy-mm-dd',
	      changeMonth: true,
	      changeYear: true,
	      defaultDate: 'y',
	      buttonText:'',
	      minDate:'<?php echo $posted_agreement_from!='' ? $posted_agreement_from :  $default_date;?>' ,
	      maxDate:'<?php echo date('Y-m-d',strtotime('+2 years'));?>',
	      yearRange: "c-100:c+100",
	      buttonImageOnly: true,
	      onSelect: function(dateText, inst) {
	        $('.end_date1').val(dateText);
	      }
	    });
	  });
	});
</script>
<?php $this->load->view("bottom_application");?>