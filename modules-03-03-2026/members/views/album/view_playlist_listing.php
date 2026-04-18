<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
  array('heading'=>'Dashboard','url'=>'members')
);
$site_title_text = escape_chars($this->config->item('site_name'));
$posted_keyword = $this->input->get_post('keyword',TRUE);
$posted_keyword = escape_chars($posted_keyword);
$posted_status = $this->input->get_post('status',TRUE);
$posted_status = escape_chars($posted_status); 
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
        <div class="bg-white p-2 mb-2 rounded-3">
          <?php
          if(error_message() !=''){
            echo error_message();
          }?>
          <?php echo form_open("",'id="search_form" method="get" ');?>
          <div class="row g-0">
            <div class="col-sm-7 mb-2 mb-sm-0 pe-sm-5">
              <input type="text" name="keyword" class="form-control fs-7 w-100" value="<?php echo $posted_keyword;?>" placeholder="Search by title">
            </div>
            <div class="col-3 col-sm-3 mt-1">
              <input type="submit" class="btn btn-sm btn-purple me-2" value="Search">
              <?php
              if( $posted_keyword!='') {
              echo '<a href="'.site_url('members/album/playlists').'" class="btn btn-sm btn-outline-danger"><b>Clear</b></a>';
              }?>
            </div>
            <div class="col-2 col-sm-2 mt-1">Show Entries 
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
                    <th>Image</th>
                    <th>Type</th>
                    <th>Title</th>
                    <th>Songs</th>
                    <th>Created At</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i=1;
                  $music_types = $this->config->item('music_types');
                  if(is_array($res) && !empty($res)){
                    foreach ($res as $key => $val) {
                      $songs = get_playlist_songs($val['id']);
                      ?>
                      <tr class="pr_parent" data-label-id="<?php echo $val['id'];?>">
                        <td>
                          <div class="user_pic text-center overflow-hidden rounded-3">
                            <span class="align-middle d-table-cell">
                              <?php
                                echo '<img src="'.get_image('playlist',$val['playlist_img'],100,100,'AR').'" alt="" class="mw-100 mh-100">';
                              ?>
                            </span>
                          </div>
                        </td>
                        <td><?php echo $music_types[$val['music_type']];?></td>
                        <td>
                          <p class="fw-semibold purple"><?php echo $val['title'];?></p>
                        </td>
                        <td>
                          <p class="mt-2 fs-7"><?php echo ($songs) ? $songs : 'NA' ;?></p>
                        </td>
                        <td><?php echo getDateFormat($val['created_date'],1);?></td>
                        <td>
                          <a href="<?php echo site_url("members/album/playlist_delete/".md5($val['id']));?>" class="me-2 confirm_delete" title="Delete">
                            <img src="<?php echo theme_url();?>images/delete.svg" width="19" alt="Delete" class="hand">
                          </a>
                        </td>
                      </tr>
                      <?php 
                      $i++;
                      } 
                    }
                    else{
                    echo '<tr><td colspan="6"><div class="text-center b mt-4">'.$this->config->item('no_record_found').'</div></td></tr>';
                  }?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <?php echo $page_links; ?>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
</div>
<?php $this->load->view("bottom_application");?>