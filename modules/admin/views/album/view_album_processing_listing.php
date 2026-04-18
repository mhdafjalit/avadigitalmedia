<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
  array('heading'=>'Dashboard','url'=>'admin')
);
$site_title_text = escape_chars($this->config->item('site_name'));
$posted_keyword = escape_chars($this->input->get_post('keyword',TRUE));
$posted_status = escape_chars($this->input->get_post('status',TRUE)); 
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="dash_outer">
  <div class="dash_container">
    <?php $this->load->view('view_left_sidebar'); ?>
    <div id="main-content" class="h-100">
      <?php $this->load->view('view_top_sidebar');?>
      <div class="top_sec d-flex justify-content-between">
        <h1 class="mt-4"><?php echo $heading_title;?></h1>
        <?php echo navigation_breadcrumb($heading_title,$bdcm_array); ?>
      </div>
      
      <div id="topMusicPlayer" class="pro-music-player d-none">

<div class="player-cover">
<img src="<?= theme_url();?>images/no-img2.jpg" id="coverImg">
</div>

<div class="player-main">

<div class="player-title" id="trackTitle">
Audio Preview
</div>

<div class="player-progress">

<span id="currentTime">0:00</span>

<div class="progress-wrap">
<input type="range" id="progressBar" value="0">
</div>

<span id="duration">0:00</span>

</div>

</div>

<div class="player-controls">

<button id="backwardBtn" class="control-btn">
<i class="fa fa-backward"></i>
</button>

<button id="playPauseBtn" class="play-main">
<i class="fa fa-play"></i>
</button>

<button id="forwardBtn" class="control-btn">
<i class="fa fa-forward"></i>
</button>

</div>

<audio id="globalAudio"></audio>

</div>


<style>
   .pro-music-player{
position:sticky;
top:0;
z-index:9999;
display:flex;
align-items:center;
gap:20px;
padding:12px 25px;
background:rgba(20,20,25,0.95);
backdrop-filter:blur(10px);
border-bottom:1px solid rgba(255,255,255,0.08);
box-shadow:0 5px 20px rgba(0,0,0,0.35);
color:#fff;
}

.player-cover img{
width:52px;
height:52px;
border-radius:10px;
object-fit:cover;
box-shadow:0 4px 10px rgba(0,0,0,0.4);
}

.player-main{
flex:1;
}

.player-title{
font-weight:600;
font-size:14px;
margin-bottom:6px;
}

.player-progress{
display:flex;
align-items:center;
gap:10px;
font-size:12px;
}

.progress-wrap{
flex:1;
}

#progressBar{
width:100%;
height:4px;
cursor:pointer;
}

.player-controls{
display:flex;
align-items:center;
gap:10px;
}

.control-btn{
background:none;
border:none;
color:#ccc;
font-size:18px;
cursor:pointer;
transition:.2s;
}

.control-btn:hover{
color:#fff;
transform:scale(1.1);
}

