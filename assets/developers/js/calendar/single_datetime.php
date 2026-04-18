<?php
$_POST['start_date'] = '';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="ui/jquery-ui-1.8.23.custom.css" rel="stylesheet" type="text/css" >
	<script type="text/javascript" src="jquery-1.6.2.js"></script> 
	<script type="text/javascript">
		var gObj={'bspath':''};
	</script>
	
</head>
<body>
<div>
  
  Date<p class="fl ml5"><input name="start_date" class="start_date1" type="text" style="padding:2px; width:133px;" value="<?php echo $_POST['start_date'];?>"><a href="#" class="start_date"><img src="images/cal0.png" width="16" height="16" alt=""></a></p>
 
  <p class="fr">
	<input name="btn_sbt2" type="submit" class="btn1 btn_sbt2" value="Fetch Records" style="padding:0px 15px; height:23px; line-height:23px; font-size:11px;">
  </p>
 </div>
<form id="myform" action="" method="POST">
<input type="hidden" name="start_date" value="<?php echo $_POST['start_date'];?>" />

</form>
  <?php $default_date = date('Y-m-d',strtotime(date('Y-m-d',time())."-10 years"));?>
<script type="text/javascript" src="ui/jquery-ui-1.8.23.custom.min.js"></script>
<script language="javascript" src="ui/jquery-ui-timepicker-addon.js"></script>
<script language="javascript" src="ui/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
	$('.btn_sbt2').live('click',function(e){
		e.preventDefault();
		$start_date = $('.start_date1:eq(0)').val();
		
		$start_date = $start_date=='From' ? '' : $start_date;
		
		
		$(':hidden[name="start_date"]','#myform').val($start_date);
		
		$("#myform").submit();
	});
	$('.start_date').live('click',function(e){
	  e.preventDefault();
	  cls = 'start_date1';
	  $('.'+cls+':eq(0)').focus();
	});
	$( ".start_date1").live('focus',function(){
			$(this).datetimepicker({
			showOn: "focus",
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			defaultDate: 'y',
			buttonText:'',
			minDate:'<?php echo $default_date;?>' ,
			maxDate:'<?php echo date('Y-m-d',strtotime(date('Y-m-d',time())."+180 days"));?>',
			yearRange: "c-100:c+100",
			buttonImageOnly: true,
			
		});
	});
	
	  
  });
</script> 
</body>
</html>