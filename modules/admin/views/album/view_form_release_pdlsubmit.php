<?php
$this->load->view('top_application',['has_header'=>false,'ws_page'=>'store_pg','is_popup'=>true,'has_body_style'=>'padding:0']);?>
<div class="p-3 bg-light text-center border-bottom">
  <h1><?php echo $heading_title;?></h1>
</div>
<div class="pt-4 pb-4 ps-2 pe-2">
  <div class="dash_box p-4">
    <?php 
    echo error_message();
    echo form_open(current_url_query_string(),'name="api_final_frm" id="api_final_frm" autocomplete="off"');?>
    <div class="mb-3">
      <label class="form-label">Release Platform</label>
      <select name="platforms_to_release" class="form-control">
        <option value="">Select</option>
        <?php
        $platforms_to_release = set_value('platforms_to_release');
        if(is_array($pdl_release_platform) && !empty($pdl_release_platform)){
          foreach ($pdl_release_platform as $key => $val) {
            echo '<option value="'.$val.'" '.(($platforms_to_release==$val)? 'selected' :'').'>'.$val.'</option>';
          }    
        }?>
      </select>
      <?php echo form_error('platforms_to_release');?>
    </div>
    <div class="mb-3">
      <button type="submit" name="btn_sbt" value="Y" class="btn btn-success">Submit</button>
    </div>
    <?php echo form_close();?>
  </div>
</div>
<?php $this->load->view("bottom_application",array('has_footer'=>false,'ws_page'=>'store_pg','is_popup'=>true));?>