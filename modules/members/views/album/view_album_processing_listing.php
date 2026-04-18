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
              echo '<a href="'.site_url('members/album/album_processing').'" class="btn btn-sm btn-outline-danger"><b>Clear</b></a>';
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
                    <th>#</th>
                    <th>Album</th>
                    <th>Title</th>
                    <th>Artist</th>
                    <th>Label</th>
                    <th>Catalogue /ISRC/UPC/EAN</th>
                    <th>Stores</th>
                    <th>Go Live Date</th>
                    <th>Created By</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i=1;
                  $album_status_arr = $this->config->item('album_status_arr');
                  if(is_array($res) && !empty($res)){
                    foreach ($res as $key => $val) {
                      $artist_name = get_db_field_value('wl_artists','name',['pdl_id'=>$val['artist_name']]);
                      $total_territories = count_record ('wl_release_territories',"release_id='".$val['release_id']."'");
                      $total_stores = count_record('wl_release_stores',"release_id='".$val['release_id']."'");
                      ?>
                      <tr class="pr_parent" data-release-id="<?= $val['release_id'];?>">
                        <td><?= $i;?></td>
                        <td>
                          <div class="user_pic text-center overflow-hidden rounded-3">
                            <span class="align-middle d-table-cell">
                              <img src="<?= get_image('release',$val['release_banner'],'230','230','AR');?>" alt="" class="mw-100 mh-100">
                            </span>
                          </div>
                        </td>
                        <td>
                          <p class="fw-semibold purple"><?= $val['release_title'];?></p>
                        </td>
                        <td><?php echo $artist_name;?></td>
                        <td><?php echo $val['label_name'];?></td>
                        <td>
                          Catlog# :<?php echo $val['producer_catalogue'];?>
                          <p class="mt-1">ISRC# :<?= $val['isrc'];?></p>
                          <p class="mt-1">UPC/EAN #:<?= $val['upc_ean'];?></p>
                        </td>
                        <td class="white_space">
                          <p><?= $total_territories;?> terrs</p>
                          <a data-fancybox="" data-type="iframe" data-src="<?= site_url('members/album/view_stored/'.md5($val['release_id']));?>" href="javascript:void(0);" class="pop2 text-primary mt-1 d-block"><?= $total_stores;?> stored</a>
                        </td>
                        <td><?= getDateFormat($val['original_release_date_of_music'],1);?></td>
                        <td>
                          <?= $val['first_name'];?><br>
                          at: <?= getDateFormat($val['created_date'],7);?>
                        </td>
                        <td class="white_space">
                          <a data-fancybox="" data-type="iframe" data-src="<?= site_url('members/release/view_meta_release/'.md5($val['release_id']));?>" href="javascript:void(0);" class="pop2 btn btn-info btn-sm me-2">
                            <img src="<?= theme_url();?>images/eye2.svg" width="18" decoding="async" fetchpriority="high" alt="View">
                          </a>
                          <?php
                          echo '<p class="mt-1">Status</p><p class="mt-1 text-danger fw-semibold">'.$album_status_arr[$val['status']].'</p>';
                          echo ($val['status']=='3' && $val['reason']!='')? '<p class="mt-2 text-secondary"><span class="fw-semibold">Reason:</span> &ldquo; '.$val['reason'].' &rdquo;</p>' : '';
                          ?>
                        </td>
                      </tr>
                      <?php 
                      $i++;
                      } 
                    }
                    else{
                    echo '<tr><td colspan="10"><div class="text-center b mt-4">'.$this->config->item('no_record_found').'</div></td></tr>';
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