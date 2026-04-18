<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
  array('heading'=>'Dashboard','url'=>'admin')
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
          <?php echo form_open("",'id="search_form" method="get" ');
          ?>
          <div class="row g-0">
            <div class="col-sm-4 mb-2 mb-sm-0 pe-sm-4">
              <input type="text" name="keyword" class="form-control fs-7 w-100" value="<?php echo $posted_keyword;?>" placeholder="Search by name, email, phone">
            </div>
            <div class="col-4 col-sm-2 mt-1"> 
              <select class="bg-light border ms-1 rounded-2 p-1" name="status">
                <option value="">Status</option>
                <option value="1" <?php echo $posted_status==='1' ? 'selected="selected"' : '';?>>Active</option>
                <option value="0" <?php echo $posted_status==='0' ? 'selected="selected"' : '';?>>In-active</option>
              </select>
            </div>
            <div class="col-2 col-sm-2 mt-1">
              <input type="submit" class="btn btn-sm btn-purple me-2" value="Search">
              <?php
              if( $posted_keyword!='' || $posted_status!='') {
              echo '<a href="'.site_url('admin/sub_admins').'" class="btn btn-sm btn-outline-danger"><b>Clear</b></a>';
              }?>
            </div>
            <div class="col-2 col-sm-2 mt-1">Show Entries 
              <?php echo front_record_per_page('per_page','per_page');?>
            </div>
            <div class="col-2 col-sm-2 text-end">
              <a href="<?php echo site_url('admin/member_add');?>" class="btn btn-purple">Add User</a>
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
                    <th>Sno.</th>
                    <th>Name</th>
                    <th>Login Info</th>
                    <th>Contact Info</th>
                    <th>IP Address</th>
                    <th>Created By</th>
                    <th>Created On</th>
                    <th>Rate %</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i=1;
                  if(is_array($res) && !empty($res)){
                     function getRecordName($id, $table, $column) {
                      return $id > 0 ? log_fetched_rec($id, $table, $column)['rec_data'][$column] ?? '' : '';
                    }
                    foreach ($res as $key => $val) {
                      $loop_address = $val['address'];
                      $city_name = getRecordName($val['city'], 'city', 'title');
                      $state_name = getRecordName($val['state'], 'state', 'title');
                      $country = getRecordName($val['country'], 'country', 'country_name');
                      $zipcode = $val['pin_code'] > 0 ? $val['pin_code'] : '';
                      $loop_address .= $city_name ? ' ' . $city_name : '';
                      $loop_address .= $state_name ? ', ' . $state_name : '';
                      $loop_address .= $zipcode ? ' - ' . $zipcode : '';
                      $loop_address .= $country ? ' (' . $country . ')' : '';
                      ?>
                      <tr>
                        <td><?php echo $i;?></td>
                        <td><?php echo $val['name'];?></td>
                        <td>
                          <p>
                            <span class="text-secondary">Username:</span><br>
                            <?php echo $val['user_name'];?>
                          </p>
                          <p class="mt-1">
                            <span class="text-secondary">Password:</span><br>
                            <?php echo $this->safe_encrypt->decode($val['password']);?>
                          </p>
                        </td>
                        <td>
                          <p><?php echo $val['mobile_number'];?></p>
                          <p class="mt-1"><?php echo $loop_address;?></p>
                        </td>
                        <td>
                          <p><?php echo $val['ip_address'];?></p>
                          <?php
                          echo ($val['last_login_date']) ? '<p class="mt-2">
                            <span class="text-secondary">Last Login:</span>
                            '.getDateFormat($val['last_login_date'],7).'
                          </p>' : '';?>
                        </td>
                        <td><?php echo ($val['added_by']>0) ? 'Auva Digital Media' : 'Self';?></td>
                        <td><?php echo getDateFormat($val['account_created_date'],7);?></td>
                        <td><?php echo $val['commission'];?></td>
                        <td>
                          <?php if($val['status']=='0'){?>
                          <a href="<?php echo site_url('admin/member_status/'.md5($val['customers_id']).'?status=active')?>" class="btn btn-sm btn-danger me-3" title="Click Here to Activate">Deactivate</a>
                          <?php }else{ ?>
                            <a href="<?php echo site_url('admin/member_status/'.md5($val['customers_id']).'?status=deactive')?>" class="btn btn-sm btn-primary" title="Click Here to Deactivate">Activate</a>
                          <?php } ?>
                        </td>
                        <td class="white_space">
                          <a href="<?php echo site_url('admin/view_profile/'.md5($val['customers_id']));?>" class="me-2">
                            <img src="<?php echo theme_url();?>images/eye2.svg" width="18" alt="Edit">
                          </a> 
                          <a href="<?php echo site_url("admin/member_edit/".md5($val['customers_id']));?>" class="me-2" title="Edit">
                            <img src="<?php echo theme_url();?>images/edit.svg" width="15" alt="Edit">
                          </a>
                          <img src="<?php echo theme_url();?>images/delete.svg" width="19" alt="Delete" data-bs-toggle="modal" data-bs-target="#exampleModal" class="hand">
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