<?php
$this->load->library('AudioLib');
$playlist_songs_arr = $this->session->userdata('playlist_songs_arr');
?>
<div class="p-3 border-bottom fw-semibold fs-7 text-uppercase d-flex justify-content-between">
	<p>Added Songs</p>
	<img src="<?php echo theme_url();?>images/close.svg" alt="Close" onClick="$('.playlist_box').addClass('dn');" class="hand">
</div>
<div id="container_playlist">
<?php
if(is_array($playlist_songs_arr) && !empty($playlist_songs_arr)){
	foreach($playlist_songs_arr as $key=>$val){
		$duration = '00:00:00';
    	if($val['song_file']!='' && file_exists(UPLOAD_DIR.'/release/songs/'.$val['song_file'])){
        	$file_path = UPLOAD_DIR.'/release/songs/'.$val['song_file'];
        	$duration = $this->audiolib->get_media_duration($file_path);
        }?>
		<div class="p-3 border-bottom rm_parent_playlist" data-release-id="<?php echo $val['release_id'];?>">
			<p class="fs-9 fw-semibold"><?php echo $val['song'];?></p>
			<p class="float-start fs-9 mt-1"><?php echo $duration; ?></p>
			<p class="float-end">
				<a href="javascript:void(0);" title="Delete" class="rm_elements"><img src="<?php echo theme_url();?>images/close.svg" alt="Delete"></a>
			</p>
			<p class="clearfix"></p>
		</div>
		<?php 
	}
}else{
	echo '<div class="p-3 border-bottom">No item has been added yet</div>';
}?>
</div>