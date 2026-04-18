<?php
$_POST['start_date'] = '';
$_POST['end_date'] = '';
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
  
  <p class="fl ml5"><input name="start_date" class="start_date1" type="text" style="padding:2px; width:133px;" value="<?php echo $_POST['start_date']=='' ? 'From' : $_POST['start_date'];?>"><a href="#" class="start_date"><img src="images/cal0.png" width="16" height="16" alt=""></a></p>
  <p class="fl ml5"><input name="end_date" class="end_date1" type="text" style="padding:2px; width:133px;" value="<?php echo $_POST['end_date']=='' ? 'To' : $_POST['end_date'];?>"><a href="#" class="end_date"><img src="images/cal0.png" width="16" height="16" alt=""></a></p>
  <p class="fr">
	<input name="btn_sbt2" type="submit" class="btn1 btn_sbt2" value="Fetch Records" style="padding:0px 15px; height:23px; line-height:23px; font-size:11px;">
  </p>
 </div>
<form id="myform" action="" method="POST">
<input type="hidden" name="start_date" value="<?php echo $_POST['start_date'];?>" />
<input type="hidden" name="end_date" value="<?php echo $_POST['end_date'];?>" />
</form>
  <?php $default_date = '2013-01-01';?>
<script type="text/javascript" src="ui/jquery-ui-1.8.23.custom.min.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
	$('.btn_sbt2').live('click',function(e){
		e.preventDefault();
		$start_date = $('.start_date1:eq(0)').val();
		$end_date = $('.end_date1:eq(0)').val();
		$start_date = $start_date=='From' ? '' : $start_date;
		$end_date = $end_date=='To' ? '' : $end_date;
		$(':hidden[name="keyword2"]','#myform').val($('input[type="text"][name="keyword2"]').val());
		$(':hidden[name="start_date"]','#myform').val($start_date);
		$(':hidden[name="end_date"]','#myform').val($end_date);
		$("#myform").submit();
	});
	$('.start_date,.end_date').live('click',function(e){
	  e.preventDefault();
	  cls = $(this).hasClass('start_date') ? 'start_date1' : 'end_date1';
	  $('.'+cls+':eq(0)').focus();
	});
	$( ".start_date1").live('focus',function(){
			$(this).datepicker({
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
			onSelect: function(dateText, inst) {
						  $('.start_date1').val(dateText);
						  $( ".end_date1").datepicker("option",{
							minDate:dateText ,
							maxDate:'<?php echo date('Y-m-d',strtotime(date('Y-m-d',time())."+180 days"));?>',
						});

					  }
		});
	});
	$( ".end_date1").live('focus',function(){
			$(this).datepicker({
					  showOn: "focus",
					  dateFormat: 'yy-mm-dd',
					  changeMonth: true,
					  changeYear: true,
					  defaultDate: 'y',
					  buttonText:'',
					  minDate:'<?php echo $_POST['start_date']!='' ? $_POST['start_date'] :  $default_date;?>' ,
					  maxDate:'<?php echo date('Y-m-d',strtotime(date('Y-m-d',time())."+180 days"));?>',
					  yearRange: "c-100:c+100",
					  buttonImageOnly: true,
					  onSelect: function(dateText, inst) {
						$('.end_date1').val(dateText);
					  }
				  });
	  });
	  
  });
</script> 
</body>
</html>