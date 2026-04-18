
<?php 
$this->load->view('top_application'); 

$bdcm_array = array(
array('heading'=>'Dashboard','url'=>'admin')
);

$posted_keyword = $this->input->get_post('keyword',TRUE);
$posted_keyword = escape_chars($posted_keyword);

$posted_status = $this->input->get_post('status',TRUE);
$posted_status = escape_chars($posted_status); 
?>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<style>

.switch{
position:relative;
display:inline-block;
width:50px;
height:24px;
}

.switch input{
display:none;
}

.slider{
position:absolute;
cursor:pointer;
top:0;
left:0;
right:0;
bottom:0;
background:#ccc;
transition:.4s;
border-radius:34px;
}

.slider:before{
position:absolute;
content:"";
height:18px;
width:18px;
left:3px;
bottom:3px;
background:white;
transition:.4s;
border-radius:50%;
}

input:checked + .slider{
background:#ed0671;
}

input:checked + .slider:before{
transform:translateX(26px);
}

.action_icon img{
cursor:pointer;
}

/* MODAL THEME */

.modal-theme-header{
background:linear-gradient(45deg,#7b1fa2,#9c27b0);
color:#fff;
border-bottom:none;
}

.modal-content{
border-radius:10px;
}

.btn-purple{
background:#7b1fa2;
color:#fff;
border:none;
}

.btn-purple:hover{
background:#5e1780;
}

</style>


<div class="dash_outer">
<div class="dash_container">

<?php $this->load->view('view_left_sidebar'); ?>

<div id="main-content" class="h-100">

<?php $this->load->view('view_top_sidebar');?>

<div class="top_sec d-flex justify-content-between">
<h1 class="mt-4"><?php echo $heading_title;?></h1>
<?php echo navigation_breadcrumb($heading_title,$bdcm_array); ?>
</div>

<div class="main-content-inner">

<div class="bg-white p-3 mb-3 rounded-3">

<?php validation_message(); ?>

<?php
if(error_message() !=''){
echo error_message();
}
?>

<?php echo form_open("",'id="search_form" method="get" ');?>

<div class="row">

<div class="col-md-4">
<input type="text" name="keyword" class="form-control"
value="<?php echo $posted_keyword;?>"
placeholder="Search Roles">
</div>

<div class="col-md-2">

<select class="form-control" name="status">
<option value="">Status</option>
<option value="1" <?php echo $posted_status==='1' ? 'selected' : '';?>>Active</option>
<option value="0" <?php echo $posted_status==='0' ? 'selected' : '';?>>Inactive</option>
</select>

</div>

<div class="col-md-2">
<input type="submit" class="btn btn-purple w-100" value="Search">
</div>

<div class="col-md-2">

<?php if($posted_keyword!='' || $posted_status!=''){ ?>

<a href="<?php echo site_url('admin/list_role');?>" class="btn btn-danger w-100">
Clear
</a>

<?php } ?>

</div>

<div class="col-md-2 text-end">

<button type="button" class="create_top text-white rounded-5 fw-medium trans_eff align-middle" id="openRoleModal">
+ Add Role
</button>

</div>

</div>

<?php echo form_close();?>

</div>


<div class="white_bx">

<div class="table-responsive">

<table class="table table-bordered table-striped">

<thead>

<tr>
<th width="60">Sr</th>
<th>Role Name</th>
<th width="120">Role Status</th>
<th width="120">Action</th>
</tr>

</thead>

<tbody>

<?php 

$i=1;

if(!empty($res)){

foreach($res as $val){

?>

<tr>

<td><?php echo $i;?></td>

<td>
<b><?php echo $val['role_name'];?></b><br>
<small><?php echo $val['role_description'];?></small>
</td>

<td>

<label class="switch">

<input type="checkbox"
class="status_toggle"
data-id="<?php echo md5($val['role_id']);?>"
<?php echo ($val['status']==1) ? 'checked' : '';?>>

<span class="slider"></span>

</label>

</td>

<td class="action_icon">

<a href="javascript:void(0)"
class="edit_role"
data-id="<?php echo md5($val['role_id']);?>">
<img src="<?php echo theme_url();?>images/edit.svg" width="18">
</a>

&nbsp;&nbsp;

<a href="javascript:void(0)"
data-url="<?php echo site_url("admin/delete_role/".md5($val['role_id']));?>"
class="delete_role">

<img src="<?php echo theme_url();?>images/delete.svg" width="20">

</a>

</td>

</tr>

<?php 

$i++;

}

}else{

echo '<tr><td colspan="4" class="text-center">'.$this->config->item('no_record_found').'</td></tr>';

}

?>

</tbody>

</table>

</div>

</div>

<?php echo $page_links; ?>

</div>
</div>
</div>
</div>

<!-- EDIT ROLE MODAL -->

<div class="modal fade" id="editRoleModal">

<div class="modal-dialog modal-lg modal-dialog-centered">

<div class="modal-content">

<div class="modal-header modal-theme-header">

<h5 class="modal-title">Edit Role</h5>

<button type="button" class="close text-white" data-dismiss="modal">
<span>&times;</span>
</button>

</div>

<form method="post" action="<?php echo site_url('admin/update_role');?>">

<input type="hidden" name="role_id" id="edit_role_id">
<input type="hidden"
name="<?php echo $this->security->get_csrf_token_name();?>"
value="<?php echo $this->security->get_csrf_hash();?>">
<div class="modal-body p-4">

<div class="form-group mb-3">
<label><b>Role Name</b></label>
<input type="text" name="role_name" id="edit_role_name" class="form-control" required>
</div>

<div class="form-group mb-3">
<label><b>Role Description</b></label>
<textarea name="role_description" id="edit_role_description" class="form-control"></textarea>
</div>

<div class="form-group">

<label class="d-block"><b>Status</b></label>

<label class="switch">
<input type="checkbox" name="status" id="edit_status" value="1">
<span class="slider"></span>
</label>

</div>

</div>

<div class="modal-footer">

<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
Cancel
</button>

<button type="submit" class="btn btn-purple">
Update Role
</button>

</div>

</form>

</div>
</div>
</div>

<!-- ADD ROLE MODAL -->

<div class="modal fade" id="addRoleModal">

<div class="modal-dialog modal-lg modal-dialog-centered">

<div class="modal-content">

<div class="modal-header modal-theme-header">

<h5 class="modal-title">Add New Role</h5>

<button type="button" class="close text-white" data-dismiss="modal">
<span>&times;</span>
</button>

</div>

<form method="post" action="<?php echo site_url('admin/create_role');?>">

<input type="hidden"
name="<?php echo $this->security->get_csrf_token_name();?>"
value="<?php echo $this->security->get_csrf_hash();?>">

<div class="modal-body p-4">

<div class="form-group mb-3">
<label><b>Role Name</b></label>
<input type="text" name="role_name" class="form-control" required>
</div>

<div class="form-group mb-3">
<label><b>Role Description</b></label>
<textarea name="role_description" class="form-control"></textarea>
</div>

<div class="form-group">

<label class="d-block"><b>Status</b></label>

<label class="switch">
<input type="checkbox" name="status" value="1" checked>
<span class="slider"></span>
</label>

<span class="ml-2 text-success font-weight-bold">Active</span>

</div>

</div>

<div class="modal-footer">

<button type="button"
class="btn btn-outline-secondary"
data-dismiss="modal">
Cancel
</button>

<button type="submit"
class="btn btn-purple">
Create Role
</button>

</div>

</form>

</div>
</div>
</div>

<script>
  /* EDIT ROLE */

$(".edit_role").click(function(){

var id = $(this).data("id");

$.ajax({

url:"<?php echo site_url('admin/edit_role/');?>"+id,
type:"GET",
dataType:"json",

success:function(data){

$("#edit_role_id").val(data.role_id);
$("#edit_role_name").val(data.role_name);
$("#edit_role_description").val(data.role_description);

if(data.status == 1){
$("#edit_status").prop("checked",true);
}else{
$("#edit_status").prop("checked",false);
}

$("#editRoleModal").modal("show");

}

});

});
</script>

<script>

$(document).ready(function(){

$("#openRoleModal").click(function(){

$("#addRoleModal").modal("show");

});


$(".delete_role").click(function(){

var url=$(this).data("url");

swal({
title:"Are you sure?",
text:"This role will be permanently deleted!",
icon:"warning",
buttons:true,
dangerMode:true
}).then((willDelete)=>{

if(willDelete){

window.location.href=url;

}

});

});


$(".status_toggle").change(function(){

var id=$(this).data("id");

var status=$(this).prop("checked") ? 'active':'deactive';

var url="<?php echo site_url('admin/status_role/');?>"+id+"?u_status="+status;

swal({
title:"Confirm Status Change?",
text:"Role status will be updated.",
icon:"warning",
buttons:true
}).then((ok)=>{

if(ok){

window.location.href=url;

}else{

location.reload();

}

});

});


});

</script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<?php if($this->session->flashdata('success')){ ?>
<script>
swal({
title: "Success",
text: "<?php echo $this->session->flashdata('success'); ?>",
icon: "success",
button: "OK"
});
</script>
<?php } ?>

<?php if($this->session->flashdata('error')){ ?>
<script>
swal({
title: "Error",
text: "<?php echo $this->session->flashdata('error'); ?>",
icon: "error",
button: "OK"
});
</script>
<?php } ?>

<?php $this->load->view("bottom_application");?>

