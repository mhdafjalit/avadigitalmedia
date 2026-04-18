<?php
$this->load->view('top_application',['has_header'=>false,'ws_page'=>'store_pg','is_popup'=>true,'has_body_style'=>'padding:0']);
$album_type = (int) $this->input->get_post('album_type');
$releaseId = isset($res['release_id']) ? md5($res['release_id']) : (null !== ($segment = $this->uri->segment(4)) ? $segment : null);
$prim_track_types 	= $this->config->item('prim_track_types');
$lang_arr 			= $this->config->item('lang_arr');
$artist_name = get_db_field_value('wl_artists','name',['pdl_id'=>$res['artist_name']]);
$total_territories = count_record ('wl_release_territories',"release_id='".$res['release_id']."'");
$total_release_stores = count_record ('wl_release_stores',"release_id='".$res['release_id']."'");
?>
<div class="p-3 bg-light text-center border-bottom">
  <h1><?= $heading_title;?></h1>
</div>
<div class="pt-4 pb-4 ps-2 pe-2">
	<div class="row gx-2 gy-6 text-center">
		<!-- Album Overview -->
		<div class="dash_box p-4">
			<p class="fs-5 fw-bold p-3 border-bottom"><?php echo $res['release_title'];?></p>
			<div class="mt-4 d-flex">
				<div title="Click to preview image" style="cursor:pointer;">
					<?php
					if($res['release_banner']!='' && file_exists(UPLOAD_DIR.'/release/'.$res['release_banner'])){
						$release_banner = base_url().'uploaded_files/release/'.$res['release_banner'];
						echo '<img src="'.$release_banner.'" width="100" height="100" alt="" class="rounded-3 zx_preview_img">';
					}?>
				</div>
				<div class="row">
					<div class="col-sm-6">
							<b class="fs-7 fw-semibold"><span class="p_symb">P</span> Line</b>
							<p class="mb-2"><?= $res['p_line'] ?? 'NA';?></p>
						</div>
						<div class="col-sm-6">
							<b class="fs-7 fw-semibold">&copy; Line</b>
							<p class="mb-2"><?= $res['c_line'] ?? 'NA';?></p>
						</div>
						<div class="col-sm-6">
							<b class="fs-7 fw-semibold">Primary Artist</b>
							<p class="mb-2"><?= ($artist_name) ? $artist_name : 'NA' ;?></p>
						</div>
						<div class="col-sm-6">
							<b class="fs-7 fw-semibold">Feature Artist </b>
							<p class="mb-2"><?= !empty($res['feature_artist'])? $res['feature_artist'] : 'NA' ;?></p>
						</div>
						<div class="col-sm-6">
							<b class="fs-7 fw-semibold">UPC/EAN </b>
							<p class="mb-2"><?= $res['upc_ean'] ?? 'NA';?></p>
						</div>
						<div class="col-sm-6">
							<b class="fs-7 fw-semibold">Genre </b>
							<p class="mb-2"><?= !empty($res['genre'])? $res['genre'] : 'NA' ;?></p>
						</div>
						<div class="col-sm-6">
							<b class="fs-7 fw-semibold">Sub Genre  </b>
							<p class="mb-2"><?= !empty($res['sub_genre'])? $res['sub_genre'] : 'NA' ;?></p>
						</div>
						<div class="col-sm-6">
							<b class="fs-7 fw-semibold">Label Name </b>
							<p class="mb-2"><?= !empty($res['label_name'])? $res['label_name'] : 'NA' ;?></p>
						</div>
				</div>
			</div>
			<div class="mt-3 border rounded-3">
				<p class="fs-5 fw-medium bg-light p-3 rounded-3 text-uppercase">Track</p>
				<div class="p-3">
					<div class="row gx-0 gy-3">
						<div class="col-sm-4">
							<b class="fs-7 fw-semibold">Song Title</b><p><?= $res['song_name'];?></p>
						</div>
						<div class="col-sm-4">
							<b class="fs-7 fw-semibold">Album Type</b>
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
							<b class="fs-7 fw-semibold">Track Language</b>
							<p><?= $lang_arr[$res['lyrics_lang']] ?? 'NA';?></p>
						</div>
						<div class="col-sm-4">
							<b class="fs-7 fw-semibold">Instrumental</b>
							<p><?= ($res['is_instrumental']>0) ? 'Yes' : 'No';?></p>
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
			<!-- Release Date Section -->
			<div class="mt-3 border rounded-3">
				<p class="fs-5 fw-medium bg-light p-3 rounded-3 text-uppercase">Release Date</p>
				<div class="p-3">
					<div class="row gx-0 gy-3">
						<div class="col-sm-6">
							<b class="fs-7 fw-semibold">Release Date </b>
							<p><?= ($res['go_live_date']) ? getDateFormat($res['go_live_date'],1) : 'NA';?></p>
						</div>
						<div class="col-sm-6">
							<b class="fs-7 fw-semibold">Original Music Release Date </b>
							<p><?= ($res['original_release_date_of_music']) ? getDateFormat($res['original_release_date_of_music'],1) : 'NA';?> </p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="imgModal">
  <div class="modal-dialog modal-xl modal-dialog-centered">
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
<?php $this->load->view("bottom_application",array('has_footer'=>false,'ws_page'=>'store_pg','is_popup'=>true));?>