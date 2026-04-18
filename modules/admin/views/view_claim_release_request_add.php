<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
  array('heading'=>'Dashboard','url'=>'admin')
);
$request_types = $this->config->item('request_types');
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
        <div class="dash_box p-4">
          <?php
          if(error_message() !=''){
            echo error_message();
          }?>

          
          <div class="mb-4"> 
            <a href="<?php echo base_url();?>admin/channel_add_request" class="btn btn-outline-dark ">Channel Add Request</a>  
            <a href="<?php echo base_url();?>admin/channel_white_list_request" class="btn btn-outline-dark ">Channel White List Request</a>  
            <a href="javaScript:void(0)" class="btn btn-outline-dark active">Claim Release Request</a>
            
              </div>


          <?php echo form_open("",'id="permission_form"');?>
          <input type="hidden" name="request_type" value="3">
               
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
                    <th width="25%">Status</th>
                    <th width="10%">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i=1;
                  $label_status_arr = $this->config->item('label_status_arr');
                  if(is_array($res) && !empty($res)){
                    foreach ($res as $key => $val) {
                      ?>
                      <tr class="pr_parent" data-request-id="<?php echo $val['request_id'];?>">
                        <td><?php echo $i;?></td>
                        <td>
                          <p><?php echo $request_types[$val['request_type']];?></p><hr>
                          <b>Added By :</b> <?php echo $val['first_name'];?>
                        </td>
                        <td>
                          <p><?php echo $val['url'];?></p>
                        </td>
                        <td><?php echo getdateFormat($val['created_date'],1);?></td>
                        <td class="text-<?php echo ($val['status']=='1')? 'success':'danger';?>">
                          <?php
                          if($val['status']=='0'){?>
                            <div class="d-flex flex-wrap align-items-center add_status mb-2">
                              <select class="form-select fs-7 mb-1 me-1 lstatus" name="status">
                                <option value="">Select</option>
                                <?php
                                if(is_array($label_status_arr) && !empty($label_status_arr)){
                                  foreach($label_status_arr as $lkey=>$lv){
                                    if ($lkey != 2) {
                                      echo '<option value="'.$lkey.'" '.(($lkey==$val['status'])? 'selected' : '').'>'.$lv.'</option>';
                                    }
                                  }
                                }?>
                              </select>
                              <div id="err_status_<?php echo $val['request_id'];?>" class="required"></div>
                              <input type="text" name="reason" class="form-control fs-7 lreason dn" placeholder="Enter Reason">
                              <div id="err_reason_<?php echo $val['request_id'];?>" class="required"></div>
                              </div>
                              <input type="button" value="Submit" class="btn btn-sm btn-primary mt-1 btn_sbt">
                              <div class="text-success msg" id="msg_<?php echo $val['request_id'];?>"></div>
                            </div>
                            <?php 
                          }
                          echo $label_status_arr[$val['status']];
                          echo ($val['status']=='3' && $val['reason']!='')? '<p class="mt-2 text-secondary"><span class="fw-semibold">Reason:</span> &ldquo; '.$val['reason'].' &rdquo;</p>' : '';
                          ?>
                        </td>
                        <td class="text-nowrap">
                          <a href="<?php echo site_url("admin/channel_request_delete/".md5($val['request_id']));?>" class="me-2 confirm_delete" title="Delete">
                            <img src="<?php echo theme_url();?>images/delete.svg" width="19" alt="Delete" class="hand">
                          </a>
                        </td>
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
<script type="text/javascript">
  $(document).ready(function() {
    // Handle status change
    $('.lstatus').on('change', function(e) {
      var cobj = $(this);
      var parent_node_obj = cobj.closest('.pr_parent');
      var status = cobj.val();
      
      if (status == '3') {
        parent_node_obj.find('.lreason').removeClass('dn');
      } else {
        parent_node_obj.find('.lreason').addClass('dn');
      }
    });
    function validateData(ref_node) {
      var fld, ref_hint, err = 0;
      var data_obj = {};
      var err_obj = [];
      var request_id = ref_node.data('request-id');
      data_obj['btn_sbt'] = 'Y';
      data_obj['request_id'] = request_id;
      var status = ref_node.find('.lstatus').val();
      if (!status) {
        err_obj.push({ msg: "Please select a status", ele: '.lstatus', 'hint': 'status_' + request_id });
      } else {
        data_obj['status'] = status;
      }
      if (status == '3') {
        var reason = ref_node.find('.lreason').val();
        if (!reason) {
          err_obj.push({ msg: "Please enter a reason", ele: '.lreason', 'hint': 'reason_' + request_id });
        } else {
          data_obj['reason'] = reason;
        }
      }
      if (err_obj.length) {
        $.each(err_obj, function(m, n) {
          $('#err_' + n.hint).html(n.msg);
          if (!err) {
            err = 1;
            fld = ref_node.find(n.ele);
          }
        });
        fld.focus();
      }
      
      return { error: err, data_obj: data_obj };
    }
    $('.btn_sbt').on('click', function(e) {
      e.preventDefault();
      var cobj = $(this);
      var parent_node_obj = cobj.closest('.pr_parent');
      parent_node_obj.addClass('overlay_enable');
      parent_node_obj.find('.required, .msg').html('');

      var res = validateData(parent_node_obj);
      if (!res['error']) {
        $.ajax({
          url: '<?php echo site_url('admin/channel_request_status');?>',
          type: 'POST',
          data: res['data_obj'],
          headers: { XRSP: 'json' },
          dataType: 'json',
          success: function(data) {
            if (data.status == '1') {
              $('#msg_' + data.request_id).html(data.msg);
              location.reload();
            } else {
              if (Object.keys(data.error_flds).length) {
                $.each(data.error_flds, function(m, n) {
                  $('#err_' + m + '_' + res['data_obj']['request_id']).html('<div class="required">' + n + '</div>');
                });
              }
            }
          },
          always: function() {
            parent_node_obj.removeClass('overlay_enable');
          }
        });
      } else {
        parent_node_obj.removeClass('overlay_enable');
      }
    });
  });
</script>
<?php $this->load->view('dynamic_form_blocks_js');?>
<?php $this->load->view("bottom_application");?>