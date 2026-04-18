<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
	array('heading'=>'Release','url'=>'members/release'),
	array('heading'=>'Dashboard','url'=>'members')
);
$album_type = (int) $this->input->get_post('album_type');
$releaseId = isset($res['release_id']) ? md5($res['release_id']) : (null !== ($segment = $this->uri->segment(4)) ? $segment : null);

$artist_name = get_db_field_value('wl_artists','name',['pdl_id'=>$res['artist_name']]);
$prim_track_types 	= $this->config->item('prim_track_types');
$content_types_arr 	= $this->config->item('content_types_arr');
$moods_arr 			= $this->config->item('moods_arr');
$lang_arr 			= $this->config->item('lang_arr');
if(is_array($res) && !empty($res))
{
	$prim_track_type = set_value('prim_track_type',$res['prim_track_type']);
	$release_type 	= set_value('release_type',$res['release_type']);
	$content_type 	= set_value('content_type',$res['content_type']);
	$is_instrumental = set_value('is_instrumental',$res['is_instrumental']);
	$song_name 		= set_value('song_name',$res['song_name']);
	$crbt_title 	= set_value('crbt_title',$res['crbt_title']);
	$time_for_crbt_cut = set_value('time_for_crbt_cut',$res['time_for_crbt_cut']);
	$track_duration = set_value('track_duration',$res['track_duration']);
	$isrc 			= set_value('isrc',$res['isrc']);
	$is_isrc 		= set_value('is_isrc',$res['is_isrc']);
	$song_mood 		= set_value('song_mood',$res['song_mood']);
	$publisher	 	= set_value('publisher',$res['publisher']);
	$composer 		= set_value('composer',$res['composer']);
	$music_director = set_value('music_director',$res['music_director']);
	$lyrics_lang 	= set_value('lyrics_lang',$res['lyrics_lang']);
	$lyrics 		= set_value('lyrics',$res['lyrics']);
	$lyricist 		= set_value('lyricist',$res['lyricist']);
	$track_title_lang = set_value('track_title_lang',$res['track_title_lang']);
	$track_price 	= set_value('track_price',$res['track_price']);
}
else
{
  	$prim_track_type 	= set_value('prim_track_type');
  	$release_type 		= set_value('release_type');
  	$content_type 		= set_value('content_type');
  	$is_instrumental 	= set_value('is_instrumental');
  	$song_name 			= set_value('song_name');
  	$crbt_title 		= set_value('crbt_title');
  	$time_for_crbt_cut 	= set_value('time_for_crbt_cut');
  	$track_duration 	= set_value('track_duration');
  	$isrc 				= set_value('isrc');
  	$is_isrc 			= set_value('is_isrc');
  	$song_mood 			= set_value('song_mood');
  	$publisher 			= set_value('publisher');
  	$composer 			= set_value('composer');
  	$music_director 	= set_value('music_director');
  	$lyrics_lang 		= set_value('lyrics_lang');
  	$lyrics 			= set_value('lyrics');
  	$lyricist 			= set_value('lyricist');
  	$track_title_lang 	= set_value('track_title_lang');
  	$track_price 		= set_value('track_price');
}?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
.select2-container .select2-selection--single {
    box-sizing: border-box;cursor: pointer;display: block;height:36px;user-select: none;-webkit-user-select: none;
}
</style>
    <meta name="csrf-token-name" content="<?= $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf-token-hash" content="<?= $this->security->get_csrf_hash(); ?>">

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
							<a class="nav-link active" aria-current="page" href="<?= site_url('members/release/tracks/'.$releaseId.'?album_type='.$album_type);?>">Tracks</a>
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
							<a class="nav-link" href="<?= site_url('members/release/submission/'.$releaseId.'?album_type='.$album_type);?>">Submission</a>
						</li>
					</ul>
					<p class="border-bottom"></p>
					<?php
					echo error_message();
					echo form_open(current_url_query_string(),'name="track_frm" id="track_frm" autocomplete="off"');?>
					<div class="row g-3 mt-1">
						<div class="col-lg-2">
							<?php
							if(isset($res['release_banner']) && $res['release_banner']!='' && file_exists(UPLOAD_DIR.'/release/'.$res['release_banner'])){
								$release_banner = base_url().'uploaded_files/release/'.$res['release_banner'];
							}else{
								$release_banner = theme_url().'images/no-img2.jpg';
							}?>
							<div class="user_pic text-center overflow-hidden rounded-3">
              	<span class="align-middle d-table-cell" title="Click to preview image" style="cursor:pointer;">
									<img src="<?= $release_banner;?>" alt="" class="mw-100 mh-100 zx_preview_img">
								</span>
							</div>
							<label class="form-label">Uploaded Cover Picture</label>
						</div>
						<div class="col-lg-6">
							<label for="Phone" class="form-label">Song Title <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
							<input type="text" class="form-control" name="song_name" id="song_name" value="<?= $song_name;?>">
							<?php echo form_error('song_name');?>
						</div>
						<div class="col-lg-4">
							<div class="artist_area">
								<div class="artist_sec">
									<label class="form-label">Primary Artist * </label>
									<input type="text" class="form-control" name="artist_name" id="artist_name" value="<?= $artist_name ?? 'NA' ;?>" disabled>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="featuring_area">
								<div class="feature_sec">
									<label class="form-label">Feature Artist </label>
									<input type="text" class="form-control" name="feature_artist" id="feature_artist" value="<?= $res['feature_artist'] ?? 'NA' ;?>" disabled>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<label class="form-label d-block">Primary Track Type *</label>
							<?php
				            if(is_array($prim_track_types) && !empty($prim_track_types)){
				              foreach($prim_track_types as $ptkey=>$ptt){
				                echo '<input type="radio" name="prim_track_type" class="btn-check" id="add'.$ptkey.'" value="'.$ptkey.'" '.(($prim_track_type==$ptkey)? 'checked' : '').' autocomplete="off">
				                <label class="btn btn-sm btn-outline-dark mb-1" for="add'.$ptkey.'">'.$ptt.'</label>';
				              }
				            }?>
				            <?php echo form_error('prim_track_type');?>
						</div>
						<div class="col-lg-4">
							<label class="form-label d-block">Release Type *</label>
							<input type="radio" class="btn-check" name="release_type" id="Album" value="Album" <?= (($release_type=='Album' || $release_type=='')? 'checked' : '');?> autocomplete="off">
							<label class="btn btn-sm btn-outline-dark" for="Album">Album</label>
							<input type="radio" class="btn-check" name="release_type" id="Film" value="Film" <?= (($release_type=='Film')? 'checked' : '');?> autocomplete="off">
							<label class="btn btn-sm btn-outline-dark" for="Film">Film</label>
							<?php echo form_error('release_type');?>
						</div>
						<div class="col-lg-4">
							<label class="form-label d-block">Content Type *</label>
							<?php
				            if(is_array($content_types_arr) && !empty($content_types_arr)){
				              foreach($content_types_arr as $ctkey=>$ct){
				                echo '<input type="radio" name="content_type" class="btn-check" id="ct'.$ctkey.'" value="'.$ct.'" '.(($content_type==$ct)? 'checked' : '').' autocomplete="off">
				                <label class="btn btn-sm btn-outline-dark mb-1" for="ct'.$ctkey.'">'.$ct.'</label>';
				              }
				            }?>
				            <?php echo form_error('content_type');?>
						</div>
						<div class="col-lg-4">
							<label class="form-label d-block">Instrumental *</label>
							<input type="radio" class="btn-check" name="is_instrumental" id="Yes" value="1" <?= (($is_instrumental==1)? 'checked' : '');?> autocomplete="off">
							<label class="btn btn-sm btn-outline-dark" for="Yes">Yes</label>
							<input type="radio" class="btn-check" name="is_instrumental" id="No" value="0" <?= (($is_instrumental==0)? 'checked' : '');?> autocomplete="off">
							<label class="btn btn-sm btn-outline-dark" for="No">No</label>
							<?php echo form_error('is_instrumental');?>
						</div>
						<div class="col-lg-4">
							<label class="form-label d-block">Ask to Generate an ISRC *</label>
							<input type="radio" class="btn-check" name="is_isrc" id="isrcYes" <?= (($is_isrc==1)? 'checked' : '');?> value="1" autocomplete="off">
							<label class="btn btn-sm btn-outline-dark" for="isrcYes">Yes</label>
							<input type="radio" class="btn-check" name="is_isrc" id="isrcNo" <?= (($is_isrc==0)? 'checked' : '');?> value="0" autocomplete="off">
							<label class="btn btn-sm btn-outline-dark" for="isrcNo">No</label>
						</div>
						<div class="col-lg-4">
							<label class="form-label">ISRC * <img src="<?= theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="ISRC (International Standard Recording Code) is a 12-character alphanumeric code that acts as a unique `digital fingerprint` (e.g.: INAVN2022631)"></label>
							<input type="text" class="form-control" name="isrc" id="isrc" value="<?= $isrc;?>">
							<small>Note: ISRC (e.g.: INAVN2022631) and Unique in data.</small>
							<?php echo form_error('isrc');?>
						</div>
						<div class="col-lg-4">
							<label class="form-label">Mood </label>
							<select class="form-select zx_select" name="song_mood" id="song_mood">
								<option value="">Select</option>
								<?php
								if(is_array($moods_arr) && !empty($moods_arr)){
									foreach($moods_arr as $lkey=>$lval){
										echo '<option value="'.$lval.'" '.(($song_mood==$lval)? 'selected' : '').'>'.$lval.'</option>';
									}
								}?>
							</select>
							<?php echo form_error('song_mood');?>
						</div>
						<div class="col-lg-4">
							<label class="form-label">Price <img src="<?= theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
							<input type="text" class="form-control" name="track_price" id="track_price" value="<?= $track_price;?>">
							<?php echo form_error('track_price');?>
						</div>
						<div class="col-lg-4">
							<label class="form-label">CRBT Title</label>
							<input type="text" class="form-control" name="crbt_title" id="crbt_title" value="<?= $crbt_title;?>">
							<?php echo form_error('crbt_title');?>
						</div>
						<div class="col-lg-4">
							<label class="form-label">Time for CRBT Cut <img src="<?= theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Caller Ring Back Tone (CRBT)"></label>
							<div class="input-group">
								<span class="input-group-text"><b>00:00:</b></span>
								<input type="text" class="form-control" name="time_for_crbt_cut" id="time_for_crbt_cut" min="0" value="<?= $time_for_crbt_cut;?>">
							</div>
							<?php echo form_error('time_for_crbt_cut');?>
						</div>
						<div class="col-lg-4">
							<label class="form-label">Track Duration</label>
							<input type="time" class="form-control" name="track_duration" id="track_duration" value="<?= $track_duration;?>" step="1" placeholder="HH:MM:SS">
							<?php echo form_error('track_duration');?>
						</div>
						<div class="col-lg-4">
							<label class="form-label d-block">Lyricist & Writer *</label>
			            	<input type="text" name="lyricist" id="lyricist" class="form-control" value="<?= $lyricist;?>">
			            	<?php echo form_error('lyricist'); ?>
						</div>
						<div class="col-lg-4">
							<label class="form-label d-block">Composer * </label>
				            <input type="text" name="composer" id="composer" class="form-control" value="<?= $composer;?>" />
				            <?php echo form_error('composer'); ?>
						</div>
						<div class="col-lg-4">
							<label class="form-label">Music Director *</label>
				            <input type="text" name="music_director" id="music_director" class="form-control" value="<?= $music_director;?>"/>
				            <?php echo form_error('music_director'); ?>
						</div>
						<div class="col-lg-4">
							<label class="form-label">Publisher</label>
							<input type="text" class="form-control" name="publisher" id="publisher" value="<?= $publisher;?>">
							<?php echo form_error('publisher');?>
						</div>
						<div class="col-lg-4">
							<label class="form-label">Track Title Language * <img src="<?= theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
							<select class="form-select zx_select" name="track_title_lang" id="track_title_lang">
								<option value="">Select</option>
								<?php
								if(is_array($lang_arr) && !empty($lang_arr)){
									foreach($lang_arr as $lkey=>$lval){
										echo '<option value="'.$lkey.'" '.(($track_title_lang==$lkey)? 'selected' : '').'>'.$lval.'</option>';
									}
								}?>
							</select>
							<?php echo form_error('track_title_lang');?>
						</div>
						<div class="col-lg-4">
							<label class="form-label">Lyrics Language * <img src="<?= theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
							<select class="form-select zx_select" name="lyrics_lang" id="lyrics_lang">
								<option value="">Select</option>
								<?php
								if(is_array($lang_arr) && !empty($lang_arr)){
									foreach($lang_arr as $lkey=>$lval){
										echo '<option value="'.$lkey.'" '.(($lyrics_lang==$lkey)? 'selected' : '').'>'.$lval.'</option>';
									}
								}?>
							</select>
							<?php echo form_error('lyrics_lang');?>
						</div>
						<div class="col-12">
							<label class="form-label">Lyrics  <img src="<?= theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="The written words that accompany the melody of a song, serving as the semantic, sung, or spoken content"></label>
							<textarea name="lyrics" id="lyrics" rows="4" class="form-control"><?= $lyrics;?></textarea>
							<?php echo form_error('lyrics');?>
						</div>
					</div>
					<div class="mt-3">
						<input type="hidden" name="album_type" value="<?= $album_type;?>">
						<input name="action" type="submit" class="btn btn-purple" value="Submit">
					</div>
					<?php echo form_close();?>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
	$(document).ready(function() {
		$('.zx_select').select2();  
	});
	$('.zx_preview_img').click(function(){
	  $('#showImg').attr('src', $(this).attr('src'));
	  new bootstrap.Modal(document.getElementById('imgModal')).show();
	});

$(function(){
    $('input[name="is_isrc"]').change(function(){
        if($(this).val() == 1){
            $("#isrc").prop("readonly", true);
            
            // Get CSRF from hidden input instead of meta tags
            var csrf_token_name = '<?= $this->security->get_csrf_token_name(); ?>';
            var csrf_hash = $('input[name="' + csrf_token_name + '"]').val();
            
            var postData = {};
            postData[csrf_token_name] = csrf_hash;
            
            $.ajax({
                url: "<?= base_url('remote/auto_generate_isrc'); ?>",
                type: "POST",
                data: postData,
                dataType: "json",
                success: function(res){
                    $("#isrc").val(res.isrc);
                },
                error: function(xhr, status, error){
                    console.log("Error: " + error);
                    console.log(xhr.responseText);
                }
            });
        } else {
            $("#isrc").prop("readonly", false).val('');
        }
    });
});

</script>
<?php $this->load->view("bottom_application");?>