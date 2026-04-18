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
        <div class="mb-3">
          <?php
          if(error_message() !=''){
            echo error_message();
          }?>
          <?php echo form_open("",'id="permission_form"');?>
          <div class="row g-3">
            <div class="col-sm-5 col-lg-4">
              <label class="form-label">Select User *</label>
              <select class="form-select" name="customers_id" id="customers_id">
              <option value="">Select</option>
              <?php
                $posted_user_id = set_value('customers_id');
                if(is_array($res_customers) && !empty($res_customers)){
                  foreach($res_customers as $lkey=>$lv){
                    echo '<option value="'.$lv['customers_id'].'" '.(($posted_user_id==$lv['customers_id'])? 'selected' : '').'>'.$lv['name'].' [ '.$lv['sponsor_id'].' ]</option>';
                  }
                }?>
              </select>
              <?php echo form_error('customers_id');?>
            </div>
            <div class="col-8 col-sm-5 col-lg-4">
              <label class="form-label">Permission *</label>
              <?php
              $posted_permission = set_value('permission');?>
              <select class="form-select" name="permission" id="permission">
                <option value="">Select</option>
                <option value="1" <?php echo ($posted_permission==1)? 'selected' : '';?>>Yes</option>
              </select>
              <?php echo form_error('permission');?>
            </div>
            <div class="col">
              <label class="form-label d-none d-sm-block">&nbsp;</label>
              <button type="submit" name="btn_sbt"class="btn btn-purple" value="Submit">Submit</button>
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
                    <th>S. No.</th>
                    <th>User Name</th>
                    <th>Permission</th>
                    <th>Date</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i=1;
                  if(is_array($res) && !empty($res)){
                    foreach ($res as $key => $val) {
                      ?>
                      <tr>
                        <td><?php echo $i;?></td>
                        <td><?php echo $val['name'].' [ '.$val['sponsor_id'].' ]';?></td>
                        <td><?php echo ($val['permission']>0) ? 'Yes' : 'No';?></td>
                        <td><?php echo getdateFormat($val['created_date'],1);?></td>
                        <td>
                          <a href="<?php echo site_url("admin/download_permission_delete/".md5($val['dp_id']));?>" class="me-2 confirm_delete" title="Delete">
                            <img src="<?php echo theme_url();?>images/delete.svg" width="19" alt="Delete" class="hand">
                          </a>
                        </td>
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
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<?php
$default_date = $this->config->item('site_start_date');
$posted_agreement_from = $this->input->post('agreement_from');
?>
<script>
  $(document).ready(function(){
    $('[id ^="per_page"]').on('change',function(){
      $(':hidden[name="end_date"]','#ord_frm').val($(this).val());
      $('#ord_frm').submit();
    });
    $(document).on('click','.start_date,.end_date',function(e){
      e.preventDefault();
      cls = $(this).hasClass('start_date') ? 'start_date1' : 'end_date1';
      $('.'+cls+':eq(0)').focus();
    });
    $(document).on('focus','.start_date1',function(){
      $(this).datepicker({
        showOn: "focus",
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        defaultDate: 'y',
        buttonText:'',
        minDate:'<?php echo $default_date;?>' ,
        maxDate:'<?php echo date('Y-m-d',strtotime(date('Y-m-d',time())));?>',
        yearRange: "c-100:c+100",
        buttonImageOnly: true,
        onSelect: function(dateText, inst) {
          $('.start_date1').val(dateText);
          $(".end_date1").datepicker("option",{
            minDate:dateText ,
            maxDate:'<?php echo date('Y-m-d',strtotime('+365 days'));?>',
          });
        }
      });
    });
    $(document).on('focus','.end_date1',function(){
      $(this).datepicker({
        showOn: "focus",
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        defaultDate: 'y',
        buttonText:'',
        minDate:'<?php echo $posted_agreement_from!='' ? $posted_agreement_from :  $default_date;?>' ,
        maxDate:'<?php echo date('Y-m-d',strtotime('+2 years'));?>',
        yearRange: "c-100:c+100",
        buttonImageOnly: true,
        onSelect: function(dateText, inst) {
          $('.end_date1').val(dateText);
        }
      });
    });
  });
</script>
<?php $this->load->view("bottom_application");?>