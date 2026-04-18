<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
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
		        <?php echo navigation_breadcrumb($heading_title,$bdcm_array); ?>
	      	</div>
	      	<p class="clearfix"></p>
			<div class="main-content-inner text-center">
				<p class="fs-3 fw-normal">Create Your Releases</p>
				<p class="text-center fs-5 fw-bold mt-4">What you want to Release</p>
				<?php
				$album_types = $this->config->item('album_types');
				if(is_array($album_types) && !empty($album_types)){?>
				<div class="album_tab m-0">
					<ul class="nav nav-pills justify-content-center mb-3" id="pills-tab" role="tablist">
						<?php
						foreach ($album_types as $key => $val) {
							echo '<li class="nav-item" role="presentation">
								<button class="nav-link '.(($key==1)? ' active':'').'" id="pills-'.$key.'-tab" data-bs-toggle="pill" data-bs-target="#pills-'.$key.'" type="button" role="tab" aria-controls="pills-'.$key.'" aria-selected="true" '.(($key==2)? 'disabled="disabled"':'').'><img src="'.theme_url().'images/'.(($key==1)? 'audio':'video').'-tab-ico.svg" alt=""> '.$val.' Album</button>
							</li>';
						}?>
					</ul>
				</div>
				<div class="tab-content" id="pills-tabContent">
					<?php
					foreach ($album_types as $key => $val) {
						echo '<div class="tab-pane fade '.(($key==1)? ' show active':'').'" id="pills-'.$key.'" role="tabpanel" aria-labelledby="pills-'.$key.'-tab" tabindex="0">
							<div class="row g-0">
								<div class="col-sm-4 mt-4">
									<div class="release_box bg-white position-relative shadow m-auto">
										<div class="release_pic text-center overflow-hidden m-auto">
											<span class="align-middle d-table-cell">
												<a href="'.site_url('admin/metas/add').'">
													<img src="'.theme_url().'images/release-img1.jpg" alt="" class="mw-100 mh-100">
												</a>
											</span>
										</div>
										<p class="release_name trans_eff">
											<a href="'.site_url('admin/metas/add').'" class="text-white">Single Release '.$val.'</a>
										</p>
									</div>
								</div>
								<div class="col-sm-4 mt-4">
									<div class="release_box bg-white position-relative shadow m-auto">
										<div class="release_pic text-center overflow-hidden m-auto">
											<span class="align-middle d-table-cell">
												<a href="'.site_url('members/release/bulk_release?album_type='.$key).'">
													<img src="'.theme_url().'images/release-img2.jpg" alt="" class="mw-100 mh-100">
												</a>
											</span>
										</div>
										<p class="release_name trans_eff">
											<a href="'.site_url('members/release/bulk_release?album_type='.$key).'" class="text-white">Bulk Release '.$val.'</a>
										</p>
									</div>
								</div>
								<div class="col-sm-4 mt-4">
									<div class="release_box bg-white position-relative shadow m-auto">
										<div class="release_pic text-center overflow-hidden m-auto">
											<span class="align-middle d-table-cell">
												<a href="'.site_url('members/release/create_playlist?album_type='.$key).'">
													<img src="'.theme_url().'images/release-img3.jpg" alt="" class="mw-100 mh-100">
												</a>
											</span>
										</div>
										<p class="release_name trans_eff">
											<a href="'.site_url('members/release/create_playlist?album_type='.$key).'" class="text-white">Create your Own Playlist</a>
											<a href="'.site_url('admin/album/playlists?album_type='.$key).'" class="text-white fs-11">View Playlist</a>
										</p>
									</div>
								</div>
							</div>
						</div>';
					}?>
				</div>
				<?php } ?>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php $this->load->view("bottom_application");?>