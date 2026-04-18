<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
  array('heading'=>'Dashboard','url'=>'admin')
);
$site_title_text = escape_chars($this->config->item('site_name'));
$posted_keyword = escape_chars($this->input->get_post('keyword',TRUE));
$posted_status = escape_chars($this->input->get_post('status',TRUE)); 
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
              <input type="text" name="keyword" class="form-control fs-7 w-100" value="<?= $posted_keyword;?>" placeholder="Search by title, label, post by">
            </div>
            <div class="col-3 col-sm-3 mt-1">
              <input type="submit" class="btn btn-sm btn-purple me-2" value="Search">
              <?php
              if( $posted_keyword!='') {
              echo '<a href="'.site_url('admin/album/final_album').'" class="btn btn-sm btn-outline-danger">Clear</a>';
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
                        <td><?= $artist_name;?></td>
                        <td><?= $val['label_name'];?></td>
                        <td>
                          Catlog# :<?php echo $val['producer_catalogue'];?>
                          <p class="mt-1">ISRC# :<?= $val['isrc'];?></p>
                          <p class="mt-1">UPC/EAN #:<?= $val['upc_ean'];?></p>
                        </td>
                        <td class="white_space">
                          <p><?= $total_territories;?> terrs</p>
                          <a data-fancybox="" data-type="iframe" data-src="<?= site_url('admin/album/view_stored/'.md5($val['release_id']));?>" href="javascript:void(0);" class="pop2 text-primary mt-1 d-block"><?= $total_stores;?> stored</a>
                        </td>
                        <td><?= getDateFormat($val['original_release_date_of_music'],1);?></td>
                        <td>
                          <?= $val['first_name'];?><br>
                          at: <?= getDateFormat($val['created_date'],7);?>
                        </td>
                        <td class="white_space">
                          <a data-fancybox="" data-type="iframe" data-src="<?= site_url('admin/release/view_meta_release/'.md5($val['release_id']));?>" href="javascript:void(0);" class="pop2 btn btn-info btn-sm me-2">
                            <img src="<?= theme_url();?>images/eye2.svg" width="18" decoding="async" fetchpriority="high" alt="View">
                          </a>
                          <?php
                          if($this->mres['member_type']==1){
                            if($val['status']==1 && $val['is_verify_meta']==1 && $val['is_pdl_submit']!=1){?>
                            <a data-fancybox="" data-type="iframe" data-src="<?= site_url('admin/album/final_api/'.md5($val['release_id']).'?album_type='.$val['album_type']);?>" class="btn btn-success btn-sm pop1">Final Submit</a>
                            <?php
                            }
                            if ($val['is_verify_meta'] == '1') { ?>
                            <div class="btn-group btn-group-sm" role="group">
                              <?php 
                              $flags = [
                                  'is_new_released'   => 'star',
                                  'is_recently_added' => 'clock',
                                  'is_latest'         => 'bolt',
                                  'is_top_rated'      => 'thumbs-up'
                              ];
                              foreach ($flags as $field => $icon) {
                                $active = !empty($val[$field]);
                                ?>
                                <button type="button" class="btn btn-sm btn-<?= $active ? 'success' : 'secondary'; ?> toggle-flag" data-id="<?= $val['id']; ?>" data-field="<?= $field; ?>" data-title="<?= ucfirst(str_replace('_', ' ', $field)); ?>" title="<?= $active ? 'Unset as ' : 'Set as '; ?><?= ucfirst(str_replace('_', ' ', $field)); ?>" style="width:37px;height:34px;margin-right:2px;"> <i class="fas fa-<?= $icon; ?>"></i>
                                </button>
                                <?php 
                              }?>
                            </div>
                            <?php } ?>
                            <div class="align-items-center add_status mb-1 mt-2">
                              <select class="form-select fs-7 mb-1 me-1 w-100 lstatus" name="status">
                                <option value="">Select</option>
                                <?php
                                if(is_array($album_status_arr) && !empty($album_status_arr)){
                                  foreach($album_status_arr as $lkey=>$lv){
                                    if ($lkey != 2 && !($lkey == 1 && $this->mres['member_type'] != 1)) {
                                      if (($val['status'] == 1 && $val['is_verify_meta'] == 1) && in_array($lkey, [0,5,6])) {
                                        continue;
                                      }
                                      echo '<option value="'.$lkey.'" '.(($lkey==$val['status'])? 'selected' : '').'>'.$lv.'</option>';
                                    }
                                  }
                                }?>
                              </select>
                              <div id="err_status_<?= $val['release_id'];?>" class="required"></div>
                              <input type="text" name="reason" class="form-control fs-7 lreason dn" placeholder="Enter Reason">
                              <div id="err_reason_<?= $val['release_id'];?>" class="required"></div>
                              </div>
                              <input type="button" value="Update" class="btn btn-sm btn-primary mt-1 btn_sbt">
                              <div class="text-success msg" id="msg_<?= $val['release_id'];?>"></div>
                            </div>
                            <?php
                          } 
                          echo '<p class="mt-1">Status : <span class="mt-1 text-danger fw-semibold">'.$album_status_arr[$val['status']].'</span></p>';
                          echo (($val['status']=='3' || $val['status']=='4') && $val['reason']!='')? '<p class="mt-2 text-secondary"><span class="fw-semibold">Reason:</span> &ldquo; '.$val['reason'].' &rdquo;</p>' : '';
                          ?>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
  $(document).ready(function() {
    // Handle status change
    $('.lstatus').on('change', function(e) {
      var cobj = $(this);
      var parent_node_obj = cobj.closest('.pr_parent');
      var status = cobj.val();
      
      if (status == '3' || status == '4') {
        parent_node_obj.find('.lreason').removeClass('dn');
      } else {
        parent_node_obj.find('.lreason').addClass('dn');
      }
    });
    function validateData(ref_node) {
      var fld, ref_hint, err = 0;
      var data_obj = {};
      var err_obj = [];
      var release_id = ref_node.data('release-id');
      data_obj['btn_sbt'] = 'Y';
      data_obj['release_id'] = release_id;
      var status = ref_node.find('.lstatus').val();
      if (!status) {
        err_obj.push({ msg: "Please select a status", ele: '.lstatus', 'hint': 'status_' + release_id });
      } else {
        data_obj['status'] = status;
      }
      if (status == '3' || status == '4') {
        var reason = ref_node.find('.lreason').val();
        if (!reason) {
          err_obj.push({ msg: "Please enter a reason", ele: '.lreason', 'hint': 'reason_' + release_id });
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
          url: '<?= site_url('admin/album');?>',
          type: 'POST',
          data: res['data_obj'],
          headers: { XRSP: 'json' },
          dataType: 'json',
          success: function(data) {
            if (data.status == '1') {
              $('#msg_' + data.release_id).html(data.msg);
              location.reload();
            } else {
              if (Object.keys(data.error_flds).length) {
                $.each(data.error_flds, function(m, n) {
                  $('#err_' + m + '_' + res['data_obj']['release_id']).html('<div class="required">' + n + '</div>');
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
<script>
$(document).on('click', '.toggle-flag', function () {
    var btn   = $(this),
    id    = btn.data('id'),
    field = btn.data('field'),
    label = btn.data('title');
    function toast(icon, title) {
      Swal.fire({
          toast: true,
          position: 'top-end',
          icon: icon,
          title: title,
          showConfirmButton: false,
          timer: 1500
      });
    }
    $.post('<?php echo base_url("admin/album/toggle_"); ?>' + field + '/' + id, {
      <?php echo $this->security->get_csrf_token_name(); ?>:
      '<?php echo $this->security->get_csrf_hash(); ?>'
    }, function (res) {
      if (res.status === 'success') {
        var active = res.new_value == 1;
        btn.toggleClass('btn-success', active)
           .toggleClass('btn-secondary', !active)
           .attr('title', (active ? 'Unset as ' : 'Set as ') + label);

        toast('success', 'Flag updated successfully!');
      } else {
          toast('error', res.message || 'Something went wrong!');
      }
    }, 'json').fail(function () {
        toast('error', 'Request failed!');
    });
});
</script>
<?php $this->load->view("bottom_application");?>