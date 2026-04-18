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
            <div class="col-sm-5 mb-2 mb-sm-0 pe-sm-5">
              <input type="text" name="keyword" class="form-control fs-7 w-100" value="<?php echo $posted_keyword;?>" placeholder="Search by title">
            </div>
            <div class="col-2 col-sm-2 mt-1">
              <input type="submit" class="btn btn-sm btn-purple me-2" value="Search">
              <?php
              if( $posted_keyword!='') {
              echo '<a href="'.site_url('members/labels').'" class="btn btn-sm btn-outline-danger"><b>Clear</b></a>';
              }?>
            </div>
            <div class="col-3 col-sm-2 mt-1">Show Entries 
              <?php echo front_record_per_page('per_page','per_page');?>
            </div>
            <div class="col-3 col-sm-2 text-end">
              <a href="<?php echo site_url('members/add_label');?>" class="btn btn-purple">Add Label</a>
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
                    <th>Channel Details</th>
                    <th>Contact Details</th>
                    <th>User Rate %</th>
                    <th>Agreement Period</th>
                    <th>Download</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i=1;
                  $label_status_arr = $this->config->item('label_status_arr');
                  if(is_array($res) && !empty($res)){
                    foreach ($res as $key => $val) {
                      ?>
                      <tr class="pr_parent" data-label-id="<?php echo $val['label_id'];?>">
                        <td><?php echo $i;?></td>
                        <td>
                          <p><?php echo $val['channel_name'];?></p>
                          <p class="mt-1"><?php echo $val['channel_url'];?></p>
                          <b>Added On :</b> <?php echo getDateFormat($val['created_date'],1);?>
                        </td>
                        <td>
                          <p><?php echo $val['email'];?></p>
                          <p class="mt-1"><?php echo $val['phone'];?></p>
                        </td>
                        <td><?php echo $val['user_rate'];?>%</td>
                        <td class="lh-base text-nowrap">
                          <b>From:</b> <?php echo getdateFormat($val['agreement_from'],1);?><br>  
                          <b>To:</b> <?php echo getdateFormat($val['agreement_to'],1);?>
                        </td>
                        <td>
                          <?php
                          if(!empty($val['government_doc']) && file_exists(UPLOAD_DIR.'/labels/'.$val['government_doc'])){
                            echo '<a href="'.site_url('pages/download_file/labels/').$val['government_doc'].'" title="Download Government Id" class="me-2">
                              <img src="'.theme_url().'images/download.svg" alt="Download">
                            </a>';
                          }if(!empty($val['agreement_doc']) && file_exists(UPLOAD_DIR.'/labels/'.$val['agreement_doc'])){
                            echo '<a href="'.site_url('pages/download_file/labels/').$val['agreement_doc'].'" title="Download User Agreement">
                              <img src="'.theme_url().'images/download.svg" alt="Download">
                            </a>';
                          }?>
                        </td>
                        <td class="text-<?php echo ($val['status']=='1')? 'success':'danger';?>">
                          <?php
                          echo $label_status_arr[$val['status']];
                          echo ($val['status']=='3' && $val['reason']!='')? '<p class="mt-2 text-secondary"><span class="fw-semibold">Reason:</span> &ldquo; '.$val['reason'].' &rdquo;</p>' : '';
                          ?>
                        </td>
                        <td class="text-nowrap">                          
                          <a href="<?php echo site_url("members/edit_label/".md5($val['label_id']));?>" class="me-2" title="Edit">
                            <img src="<?php echo theme_url();?>images/edit.svg" width="15" alt="Edit">
                          </a> 
                          
                          <a href="<?php echo site_url("members/label_delete/".md5($val['label_id']));?>" class="me-2 confirm_delete" title="Delete">
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
      var label_id = ref_node.data('label-id');
      data_obj['btn_sbt'] = 'Y';
      data_obj['label_id'] = label_id;
      var status = ref_node.find('.lstatus').val();
      if (!status) {
        err_obj.push({ msg: "Please select a status", ele: '.lstatus', 'hint': 'status_' + label_id });
      } else {
        data_obj['status'] = status;
      }
      if (status == '3') {
        var reason = ref_node.find('.lreason').val();
        if (!reason) {
          err_obj.push({ msg: "Please enter a reason", ele: '.lreason', 'hint': 'reason_' + label_id });
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
          url: '<?php echo site_url('admin/labels');?>',
          type: 'POST',
          data: res['data_obj'],
          headers: { XRSP: 'json' },
          dataType: 'json',
          success: function(data) {
            if (data.status == '1') {
              $('#msg_' + data.label_id).html(data.msg);
              location.reload();
            } else {
              if (Object.keys(data.error_flds).length) {
                $.each(data.error_flds, function(m, n) {
                  $('#err_' + m + '_' + res['data_obj']['label_id']).html('<div class="required">' + n + '</div>');
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
<?php $this->load->view("bottom_application");?>