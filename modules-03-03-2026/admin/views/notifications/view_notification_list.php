<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
  array('heading'=>'Dashboard','url'=>'admin')
);
$posted_keyword = $this->input->get_post('keyword',TRUE);
$posted_keyword = escape_chars($posted_keyword);
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
        <div class="mb-3">
          <?php
          if(error_message() !=''){
            echo error_message();
          }?>
          <?php echo form_open("",'id="search_form" method="get" ');?>
          <div class="row g-3">
            <div class="col-sm-5 mb-2 mb-sm-0 pe-sm-5">
              <input type="text" name="keyword" class="form-control fs-7 w-100" value="<?php echo $posted_keyword;?>" placeholder="Search by title">
            </div>
             <div class="col-2 col-sm-2 mb-2">
              <input type="submit" class="btn btn-sm btn-purple me-2" value="Search">
              <?php
              if( $posted_keyword!='') {
              echo '<a href="'.site_url('admin/notifications').'" class="btn btn-sm btn-outline-danger"><b>Clear</b></a>';
              }?>
            </div>
            <div class="col-3 col-sm-2 mb-2">Show Entries 
              <?php echo front_record_per_page('per_page','per_page');?>
            </div>
            <div class="col">
            <?php
			if($this->mres['member_type'] == '1'){?>
              <a href="javascript:void(0);" data-fancybox="" data-type="iframe" data-src="<?php echo site_url('admin/notifications/add');?>" class="pop1 float-end btn btn-purple">Add Notification</a>
              <?php
			}
			?>
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
                    <th>No.</th>
                    <th>Notification</th>
                    <th>Date</th>
                    <?php if($this->mres['member_type']=='1'){?><th>Action</th><?php } ?>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $i=1;
                  if(is_array($res_notification_data) && !empty($res_notification_data)){
                    foreach ($res_notification_data as $key => $val) {
                      ?>
                      <tr>
                        <td><?php echo $i;?></td>
                        <td>
                          <b class="d-block"><?php echo $val['notification_title'];?></b>
                          <p><?php echo $val['description'];?></p>
                        </td>
                        <td><?php echo getDateFormat($val['created_at'],1);?></td>
                        <?php if($this->mres['member_type']=='1'){?>
                        <td class="text-nowrap">
                        <a href="javascript:void(0);" data-fancybox="" data-type="iframe" title="Edit" data-src="<?php echo site_url('admin/notifications/edit/'.$val['notification_id']);?>" class="pop1"> 
                            <img src="<?php echo theme_url();?>images/edit.svg" width="15" alt="Edit">
                          </a> 
                          
                          <a href="<?php echo site_url("admin/notifications/notification_delete/".md5($val['notification_id']));?>" class="me-2 confirm_delete" title="Delete">
                            <img src="<?php echo theme_url();?>images/delete.svg" width="19" alt="Delete" class="hand">
                          </a>
                        </td>
                        <?php
						}?>
                      </tr>
                      <?php 
                      $i++;
                      } 
                    }
                    else{
                    echo '<tr><td colspan="5"><div class="text-center b mt-4">'.$this->config->item('no_record_found').'</div></td></tr>';
                  }?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <?php echo $page_links;?>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
</div>
<?php $this->load->view("bottom_application",array('x_dsg_page'=>'notification_list'));?>