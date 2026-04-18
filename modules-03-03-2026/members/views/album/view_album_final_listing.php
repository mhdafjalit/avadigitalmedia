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
              <input type="text" name="keyword" class="form-control fs-7 w-100" value="<?php echo $posted_keyword;?>" placeholder="Search by title, label, post by">
            </div>
            <div class="col-3 col-sm-3 mt-1">
              <input type="submit" class="btn btn-sm btn-purple me-2" value="Search">
              <?php
              if( $posted_keyword!='') {
              echo '<a href="'.site_url('members/album').'" class="btn btn-sm btn-outline-danger"><b>Clear</b></a>';
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
                    <th></th>
                    <th></th>
                    <th>Album</th>
                    <th>Title</th>
                    <th>Created By</th>
                    <th>Label</th>
                    <th>Catalogue Number</th>
                    <th>Stores</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i=1;
                  $album_status_arr = $this->config->item('album_status_arr');
                  if(is_array($res) && !empty($res)){
                    foreach ($res as $key => $val) {
                      $prim_artists     = get_prim_artists($val['release_id']);
                      $total_territories = count_record ('wl_release_territories',"release_id='".$val['release_id']."'");
                      $total_stores = count_record('wl_release_stores',"release_id='".$val['release_id']."'");
                      ?>
                      <tr class="pr_parent" data-label-id="<?php echo $val['release_id'];?>">
                        <td><img src="<?php echo theme_url();?>images/record.svg" alt=""></td>
                        <td><img src="<?php echo theme_url();?>images/industry.svg" alt=""></td>
                        <td>
                          <div class="user_pic text-center overflow-hidden rounded-3">
                            <span class="align-middle d-table-cell">
                              <?php
                              if($val['release_banner']!='' && file_exists(UPLOAD_DIR.'/release/'.$val['release_banner'])){
                                $release_banner = base_url().'uploaded_files/release/'.$val['release_banner'];
                                echo '<img src="'.$release_banner.'" alt="" class="mw-100 mh-100">';
                              }?>
                            </span>
                          </div>
                        </td>
                        <td>
                          <p class="fw-semibold purple"><?php echo $val['release_title'];?></p>
                          <p class="mt-2 fs-7"><?php echo ($prim_artists) ? $prim_artists : 'NA' ;?></p>
                        </td>
                        <td><?php echo $val['first_name'];?></td>
                        <td>
                          <?php echo $val['channel_name'];?>
                          <small class="d-block mt-1"><?php echo getDateFormat($val['label_date'],1);?></small>
                        </td>
                        <td>
                          Cat# :<?php echo $val['producer_catalogue'];?>
                          <p class="mt-1">ISRC# :<?php echo $val['isrc'];?></p>
                          <p class="mt-1">Release Id:<?php echo $val['release_id'];?></p>
                        </td>
                        <td class="white_space">
                          <p><?php echo $total_territories;?> terrs</p>
                          <a data-fancybox="" data-type="iframe" data-src="<?php echo site_url('members/album/view_stored/'.md5($val['release_id']));?>" href="javascript:void(0);" class="pop2 text-primary mt-1 d-block"><?php echo $total_stores;?> stored</a>
                        </td>
                        <td class="white_space">
                          <a href="<?php echo site_url("members/release/view_release/".md5($val['release_id']));?>" class="me-2" title="View"><img src="<?php echo theme_url();?>images/eye2.svg" width="18" decoding="async" fetchpriority="high" alt="View"></a> 
                          <a href="<?php echo site_url('members/album/album_status/'.md5($val['release_id']).'?al_status=takedown')?>" class="me-2" title="Takedown Release"><img src="<?php echo theme_url();?>images/money.svg" width="21" decoding="async" fetchpriority="high" alt="Takedown Release"></a>
                          <?php
                          echo '<p class="mt-1">Status</p><p class="mt-1 text-danger fw-semibold">'.$album_status_arr[$val['status']].'</p>';?>
                        </td>
                      </tr>
                      <?php 
                      $i++;
                      } 
                    }
                    else{
                    echo '<tr><td colspan="9"><div class="text-center b mt-4">'.$this->config->item('no_record_found').'</div></td></tr>';
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