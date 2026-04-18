<?php 
	$this->load->view('top_application'); 
	$bdcm_array = array(
		array('heading'=>'Release','url'=>'members/release'),
		array('heading'=>'Dashboard','url'=>'members')
	);
$album_type = (int) $this->input->get_post('album_type');
$releaseId = isset($res['release_id']) ? md5($res['release_id']) : (null !== ($segment = $this->uri->segment(4)) ? $segment : null);
$prim_track_types 	= $this->config->item('prim_track_types');
$lang_arr 			= $this->config->item('lang_arr');
$artist_name = get_db_field_value('wl_artists','name',['pdl_id'=>$res['artist_name']]);
$total_territories = count_record ('wl_release_territories',"release_id='".$res['release_id']."'");
$total_release_stores = count_record ('wl_release_stores',"release_id='".$res['release_id']."'");
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
							<a class="nav-link" href="<?= site_url('members/release/new_release/'.$releaseId.'?album_type='.$album_type);?>">Release Information</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('members/release/upload_release/'.$releaseId.'?album_type='.$album_type);?>">Upload</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('members/release/tracks/'.$releaseId.'?album_type='.$album_type);?>">Tracks</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('members/release/stores/'.$releaseId.'?album_type='.$album_type);?>">Stores</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('members/release/territories/'.$releaseId.'?album_type='.$album_type);?>">Territories</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('members/release/date_release/'.$releaseId.'?album_type='.$album_type);?>">Release Date</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" aria-current="page" href="<?= site_url('members/release/submission/'.$releaseId.'?album_type='.$album_type);?>">Submission</a>
						</li>
					</ul>
					<p class="border-bottom"></p>
					<?php
			       	echo error_message();
			       	echo form_open(current_url_query_string(),'id="release_submission" role="form"');
					?>
					<div class="mt-4 border rounded-3">
						<p class="fs-5 fw-medium bg-light p-3 rounded-3 text-uppercase">Release Information</p>
						<div class="p-3">
							<div class="row gx-0 gy-3">
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Album Title</b><p><?= $res['release_title'];?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold"><span class="p_symb">P</span> Line</b>
									<p><?= $res['p_line'] ?? 'NA';?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Version/Subtitle</b>
									<p><?= $res['version'] ?? 'NA';?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">&copy; Line</b>
									<p><?= $res['c_line'] ?? 'NA';?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Primary Artist</b>
									<p><?= ($artist_name) ? $artist_name : 'NA' ;?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Feature Artist </b>
									<p><?= !empty($res['feature_artist'])? $res['feature_artist'] : 'NA' ;?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Production Year </b><p><?= $res['production_year'] ?? 'NA';?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">UPC/EAN </b><p><?= $res['upc_ean'] ?? 'NA';?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Genre </b>
									<p><?= !empty($res['genre'])? $res['genre'] : 'NA' ;?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Sub Genre  </b>
									<p><?= !empty($res['sub_genre'])? $res['sub_genre'] : 'NA' ;?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Label Name </b>
									<p><?= !empty($res['label_name'])? $res['label_name'] : 'NA' ;?></p>
								</div>
							</div>
						</div>
					</div>
					<div class="mt-4 border rounded-3">
						<p class="fs-5 fw-medium bg-light p-3 rounded-3 text-uppercase">Track</p>
						<div class="p-3">
							<div class="row gx-0 gy-3">
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Song Title</b><p><?= $res['release_title'];?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Primary Track Type </b>
									<p><?= $prim_track_types[$res['prim_track_type']] ?? 'NA';?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Release Type</b>
									<p><?= $res['release_type'];?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Content Type</b>
									<p><?= $res['content_type'];?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">ISRC</b>
									<p><?= $res['isrc'];?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Song Mood</b>
									<p><?= $res['song_mood'];?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">CRBT Title</b>
									<p><?= $res['crbt_title'] ?? 'NA';?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Time for CRBT Cut</b>
									<p><?= !empty($res['lyricist'])? $res['time_for_crbt_cut'].'secs' : 'NA';?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Track Duration</b>
									<p><?= $res['track_duration'] ?? 'NA';?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Track Title Language</b>
									<p><?= $lang_arr[$res['track_title_lang']] ?? 'NA';?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Lyricist & Writer </b>
									<p><?= !empty($res['lyricist'])? $res['lyricist'] : 'NA' ;?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Composer </b>
									<p><?= !empty($res['composer'])? $res['composer'] : 'NA' ;?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Music Director </b>
									<p><?= !empty($res['music_director'])? $res['music_director'] : 'NA' ;?></p>
								</div>
								<div class="col-sm-4">
									<b class="fs-7 fw-semibold">Publisher </b>
									<p><?= !empty($res['publisher'])? $res['publisher'] : 'NA' ;?></p>
								</div>
							</div>
						</div>
					</div>
					<div class="mt-4 border rounded-3">
						<p class="fs-5 fw-medium bg-light p-3 rounded-3 text-uppercase">Price</p>
						<div class="p-3">
							<div class="row gx-0 gy-3">
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Main Price</b>
									<p><?= display_price($res['track_price']);?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Producer Catalogue Number </b>
									<p><?= $res['producer_catalogue'];?></p>
								</div>
							</div>
						</div>
					</div>
					<div class="mt-4 border rounded-3">
						<p class="fs-5 fw-medium bg-light p-3 rounded-3 text-uppercase">Territories</p>
						<div class="p-3">
							<div class="row gx-0 gy-3">
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Your Release is Authorized in</b>
									<p><?= $total_territories;?> Territories</p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Your Release Stores</b>
									<p><?= $total_release_stores;?> Stores</p>
								</div>
							</div>
						</div>
					</div>
					<div class="mt-4 border rounded-3">
						<p class="fs-5 fw-medium bg-light p-3 rounded-3 text-uppercase">Release Date</p>
						<div class="p-3">
							<div class="row gx-0 gy-3">
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Physical/Live Release Date </b>
									<p><?= ($res['go_live_date']) ? getDateFormat($res['go_live_date'],1) : 'NA';?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Original Music Release Date </b>
									<p><?= ($res['original_release_date_of_music']) ? getDateFormat($res['original_release_date_of_music'],1) : 'NA';?> </p>
								</div>
                                
                                <?php /*
                                <div class="col-sm-3">
									<b class="fs-7 fw-semibold">State </b>
									<p><?php echo ($res['main_release_state']) ? $res['main_release_state'] : 'NA';?> </p>
								</div> */?>
							</div>
						</div>
					</div>
					<div class="mt-4 border rounded-3">
						<p class="fs-5 fw-medium bg-light p-3 rounded-3 text-uppercase">Assets</p>
						<div class="p-3">
							<div class="table-responsive">
								<div class="scrollbar style-4">
									<table class="table table-bordered mb-0 acc_table table-striped">
									 	<thead>
											<tr>
												<th>#</th>
												<th></th>
												<th>Asset(s)</th>
												<th>Artist(s)</th>
												<th>ISRC</th>
												<th>Price</th>
												<th>Edit all ISRC codes</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>1</td>
												<td>
													<div class="user_pic text-center overflow-hidden rounded-3">
							                            <span class="align-middle d-table-cell" title="Click to preview image" style="cursor:pointer;">
							                              <?php
							                              if($res['release_banner']!='' && file_exists(UPLOAD_DIR.'/release/'.$res['release_banner'])){
							                                $release_banner = base_url().'uploaded_files/release/'.$res['release_banner'];
							                                echo '<img src="'.$release_banner.'" alt="" class="mw-100 mh-100 zx_preview_img">';
							                              }?>
							                            </span>
						                          	</div>
												</td>
												<td><?= $res['release_title'];?></td>
												<td>
													<p class="fw-bold"><?= $res['song_name'];?></p>
													<p><?= ($artist_name) ? $artist_name : 'NA' ;?></p>
												</td>
												<td><?= $res['isrc'];?></td>
												<td><?= display_price($res['track_price']);?></td>
												<td>
													<a href="<?php echo site_url('members/release/tracks/'.$releaseId.'?album_type='.$album_type);?>" class="btn btn-sm btn-purple <?php echo ($res['status']!='1')? ''  :'overlay_enable';?>">Details</a>
												</td>
												<td class="white_space">
													<?php 
													if($res['release_song']!='' && file_exists(UPLOAD_DIR.'/release/songs/'.$res['release_song'])){?>
										              	<a href="<?php echo base_url().'uploaded_files/release/songs/'.$res['release_song'];?>" title="Click to download" download> <img src="<?php echo theme_url();?>images/download.svg" alt="Download"></a>
									              		<?php 
									              	}
									              	$album_status_arr = $this->config->item('album_status_arr');
                          							echo '<p class="mt-1">Status : <span class="mt-1 text-danger fw-semibold">'.$album_status_arr[$res['status']].'</span></p>';
									              	?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<p class="mt-3">
						<button type="submit" name="btn_sbt" class="btn btn-purple" value="final">Submit my Release</button>
					</p>
					<?php echo form_close(); ?>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<div class="modal fade" id="imgModal">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-transparent border-0 text-center">
      <img id="showImg" class="img-fluid">
    </div>
  </div>
</div>
<script>
$('.zx_preview_img').click(function(){
  $('#showImg').attr('src', $(this).attr('src'));
  new bootstrap.Modal(document.getElementById('imgModal')).show();
});
</script>
<?php $this->load->view("bottom_application");?>