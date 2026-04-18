<?php
function validation_message($style="")// by default On Page - set 'alert' for pop-up
{	

$processing_result=validation_errors();
if($processing_result!='')
	{	
	   if($style=="alert")
	   {	   

 ?>
<div id="alert_box">
  <div class="alert_area">
<div class="close"> <span onclick="$('#alert_box').remove();" class="txt">Close [x]</span> </div>
    <div style=" width:100%; text-align:left;">
      <?php
	      }
         ?>
      <div class="validation" >
        <div style="margin-bottom:6px;"> <strong><span class="red"> <?php echo lang('ERROR'); ?>!</span> <br />
          <?php echo lang('INVALID_ENTRIES'); ?></strong> </div>
        <div class="validation_msg" ><?php echo $processing_result; ?></div>
      </div>
      <?php 
		if($style=="alert")		{
		  ?>
    </div>
  </div>
</div>
<?php
		}
     } 
 }

 
function error_message($style="")// by default On Page - set 'alert' for pop-up
 {  

	  $ci = &get_instance();
	  $msgtypes=array('success','warning','error');
	  $msgtype='';
	  $msg='';
  foreach($msgtypes as $msgt)
	  {
		  $msg=$ci->session->flashdata($msgt);
	if($msg!='')
		  {
			  $msgtype=$msgt;
			  break;
		  }
	  }

 if( $msgtype!='' && $msg!='' )
   {	 
	 if($style=="alert")
	  {
  ?>
<div id="alert_box">
  <div class="alert_area">
 <div class="close"> <span onclick="$('#alert_box').remove();" class="txt">Close [x]</span> </div>
    <div style=" width:100%; text-align:left;">
     <?php
	  }
 ?>
      <div class="<?php echo $msgtype;?>" >
        <?php echo $msg;?>
        </div>
      <?php if($style=="alert")
		{
		  ?>
    </div>
  </div>
</div>
<?php
		}
    }   
  } 

function frontend_breadcrumb($title="",$crumbs=""){
	  $ci = CI();
	$title=@ucfirst($title);
	 
		if($ci->uri->rsegment(1)=="products" && $ci->uri->rsegment(2)=='index'){
			?>
			<p class="fr mt5"> 
		    <input name="" type="button" class="btn  btn3 radius-3 trans_eff" onclick="window.open('<?php echo site_url('cart') ?>','_parent');" value=" View Cart"> 
		    <input name="" type="button" class="btn  btn3 radius-3 trans_eff" onclick="window.open('<?php echo site_url('cart/checkout') ?>','_parent');" value=" Checkout"> 
		    </p>
			<?php 			
		}
	?>
	<div class="breadcrumb_outer hidden-xs">
<div class="container">
<ul class="breadcrumb">
<li><a href="<?php echo site_url()?>" itemprop="url" title="Home">Home</a></li>
		<?php
			if(@is_array($crumbs)){
				foreach($crumbs as $key=>$val){ 
					?>
				<li><a href="<?php echo site_url($val)?>" itemprop="url" title="<?php echo $key?>"><?php echo $key?></a></li>
					<?php
				}
			}else if($crumbs!=""){
				echo $crumbs;
			}
		?>		
		<li><?php echo $title?></li>
</ul>
</div>
</div>
	<?php 
}

function print_no_record($len=0,$mess=""){			

	if($len==0){

		echo '<div class=" b pt5 red" style=\'text-align:center\'>';

		echo ($mess=="")?"No record found.":$mess;

		echo "</div>";

	}

}

if(!function_exists('req_compose_errors')){
	function req_compose_errors($custom_error_flds=array()){
		$ci = &get_instance();
		$error_array=array();
		$err_frm_flds = $ci->form_validation->error_array();
		if(is_array($err_frm_flds)){
			foreach($err_frm_flds as $key=>$val)
			{
				$error_array[$key] = $val;
			}
		}
		if(!empty($custom_error_flds)){
			foreach($custom_error_flds as $key=>$val)
			{
				$error_array[$key] = $val;
			}
		}
		return $error_array;
	}
}


