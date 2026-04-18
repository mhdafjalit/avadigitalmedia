<?php 
	$this->load->view('top_application'); 
	$bdcm_array = array(
		array('heading'=>'Release','url'=>'admin/release'),
		array('heading'=>'Dashboard','url'=>'admin')
	);
$music_types 	= $this->config->item('music_types');
$posted_keyword = $this->input->get_post('keyword',TRUE);
$posted_keyword = escape_chars($posted_keyword);
$album_type = (int) $this->input->get_post('album_type');
$playlist_songs_arr = $this->session->userdata('playlist_songs_arr');
if(empty($playlist_songs_arr)){
	$playlist_songs_arr=[];
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
	    		<?php 
	    		echo error_message();
	    		echo form_open_multipart(current_url_query_string(),'id="playlist_form" role="form" ');
				echo '<input type="hidden" name="album_type" value="'.$album_type.'">';
				echo '<input type="hidden" name="action" value="Y">';
				?>
				<div class="dash_box p-4 mb-3">
					<div class="row g-3">
						<div class="col-sm-6 col-lg-5">
							<label for="title" class="form-label">Title for this Playlist *</label>
							<input type="text" class="form-control" name="title" id="title">
							<?php echo form_error('title');?>
						</div>
						<div class="col-sm-6 col-lg-5">
							<label for="playlist_img" class="form-label">Upload Image *</label>
							<input type="file" class="form-control" name="playlist_img" id="playlist_img">
							<?php echo form_error('playlist_img');?>
						</div>
					</div>
				</div>
				<div class="mt-3 mb-3">
					<?php
					$posted_music_type = set_value('music_type');
          if(is_array($music_types) && !empty($music_types)){
            foreach($music_types as $mtkey=>$mt){
              echo '<input type="radio" name="music_type" class="btn-check" id="add'.$mtkey.'" value="'.$mtkey.'" '.(($posted_music_type==$mtkey)? 'checked' : '').' autocomplete="off">
              <label class="btn btn-outline-dark" for="add'.$mtkey.'">'.$mt.'</label>';
            }
          }?>
          <?php echo form_error('music_type');?>
				</div>
				<?php echo form_error('playlist_song');?>
				<?php echo form_close();?>
				<div class="mb-3">
					<?php echo form_open("",'id="search_form" method="get" ');?>
					<div class="row g-0">
						<div class="col-sm-6 mb-2 mb-sm-0 pe-sm-5">
							<input type="text" name="keyword" class="form-control fs-7 w-100" value="<?php echo $posted_keyword;?>" placeholder="Search by title, primary artist">
						</div>
						<div class="col-2 col-sm-2 mt-1">
			              <input type="submit" class="btn btn-sm btn-purple me-2" value="Search">
			              <?php
			              if( $posted_keyword!='') {
			              echo '<a href="'.site_url('admin/release/create_playlist').'" class="btn btn-sm btn-outline-danger"><b>Clear</b></a>';
			              }?>
			            </div>
						<div class="col-7 col-sm-4 mt-1">Show Entries 
							<?php echo front_record_per_page('per_page','per_page');?>
						</div>
					</div>
					<?php echo form_close();?>
				</div>
				<div class="white_bx overflow-hidden">       
					<div class="table-responsive">
						<div class="scrollbar style-4">
							<table class="table table-bordered mb-0 acc_table table-striped">
							 	<thead>
									<tr>
										<th>Title</th>
										<th>Primary Artist</th>
										<th>Duration</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$this->load->library('AudioLib');
									if(is_array($res) && !empty($res)){
                    foreach ($res as $key => $val) {
                    	$duration = '00:00:00';
                    	if($val['album_media']!='' && file_exists(UPLOAD_DIR.'/release/songs/'.$val['album_media'])){
	                    	$file_path = UPLOAD_DIR.'/release/songs/'.$val['album_media'];
	                    	$duration = $this->audiolib->get_media_duration($file_path);
	                    }
                      $prim_artists = get_prim_artists($val['artist_id']);
                      $loop_is_added =  isset($playlist_songs_arr['zx_'.$val['id']]) ? 1 : 0;
                      ?>
											<tr class="pr_parent" data-release-id="<?php echo $val['id'];?>">
												<td><?php echo $val['album_name'];?></td>
												<td><?php echo ($prim_artists) ? $prim_artists : 'NA'; ?></td>
												<td><?php echo $duration; ?></td>
												<td><button type="button" class="btn btn-sm btn<?php echo $loop_is_added ? '-dark added' : '-purple add_song_list';?>"><?php echo $loop_is_added ? 'Added' : 'Add to Playlist';?></button></td>
											</tr>
											<?php
                      	} 
                    }else{
	                    echo '<tr><td colspan="4"><div class="text-center b mt-4">'.$this->config->item('no_record_found').'</div></td></tr>';
                  	}?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<?php echo $page_links; ?>
				<p class="mt-3">
					<button type="button" id="playlist_sub_btn" class="btn btn-purple">Create a Release</button>
				</p>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<div class="position-fixed z-3 bg-white border border-danger border-2 rounded-3 shadow dn playlist_box">
<?php $this->load->view("view_added_playlist");?>
</div>
<script>
$(document).ready(function() {
	var playlist_box_obj = $('.playlist_box');
    $('#playlist_sub_btn').click(function() {
        $('#playlist_form').submit();
    });
    $('.add_song_list,.added').on('click', function(e) {
      e.preventDefault();
      var cobj = $(this);
      if(cobj.hasClass('added')){
      	playlist_box_obj.removeClass('dn');
      	return false;
      }
      var parent_node_obj = cobj.closest('.pr_parent');
      if(parent_node_obj.hasClass('overlay_enable')){
      	return false;
      }
      var release_id = parent_node_obj.data('release-id');
      parent_node_obj.addClass('overlay_enable');
        $.ajax({
          url: '<?php echo site_url('admin/release/add_playlist_song');?>',
          type: 'POST',
          data: {'release_id':release_id,btn_sbt:'Y'},
          headers: { XRSP: 'json' },
          dataType: 'json'
        }).done(function(data) {
            if (data.status == '1') {
             playlist_box_obj.html(data.view_playlist).removeClass('dn');
             cobj.removeClass('btn-purple add_song_list').addClass('btn-dark added').text('Added');
            }else if (data.status == '2') {
              playlist_box_obj.html(data.view_playlist).removeClass('dn');
              cobj.removeClass('btn-purple add_song_list').addClass('btn-dark added').text('Added');
            }else {
              
            }
        }).always(function() {
        	parent_node_obj.removeClass('overlay_enable');
      	});
    });
    playlist_box_obj.on('click','.rm_elements', function(e) {
    	 e.preventDefault();
      	var cobj = $(this);
      	var parent_node_obj = cobj.closest('.rm_parent_playlist');
      	if(parent_node_obj.hasClass('overlay_enable')){
      	return false;
      }
      var release_id = parent_node_obj.data('release-id');
      var crs_btn_id = $('.pr_parent[data-release-id="'+release_id+'"]').find('.added');
      parent_node_obj.addClass('overlay_enable');
        $.ajax({
          url: '<?php echo site_url('admin/release/remove_playlist_song');?>',
          type: 'POST',
          data: {'release_id':release_id,btn_sbt:'Y'},
          headers: { XRSP: 'json' },
          dataType: 'json'
        }).done(function(data) {
            if (data.status == '1') {
             playlist_box_obj.html(data.view_playlist).removeClass('dn');
             crs_btn_id.removeClass('btn-dark added').addClass('btn-purple add_song_list').text('Add to Playlist');
            }else if(data.status == '2'){
            	 playlist_box_obj.html(data.view_playlist).removeClass('dn');
            	 crs_btn_id.removeClass('btn-dark added').addClass('btn-purple add_song_list').text('Add to Playlist');
            }else {
              
            }
       }).always(function() {
        parent_node_obj.removeClass('overlay_enable');
      });
    });
});
</script>
<?php $this->load->view("bottom_application");?>