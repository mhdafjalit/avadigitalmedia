<?php 
	$this->load->view('top_application'); 
	$bdcm_array = array(
		array('heading'=>'Release','url'=>'members/release'),
		array('heading'=>'Dashboard','url'=>'members')
	);
$album_type = (int) $this->input->get_post('album_type');
$releaseId = isset($res['release_id']) ? md5($res['release_id']) : (null !== ($segment = $this->uri->segment(4)) ? $segment : null);
if(is_array($res) && !empty($res))
{
	$original_release_date_of_music	= set_value('original_release_date_of_music',$res['original_release_date_of_music']);
}
else
{
  	$original_release_date_of_music = set_value('original_release_date_of_music');
}?>
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
							<a class="nav-link" href="<?php echo site_url('members/release/new_release/'.$releaseId.'?album_type='.$album_type);?>">Release Information</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('members/release/upload_release/'.$releaseId.'?album_type='.$album_type);?>">Upload</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('members/release/tracks/'.$releaseId.'?album_type='.$album_type);?>">Tracks</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('members/release/stores/'.$releaseId.'?album_type='.$album_type);?>">Stores</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('members/release/territories/'.$releaseId.'?album_type='.$album_type);?>">Territories</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" aria-current="page" href="<?php echo site_url('members/release/date_release/'.$releaseId.'?album_type='.$album_type);?>">Release Date</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('members/release/submission/'.$releaseId.'?album_type='.$album_type);?>">Submission</a>
						</li>
					</ul>
					<p class="border-bottom"></p>
					<?php
			       	echo error_message();
			       	echo form_open(current_url_query_string(),'id="add_release_date" autocomplete="off"');
					?>
					<div class="row g-3 mt-3">
						<div class="col-sm-6">
							<label class="form-label">Original Music Release Date * <img src="<?= theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Original Music Release Date"></label>
							<input type="text" name="original_release_date_of_music" class="form-control release_date1" value="<?php echo $original_release_date_of_music;?>">
							<?php echo form_error('original_release_date_of_music');?>
						</div>
                        <?php /*
                        <div class="col-sm-6">
							<label class="form-label">State </label>
							<input type="text" name="main_release_state" class="form-control" value="<?php echo $main_release_state;?>">
							<?php echo form_error('main_release_state');?>
						</div>
						*/?>
					</div>
                   
					<div class="mt-3">
						<input name="action" type="submit" class="btn btn-purple" value="Submit">
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
	$(document).ready(function(){
	  $(document).on('click','.release_date',function(e){
	    e.preventDefault();
	    cls = $(this).hasClass('release_date') ? 'release_date1' : 'release_date1';
	    $('.'+cls+':eq(0)').focus();
	  });
	  $(document).on('focus','.release_date1',function(){
	    $(this).datepicker({
			showOn: "focus",
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			defaultDate: 'y',
			buttonText:'',
			minDate:'<?php echo date('Y-m-d',strtotime(date('Y-m-d',time())));?>' ,
			maxDate:'<?php echo date('Y-m-d',strtotime('+5 years'));?>',
			yearRange: "c-100:c+100",
			buttonImageOnly: true,
	      	onSelect: function(dateText, inst) {
		        $('.release_date1').val(dateText);
	      	}
	    });
	  });
	});
</script>
<?php $this->load->view("bottom_application");?>