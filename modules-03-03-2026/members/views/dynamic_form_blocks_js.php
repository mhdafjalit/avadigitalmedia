<?php
/* Config Vars for dynamic form fields */
$max_rows = $this->config->item('total_addmore_limit');

?>
<script type="text/javascript">
  $(document).ready(function(){
  	
	/************************ Add More URLs Code Starts here **************/
	$('#add_more_rows').click(function(e){
	  e.preventDefault();	  
	  var toAppObj = $('#rows_container');
	  var cloneObj = $('.rows_clone_container:eq(0)').clone(true,true);
	  var curr_clone_count = $('.rows_clone_container').length;
	  cloneObj.find(':file').each(function(m,n){
	  	var $n = $(n);
	  	var new_name = n.name.replace(/^(attach_bill)(\d+)$/,"$1"+curr_clone_count);
	  	$n.prop('name',new_name);
	  });
	  $('input,file,select',cloneObj).val('');
	  cloneObj.find('.custom-file-label').html('');
	  cloneObj.find('.x_floor_img_view').remove();
	  $('.required',cloneObj).remove();
	  /*$('.bill_label_sibling',cloneObj).html(" "+(curr_clone_count+1));*/
	  toAppObj.append(cloneObj);
	  curr_clone_count = $('.rows_clone_container').length;
	  if(curr_clone_count>1){
	  	toAppObj.find('.remove_rows').removeClass('d-none');
	  }
	  $('#num_add_rows').val($('.rows_clone_container').length);
	  if($('.rows_clone_container').length >= <?php echo $max_rows;?>){
		$(this).hide();
	  }
	});
	

	$('#rows_container').on('click','.remove_rows',function(e){
	   e.preventDefault();
	   var cobj = $(this);
	   var curr_clone_count = $('.rows_clone_container').length;
	   var first_container;
	   if(curr_clone_count==1){
	   	first_container = $('.rows_clone_container');
	   	$('input,file,select',first_container).val('');
	   	$(':checked',first_container).prop('checked',false);
	   	cobj.addClass('d-none');
	   }else{
	   	$(this).parents('.rows_clone_container').remove();
	   	curr_clone_count = $('.rows_clone_container').length;
	   	if(curr_clone_count==1){
	   	 $('#rows_container').find('.remove_rows').addClass('d-none');
	   	}
	   }
	   $('#num_add_rows').val($('.rows_clone_container').length);
	   if($('.rows_clone_container').length < <?php echo $max_rows;?>){
		$('#add_more_rows').show();
	   }
	});
	/************************ Add More foods Code Ends here **************/
  });
  
   /* On page load */
 (function ( $ ) {
	
	if($('.rows_clone_container').length >= <?php echo $max_rows;?>){
		$('#add_more_rows').hide();
  	}

}( jQuery ));
</script>