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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<style>
.switch{
position:relative;
display:inline-block;
width:50px;
height:24px;
}
.switch input{display:none;}

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
background:#e40975;
}

input:checked + .slider:before{
transform:translateX(26px);
}

.action_icon img{
cursor:pointer;
}

.modal-theme-header{
background:linear-gradient(45deg,#7b1fa2,#9c27b0);
color:#fff;
border-bottom:none;
}

.modal-content{border-radius:10px;}

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

<?php echo form_open("",'method="get"');?>


<div class="row">

<div class="col-md-4">
<input type="text" name="keyword" class="form-control"
value="<?php echo $posted_keyword;?>"
placeholder="Search Designation">
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

<a href="<?php echo site_url('admin/list_designation');?>" class="btn btn-danger w-100">
Clear
</a>

<?php } ?>

</div>

<div class="col-md-2 text-end">

<button type="button"
class="create_top text-white rounded-5 fw-medium"
data-bs-toggle="modal"
data-bs-target="#addDesignationModal">

+ Add Designation

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
<th width="80">Dept Sr</th>
<th>Department</th>
<th width="80">Desig Sr</th>
<th>Designation Name</th>
<th width="120">Status</th>
<th width="120">Action</th>
</tr>
</thead>


<tbody>

<?php 

if(!empty($res)){

$departments = [];
foreach($res as $row){
    $departments[$row['department_name']][] = $row;
}

$dept_sr = 1;

foreach($departments as $dept_name => $designations){

$desig_sr = 1;
$first = true;

foreach($designations as $des){

?>

<tr>

<td>
<?php if($first){ echo $dept_sr; } ?>
</td>

<td>
<?php if($first){ ?>
<b><?php echo $dept_name; ?></b><br>
<small><?php echo $des['department_description']; ?></small>
<?php } ?>
</td>

<td><?php echo $desig_sr; ?></td>

<td>
<b><?php echo $des['designation_name']; ?></b><br>
<small><?php echo $des['designation_description']; ?></small>
</td>

<td>

<label class="switch">

<input type="checkbox"
class="status_toggle"
data-id="<?php echo md5($des['designation_id']); ?>"
<?php echo ($des['status']==1)?'checked':'';?>>

<span class="slider"></span>

</label>

</td>

<td class="action_icon">

<a href="javascript:void(0)"
class="edit_designation"
data-id="<?php echo md5($des['designation_id']); ?>">

<img src="<?php echo theme_url();?>images/edit.svg" width="18">

</a>

&nbsp;&nbsp;

<a href="javascript:void(0)"
data-url="<?php echo site_url('admin/delete_designation/'.md5($des['designation_id'])); ?>"
class="delete_designation">

<img src="<?php echo theme_url();?>images/delete.svg" width="20">

</a>

</td>

</tr>

<?php

$desig_sr++;
$first=false;

}

$dept_sr++;

}

}else{

echo '<tr><td colspan="6" class="text-center">'.$this->config->item('no_record_found').'</td></tr>';

}

?>

</tbody>



</table>

</div>

</div>

</div>
</div>
</div>
</div>



<!-- ADD DESIGNATION MODAL -->

<div class="modal fade" id="addDesignationModal">

<div class="modal-dialog modal-lg modal-dialog-centered">

<div class="modal-content">

<div class="modal-header modal-theme-header">

<h5 class="modal-title">Add Designation</h5>

<button type="button" class="close text-white" data-dismiss="modal">
<span>&times;</span>
</button>

</div>

<form method="post" action="<?php echo site_url('admin/create_designation');?>">
<input type="hidden"
name="<?php echo $this->security->get_csrf_token_name();?>"
value="<?php echo $this->security->get_csrf_hash();?>">
<div class="modal-body p-4">



<div class="form-group mb-3">

<label class="form-label">Department *</label>

<select class="form-select" name="department_id">

<option value="">Select Department</option>

<?php 
if(!empty($department)){
foreach($department as $dep){
?>

<option value="<?php echo $dep['department_id']; ?>"
<?php echo set_select('department',$dep['department_id']); ?>>

<?php echo $dep['department_name']; ?>

</option>

<?php 
}
}
?>

</select>

<?php echo form_error('department');?>

</div>


<div class="form-group mb-3">

<label><b>Designation Name</b></label>

<input type="text"
name="designation_name"
class="form-control"
required>

