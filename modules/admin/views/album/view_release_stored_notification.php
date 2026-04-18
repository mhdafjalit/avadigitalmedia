<?php
$this->load->view('top_application',['has_header'=>false,'ws_page'=>'store_pg','is_popup'=>true,'has_body_style'=>'padding:0']);?>
<div class="p-3 bg-light text-center border-bottom">
  <h1><?php echo $heading_title;?></h1>
</div>
<div class="pt-4 pb-4 ps-2 pe-2">
  <div class="row gx-2 gy-6 text-center">
    <?php
    if(is_array($release_stores) && !empty($release_stores)){
      foreach ($release_stores as $key => $val) {
        echo '<div class="col-2 m-2 p-2 border rounded-3">
          <div class="text-center overflow-hidden m-auto">
            <span class="align-middle d-table-cell">
              <img src="'.theme_url().'store_icons/'.$val['icon'].'" alt="'.$val['store_title'].'" width="85" height="35" class="">
            </span>
          </div>
          <p class="mt-1 fs-8 fw-medium">'.$val['store_title'].'</p>
        </div>';
      }
    }else{
      echo '<div class="text-center b mt-4">'.$this->config->item('no_record_found').'</div>';
    }?>
  </div>
</div>
<?php $this->load->view("bottom_application",array('has_footer'=>false,'ws_page'=>'store_pg','is_popup'=>true));?>