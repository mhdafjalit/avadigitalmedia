<?php 
	$this->load->view('top_application'); 
	$bdcm_array = array(
		array('heading'=>'Release','url'=>'admin/release'),
		array('heading'=>'Dashboard','url'=>'admin')
	);
$album_type = (int) $this->input->get_post('album_type');
$releaseId = isset($res['release_id']) ? md5($res['release_id']) : (null !== ($segment = $this->uri->segment(4)) ? $segment : null);
if($this->input->post('action') !=''){
	$posted_territories = (!is_array(set_value('release_territories'))) ? []  : array_fill_keys($posted_territories, 1);
}else{
	$posted_territories = $release_territories;	
}
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
							<a class="nav-link" href="<?php echo site_url('admin/release/new_release/'.$releaseId.'?album_type='.$album_type);?>">Release Information</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/upload_release/'.$releaseId.'?album_type='.$album_type);?>">Upload</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/tracks/'.$releaseId.'?album_type='.$album_type);?>">Tracks</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/stores/'.$releaseId.'?album_type='.$album_type);?>">Stores</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" aria-current="page" href="<?php echo site_url('admin/release/territories/'.$releaseId.'?album_type='.$album_type);?>">Territories</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/date_release/'.$releaseId.'?album_type='.$album_type);?>">Release Date</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/submission/'.$releaseId.'?album_type='.$album_type);?>">Submission</a>
						</li>
					</ul>
					<p class="border-bottom"></p>
				</div>
				<?php
		       	echo error_message();
		       	echo form_open(current_url_query_string(),'id="add_release_territories" role="form"');
				if(is_array($res_territories) && !empty($res_territories)){
					foreach ($res_territories as $tkey => $tval) {
					?>
					<div class="dash_box mt-3 overflow-hidden ref_root_ckbox_parent" >
						<div class="d-flex justify-content-between border-bottom bg-light p-3">
							<p class="fs-3"><?php echo $tval['name'];?></p>
							<p>
								<button type="button" class="btn btn-sm btn-purple sel_all_btn">Check All</button> 
								<button type="button" class="btn btn-sm btn-purple usel_all_btn">Uncheck All</button>
							</p>
						</div>
						<div class="p-4">
							<div class="row gx-0 gy-2">
								<?php
								foreach ($tval['territories'] as $ckey => $cval) {
								$territories_status = (!empty($posted_territories[$cval['country_id']]) ? 1 : 0);
								echo '<div class="col-sm-6 col-lg-4 form-check">
									  <input class="form-check-input xrec_list" type="checkbox" name="release_territories[]" value="'.$cval['country_id'].'" id="ter'.$cval['country_id'].'" '.(($territories_status=='1')? 'checked': '').'>
									  <label class="form-check-label" for="ter'.$cval['country_id'].'"><img src="'.theme_url().'flags/'.$cval['flag'].'" alt=""> '.$cval['name'].'</label>
									</div>';
								}?>
							</div>
						</div>
					</div>
					<?php } 
				}?>
				<div class="mt-3 mb-3 text-center"><?php echo form_error('release_territories[]');?></div>
				<div class="mt-3 mb-3 text-center">
					<input type="hidden" name="album_type" value="<?php echo $album_type;?>">
					<input name="action" type="submit" class="btn btn-purple" value="Submit">
				</div>
				<?php echo form_close(); ?>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php $this->load->view("bottom_application");?>