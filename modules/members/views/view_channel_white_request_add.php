<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
  array('heading'=>'Dashboard','url'=>'members')
);
$request_types = $this->config->item('request_types');
$posted_keyword = escape_chars($this->input->get_post('keyword',TRUE));
$posted_status = escape_chars($this->input->get_post('status',TRUE)); 
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
          <?php
          if(error_message() !=''){
            echo error_message();
          }?>

          <div class="mb-4"> 
            <a href="<?= site_url('members/channel_add_request');?>" class="btn btn-outline-dark ">Channel Add Request</a>  
            <a href="javaScript:void(0)" class="btn btn-outline-dark active">Channel White List Request</a>  
            <a href="<?= site_url('members/claim_release_request');?>" class="btn btn-outline-dark ">Claim Release Request</a>
          </div>

          <?php echo form_open("",'id="permission_form"');?>
          <input type="hidden" name="request_type" value="2">

           <div class="row gx-0 gy-3">
            <?php
            $total_urls = (int) set_value('num_add_rows',1);
            $total_urls = $total_urls==0 ? 1 : $total_urls;
            ?> 
            <input type="hidden" name="num_add_rows" id="num_add_rows" value="<?php echo $total_urls;?>" />
            <label for="Name" class="form-label">URL *</label>
            <div class="col-12 schedules" id="rows_container">
              <?php  
              for($i = 0;$i<$total_urls;$i++) {?>
              <div class="dy_dg mb-3 row rows_clone_container">
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="urls[]" value="<?php echo set_value('urls[' . $i . ']'); ?>" placeholder="Enter URL">
                  <?php echo form_error('urls[' . $i . ']'); ?>
                </div>
                <div class="col-sm-2">
                  <button type="submit" class="btn btn-sm btn-danger fs-4 lh-1 remove_rows <?php echo $total_urls==1 ? 'd-none ' : '';?>">-</button>
                </div>
              </div>
              <?php }?>
            </div>
            <div class="mt-2">
              <button id="add_more_rows" name="Button" type="button" class="btn btn-sm btn-dark fs-4 lh-1">+</button>
            </div>
            <div class="col-12">
              <input name="btn_sbt" type="submit" class="btn btn-purple" value="Submit">
            </div>
          </div>
          <?php echo form_close();?>
        </div>
        <div class="white_bx overflow-hidden mt-3">      
          <div class="table-responsive">
            <div class="scrollbar style-4">
              <table class="table table-bordered mb-0 acc_table table-striped">
                <thead>
                  <tr>
                    <th width="10%">S.No.</th>
                    <th width="25%">Type</th>
                    <th width="25%">URL</th>
                    <th width="15%">Date</th>
                    <!-- <th width="30%">Status</th> -->
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i=1;
                  $label_status_arr = $this->config->item('label_status_arr');
                  if(is_array($res) && !empty($res)){
                    foreach ($res as $key => $val) {
                      ?>
                      <tr>
                        <td><?php echo $i;?></td>
                        <td>
                          <p><?php echo $request_types[$val['request_type']];?></p>
                        </td>
                        <td>
                          <p><?php echo $val['url'];?></p>
                        </td>
                        <td><?php echo getdateFormat($val['created_date'],1);?></td>
                        <td class="text-<?php echo ($val['status']=='1')? 'success':'danger';?>">
                          <?php
                          echo $label_status_arr[$val['status']];
                          echo ($val['status']=='3' && $val['reason']!='')? '<p class="mt-2 text-secondary"><span class="fw-semibold">Reason:</span> &ldquo; '.$val['reason'].' &rdquo;</p>' : '';
                          ?>
                        </td>
                        <?php
                        /*
                        <td class="text-nowrap">
                          <a href="<?php echo site_url("members/channel_request_delete/".md5($val['request_id']));?>" class="me-2 confirm_delete" title="Delete">
                            <img src="<?php echo theme_url();?>images/delete.svg" width="19" alt="Delete" class="hand">
                          </a>
                        </td>
                        */?>
                      </tr>
                      <?php 
                      $i++;
                      } 
                    }
                    else{
                    echo '<tr><td colspan="8"><div class="text-center b mt-4">'.$this->config->item('no_record_found').'</div></td></tr>';
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
<?php $this->load->view('dynamic_form_blocks_js');?>
<?php $this->load->view("bottom_application");?>