.play-main{
width:42px;
height:42px;
border-radius:50%;
border:none;
background:linear-gradient(135deg,#ff416c,#ff4b2b);
color:white;
display:flex;
align-items:center;
justify-content:center;
font-size:18px;
cursor:pointer;
box-shadow:0 5px 15px rgba(0,0,0,0.3);
transition:.25s;
}

.play-main:hover{
transform:scale(1.1);
}

.audio-card{
background:#ffffff;
border-radius:12px;
border:1px solid #eee;
padding:10px;
display:flex;
flex-direction:column;
align-items:center;
justify-content:center;
min-height:95px;
box-shadow:0 2px 6px rgba(0,0,0,0.05);
transition:.25s;
}

.audio-card:hover{
box-shadow:0 5px 15px rgba(0,0,0,0.12);
}

.play-circle{
width:40px;
height:40px;
border-radius:50%;
background:linear-gradient(135deg,#ff416c,#ff4b2b);
border:none;
color:white;
font-size:15px;
display:flex;
align-items:center;
justify-content:center;
cursor:pointer;
transition:.25s;
}

.play-circle:hover{
transform:scale(1.15);
}

.album-thumb{
width:60px;
height:60px;
border-radius:8px;
object-fit:cover;
}

.table td{
vertical-align:middle;
}



</style>

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
              echo '<a href="'.site_url('admin/album/album_processing').'" class="btn btn-sm btn-outline-danger"><b>Clear</b></a>';
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
                          <a data-fancybox="" data-type="iframe" data-src="<?= site_url('admin/album/view_stored/'.md5($val['release_id']));?>" href="javascript:void(0);" class="pop2 text-primary mt-1 d-block"><?= $total_stores;?> stored</a>
                        </td>
                        <td><?= getDateFormat($val['original_release_date_of_music'],1);?></td>
                        <td>
                          <?= $val['first_name'];?><br>
                          at: <?= getDateFormat($val['created_date'],7);?>
                        </td>
                    <td class="white_space">

<!-- ACTION BUTTON ROW -->
<div class="d-flex align-items-center gap-2 flex-wrap mb-2">

    <!-- View -->
    <a data-fancybox
       data-type="iframe"
       data-src="<?= site_url('admin/release/view_meta_release/'.md5($val['release_id']));?>"
       class="btn btn-info btn-sm d-flex align-items-center justify-content-center"
       title="View">

        <img src="<?= theme_url();?>images/eye2.svg" width="16">
    </a>

    <!-- AUDIO -->
    <?php 
    if(!empty($val['release_song']) && file_exists(UPLOAD_DIR.'/release/songs/'.$val['release_song'])): 
        $audio_url = base_url().'uploaded_files/release/songs/'.$val['release_song'];
    ?>
        <button type="button"
            class="btn btn-danger btn-sm playMediaBtn"
            data-audio="<?= $audio_url;?>"
            title="Play Audio">
            <i class="fa-solid fa-music"></i>
        </button>
    <?php else: ?>
        <button type="button"
            class="btn btn-secondary btn-sm"
            disabled
            title="No Audio">
            <i class="fa-solid fa-music"></i>
        </button>
    <?php endif; ?>

    <!-- FINAL SUBMIT -->
    <?php
    if($this->mres['member_type']==1){
        if($val['status']==6 && $val['is_verify_meta']==1 && $val['is_pdl_submit']!=1){ ?>
        
        <a data-fancybox
           data-type="iframe"
           data-src="<?= site_url('admin/album/final_api/'.md5($val['release_id']).'?album_type='.$val['album_type']);?>"
           class="btn btn-success btn-sm">
           Final Submit
        </a>

    <?php } } ?>

    <!-- DELETE -->
     <?php
    if($this->mres['member_type']==1){ ?>
    <button 
        type="button"
        class="btn btn-danger btn-sm deleteRelease"
        data-id="<?= $val['release_id']; ?>"
        title="Move to Trash">
        <i class="fa fa-trash"></i>
    </button>
 <?php  } ?>
</div>


<!-- STATUS SECTION -->
<div class="border-top pt-2">

    <p class="mb-1">
        Status :
        <span class="text-danger fw-semibold">
            <?= $album_status_arr[$val['status']]?>
        </span>
    </p>

    <?php if(($val['status']=='3' || $val['status']=='4') && $val['reason']!=''): ?>
        <p class="text-secondary small mb-2">
            <b>Reason:</b> <?= $val['reason']; ?>
        </p>
    <?php endif; ?>

    <!-- ADMIN CONTROLS -->
    <?php if($this->mres['member_type']==1){ ?>

    <div class="d-flex gap-2 align-items-start flex-wrap">

        <select class="form-select form-select-sm lstatus" name="status" style="width: 120px;">
            <option value="">Select</option>
            <?php
            foreach($album_status_arr as $lkey=>$lv){
                if ($lkey != 2 && !($lkey == 1 && $this->mres['member_type'] != 1)) {

                    if ($val['status'] == 5 && in_array($lkey,[0,1,4])) continue;
                    elseif ($val['is_verify_meta'] == 1 && in_array($lkey,[0,1,4,5])) continue;

                    echo '<option value="'.$lkey.'" '.(($lkey==$val['status'])?'selected':'').'>'.$lv.'</option>';
                }
            }
            ?>
        </select>

        <input type="text"
               name="reason"
               class="form-control form-control-sm lreason dn"
               placeholder="Reason"
               style="width: 140px;">

        <button class="btn btn-primary btn-sm btn_sbt">
            Update
        </button>

    </div>

    <div class="text-success small mt-1 msg" id="msg_<?= $val['release_id'];?>"></div>

    <?php } ?>

</div>

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


<script>

$(document).ready(function(){

let audio = document.getElementById("globalAudio");

/* PLAY BUTTON CLICK */
$(document).on("click",".playMediaBtn",function(){

let url = $(this).data("audio");
let title = $(this).closest("tr").find(".purple").text().trim();
let cover = $(this).closest("tr").find("img").attr("src");

$("#topMusicPlayer").removeClass("d-none");

$("#trackTitle").text(title);

$("#coverImg").attr("src",cover);

audio.src = url;

audio.play();

/* change icon */
$("#playPauseBtn i")
.removeClass("fa-play")
.addClass("fa-pause");

});


/* PLAY / PAUSE */
$("#playPauseBtn").click(function(){

if(audio.paused){

audio.play();
$("#playPauseBtn i").removeClass("fa-play").addClass("fa-pause");

}else{

audio.pause();
$("#playPauseBtn i").removeClass("fa-pause").addClass("fa-play");

}

});


/* FORWARD 10 SEC */
$("#forwardBtn").click(function(){

audio.currentTime += 10;

});


/* BACKWARD 10 SEC */
$("#backwardBtn").click(function(){

audio.currentTime -= 10;

});


/* UPDATE PROGRESS BAR */
audio.addEventListener("timeupdate",function(){

if(audio.duration){

let progress = (audio.currentTime / audio.duration) * 100;

$("#progressBar").val(progress);

$("#currentTime").text(formatTime(audio.currentTime));

$("#duration").text(formatTime(audio.duration));

}

});


/* SEEK BAR */
$("#progressBar").on("input",function(){

audio.currentTime = audio.duration * ($(this).val() / 100);

});


function formatTime(sec){

if(isNaN(sec)) return "0:00";

let m = Math.floor(sec/60);
let s = Math.floor(sec%60);

if(s<10) s="0"+s;

return m+":"+s;

}

});

</script>



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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).on('click', '.deleteRelease', function () {

    let release_id = $(this).data('id');

    Swal.fire({
        title: 'Are you sure?',
        text: "This will move data, song & cover image in Trash!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, Trash it!'
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: "<?= site_url('admin/album/release_delete'); ?>",
                type: "POST",
                data: { release_id: release_id },
                dataType: "json",
                success: function (res) {

                    if (res.status == 1) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: res.msg,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            location.reload();
                        }, 1500);

                    } else {

                        Swal.fire('Error!', res.msg, 'error');

                    }

                },
                error: function () {
                    Swal.fire('Error!', 'Something went wrong!', 'error');
                }
            });

        }

    });

});


</script>
<?php $this->load->view("bottom_application");?>