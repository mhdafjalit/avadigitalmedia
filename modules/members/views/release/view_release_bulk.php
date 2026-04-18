<?php 
	$this->load->view('top_application'); 
	$bdcm_array = array(
		array('heading'=>'Release','url'=>'members/release'),
		array('heading'=>'Dashboard','url'=>'members')
	);
$album_type = (int) $this->input->get_post('album_type');
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
				<div class="bulk_uplod_w mt-4 text-center">
					<p class="mt-2 fw-semibold fs-5 purple">Select our Excel meta data template and complete it with your release data</p>
					<div class="row g-3">
						<div class="col-sm-4">
							<p class="mt-3">Download Label to view Ids</p>
							<a href="<?php echo site_url('members/release/download_labels');?>" class="btn btn-primary btn-sm d-inline-block"><img src="<?php echo theme_url();?>images/download2.svg" width="15" alt="Download"> Download</a>
						</div>
						<div class="col-sm-4">
							<p class="mt-3">Download Stores to view Ids</p>
							<a href="<?php echo site_url('admin/release/download_stores');?>" class="btn btn-primary btn-sm d-inline-block"><img src="<?php echo theme_url();?>images/download2.svg" width="15" alt="Download"> Download</a>
						</div>
						<div class="col-sm-4">
							<p class="mt-3">Download Territories to view Ids</p>
							<a href="<?php echo site_url('members/release/download_territories');?>" class="btn btn-primary btn-sm d-inline-block"><img src="<?php echo theme_url();?>images/download2.svg" width="15" alt="Download"> Download</a>
						</div>
						<div class="col-sm-4">
							<p class="mt-3">Download Release Excel Template</p>
							<a href="<?php echo site_url('members/release/download_release_format?album_type='.$album_type);?>" class="btn btn-primary btn-sm d-inline-block"><img src="<?php echo theme_url();?>images/download2.svg" width="15" alt="Download"> Download</a>
						</div>
					</div>
					<div class="bulk_uplod_bx rounded-2 mt-4  shadow">
						<div class="row">
							<div class="col-12 col-sm-6">
								<div class="text-start">
									<p class="fw-semibold text-dark lh-base">Note: Please download release to view the sample sheet as well as uploaded releases, also download other sheets for id's reference. </p>
									<ul class="mt-4 text-danger">
										<li>Download Labels to view Ids</li>
										<li>Download Stores to view Ids</li>
										<li>Download Territories to view Ids</li>
										<li>Aenean commodo ligula eget dolor</li>
										<li>Dolor sit amet, consectetuer</li>
									</ul>
								</div>
							</div>
							<div class="col-12 col-sm-6">
								<?php
						       	echo error_message();
						       	echo form_open_multipart(current_url_query_string(),'id="bulk_release" role="form"');
								?>
								<div class="upload_file_w p-2 rounded-2 mt-2 text-center">
									<input name="bulk_data" id="bulk_data" class="dg_custom_file" type="file"> 
									<span class="attach_btn mt-3">
										<img src="<?php echo theme_url();?>images/upload_icon.svg" width="65" alt="Upload"><a href="javascript:void(0)" class="attach_desg d-block py-2 fst-italic">Click Here To Select File</a>
									</span>
									<b class="file_url db pt-2 fs16 text-center">&nbsp;</b>
								</div>
								<?php echo form_error('bulk_data'); ?>
								<div class="mt-3 text-center">
									<input name="action" type="submit" class="btn btn-purple" value="Submit">
								</div>
								<?php echo form_close(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php $this->load->view("bottom_application");?>