</div>

<div class="form-group mb-3">

<label><b>Description</b></label>

<textarea
name="designation_description"
class="form-control"></textarea>

</div>

<div class="form-group">

<label class="switch">

<input type="checkbox"
name="status"
value="1"
checked>

<span class="slider"></span>

</label>

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
Create Designation
</button>

</div>

</form>

</div>
</div>
</div>



<!-- EDIT DESIGNATION MODAL -->

<div class="modal fade" id="editDesignationModal">

<div class="modal-dialog modal-lg modal-dialog-centered">

<div class="modal-content">

<div class="modal-header modal-theme-header">

<h5 class="modal-title">Edit Designation</h5>

<button type="button" class="close text-white" data-dismiss="modal">
<span>&times;</span>
</button>

</div>

<form method="post" action="<?php echo site_url('admin/update_designation');?>">

<input type="hidden" name="designation_id" id="edit_designation_id">
<input type="hidden"
name="<?php echo $this->security->get_csrf_token_name();?>"
value="<?php echo $this->security->get_csrf_hash();?>">





<div class="modal-body p-4">


<div class="form-group mb-3">

<label class="form-label">Department *</label>

<select class="form-select" name="department_id" id="edit_department_id">

<option value="">Select Department</option>

<?php 
if(!empty($department)){
foreach($department as $dep){
?>

<option value="<?php echo $dep['department_id']; ?>">

<?php echo $dep['department_name']; ?>

</option>

<?php 
}
}
?>

</select>


<?php echo form_error('department');?>

</div>


<div class="form-group mb-3">

<label><b>Designation Name</b></label>

<input type="text"
name="designation_name"
id="edit_designation_name"
class="form-control">

</div>

<div class="form-group mb-3">

<label><b>Description</b></label>

<textarea
name="designation_description"
id="edit_designation_description"
class="form-control"></textarea>

</div>

<div class="form-group">

<label class="switch">

<input type="checkbox"
name="status"
id="edit_status"
value="1">

<span class="slider"></span>

</label>

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
Update Designation
</button>

</div>

</form>

</div>
</div>
</div>


<?php if($this->session->flashdata('success')){ ?>

<script>

swal({
title:"Success",
text:"<?php echo $this->session->flashdata('success');?>",
icon:"success",
button:"OK"
});

</script>

<?php } ?>


<?php if($this->session->flashdata('error')){ ?>

<script>

swal({
title:"Error",
text:"<?php echo $this->session->flashdata('error');?>",
icon:"error",
button:"OK"
});

</script>

<?php } ?>


<script>

$(document).ready(function(){

/* DELETE DESIGNATION */

$(document).on("click",".delete_designation",function(){

var url = $(this).data("url");

swal({
title: "Are you sure?",
text: "This designation will be permanently deleted!",
icon: "warning",
buttons: ["Cancel", "Yes Delete"],
dangerMode: true,
})
.then((willDelete) => {

if (willDelete) {

window.location.href = url;

} else {

swal("Deletion Cancelled");

}

});

});


/* EDIT DESIGNATION */

$(document).on("click",".edit_designation",function(){

var id = $(this).data("id");

$.ajax({

url:"<?php echo site_url('admin/edit_designation/');?>"+id,
type:"GET",
dataType:"json",

success:function(data){

$("#edit_designation_id").val(data.designation_id);

$("#edit_department_id").val(data.department_id);   // IMPORTANT

$("#edit_designation_name").val(data.designation_name);

$("#edit_designation_description").val(data.designation_description);

if(data.status == 1){

$("#edit_status").prop("checked",true);

}else{

$("#edit_status").prop("checked",false);

}

$("#editDesignationModal").modal("show");

}


});

});


/* STATUS CHANGE */

$(document).on("change",".status_toggle",function(){

var id = $(this).data("id");

var status = $(this).prop("checked") ? 'active':'deactive';

var url = "<?php echo site_url('admin/status_designation/');?>"+id+"?u_status="+status;

var checkbox = $(this);

swal({
title:"Change Status?",
text:"Designation status will be updated.",
icon:"warning",
buttons:["Cancel","Yes Update"]
})
.then((ok)=>{

if(ok){

window.location.href = url;

}else{

checkbox.prop("checked", !checkbox.prop("checked"));

}

});

});

});
</script>

<?php $this->load->view("bottom_application");?>