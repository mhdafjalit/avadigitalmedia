<?php 
	$this->load->view('top_application'); 
	$bdcm_array = array(
		array('heading'=>'Release','url'=>'admin/release'),
		array('heading'=>'Dashboard','url'=>'admin')
	);
$album_type = (int) $this->input->get_post('album_type');
$releaseId = isset($res['release_id']) ? md5($res['release_id']) : (null !== ($segment = $this->uri->segment(4)) ? $segment : null);
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
							<a class="nav-link active" aria-current="page" href="<?php echo site_url('admin/release/upload_release/'.$releaseId.'?album_type='.$album_type);?>">Upload</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/tracks/'.$releaseId.'?album_type='.$album_type);?>">Tracks</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/stores/'.$releaseId.'?album_type='.$album_type);?>">Stores</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/territories/'.$releaseId.'?album_type='.$album_type);?>">Territories</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/date_release/'.$releaseId.'?album_type='.$album_type);?>">Release Date</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/submission/'.$releaseId.'?album_type='.$album_type);?>">Submission</a>
						</li>
					</ul>
					<p class="border-bottom"></p>
					<ul class="nav nav-tabs ms-0 mt-4 text-uppercase">
					  	<li class="nav-item">
						  	<a class="nav-link active" href="javascript:void(0);">Uploader</a>
					  	</li>
					</ul>
					<?php
			       	echo error_message();
			       	echo form_open_multipart(current_url_query_string(),'id="add_release_song_file" role="form"');
					?>
					<h1 class="float-sm-start mt-4">Uploader</h1>
					<div class="float-sm-end mt-3">
						<div class="row g-3">
							<div class="col-sm-6">
								<input type="file" name="release_song" class="form-control">
								<?php 
								if($res['release_song']!='' && file_exists(UPLOAD_DIR.'/release/songs/'.$res['release_song'])){?>
					              	<p class="mt-2"><a href="<?php echo base_url().'uploaded_files/release/songs/'.$res['release_song'];?>" class="text-primary me-2 b" title="Click to download" download> View</a> | 
		      						<input type="checkbox" name="release_song_delete" value="Y" /> Delete </p>
				              		<?php 
				              	}?>
				              	[<small class="text-danger">File size should not be more then 15 MB</small>]
								<?php echo form_error('release_song');?>
							</div>
							<div class="col-sm-6">
								<input type="hidden" name="album_type" value="<?php echo $album_type;?>">
								<input name="action" type="submit" class="btn btn-purple text-uppercase" value="Click here to upload">
							</div>
						</div>
					</div>
					<?php echo form_close(); ?>
					<p class="clearfix"></p>
					<div class="mt-3">
						If the uploader doesn't appear, please allow pop-ups from this website in your browser settings
						<br>
						You are able to leave this page and to navigate through your dashboard while the uploader is running, without affecting the upload.
						<br><br>
						You can upload the following format:
						<ul>
							<li>.wav </li>
							<li>.mp3</li>
						</ul>
						<br>
						Please make sure to avoid using special characters such as & / % # etc. when naming your audio files. Files might not upload correctly otherwise.
						<br><br>
						<p class="text-danger">You will be able to match your audio files with your tracks during the 'submission' step.</p>
						<br>
						Please use the new 'IMPORT FTP' tab to upload your Apple HD videos. You cannot use the Uploader to upload your videos. But you can still use it to upload audio files.
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php $this->load->view("bottom_application");?>