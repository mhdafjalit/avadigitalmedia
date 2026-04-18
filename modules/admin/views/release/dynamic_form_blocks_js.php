<?php
/* Config Vars for dynamic form fields */
$max_rows = $this->config->item('total_addmore_limit');

?>
<script type="text/javascript">
  $(document).ready(function(){
  	
	/************************ Add More URLs Code Starts here **************/

  $('.zx_add_more').on('click',function(e){
  	e.preventDefault();
  	var cobj = $(this);
  	var zx_root_parent_container = cobj.parents('.zx_root_parent_container');
  	var append_container_selector = cobj.data('clone-container');
  	var clone_selector = 'zx_clone_child';
  	var toAppObj = $('#'+append_container_selector);
	  var cloneObj = $('.'+clone_selector+':eq(0)',toAppObj).clone(true,true);
	  var curr_clone_count = $('.'+clone_selector,toAppObj).length;
	  $('input,file,select',cloneObj).val('');
	  cloneObj.find('.custom-file-label').html('');
	  $(':checked',cloneObj).prop('checked',false);
	  cloneObj.find('.x_floor_img_view').remove();
	  $('.required',cloneObj).remove();
	   toAppObj.append(cloneObj);
	   curr_clone_count = $('.'+clone_selector,toAppObj).length;
	   if(curr_clone_count>1){
	  	toAppObj.find('.remove_rows').removeClass('d-none');
	  }
	  zx_root_parent_container.find('.num_clone_rows').val(curr_clone_count);
	   if(curr_clone_count >= <?php echo $max_rows;?>){
			cobj.hide();
	  }
  });
	

	$(document).on('click','.remove_rows',function(e){
	   e.preventDefault();
	   var cobj = $(this);
	   var zx_root_parent_container = cobj.parents('.zx_root_parent_container');
	   var curr_clone_count = zx_root_parent_container.find('.zx_clone_child').length;
	   var first_container;
	   if(curr_clone_count==1){
	   	first_container = zx_root_parent_container.find('.zx_clone_child');
	   	$('input,file,select',first_container).val('');
	   	$(':checked',first_container).prop('checked',false);
	   	cobj.addClass('d-none');
	   }else{
	   	cobj.parents('.zx_clone_child').remove();
	   	curr_clone_count = zx_root_parent_container.find('.zx_clone_child').length;
	   	if(curr_clone_count==1){
	   	 zx_root_parent_container.find('.remove_rows').addClass('d-none');
	   	}
	   }
	   curr_clone_count = zx_root_parent_container.find('.zx_clone_child').length;
	   zx_root_parent_container.find('.num_clone_rows').val(curr_clone_count);
	   if(curr_clone_count < <?php echo $max_rows;?>){
			zx_root_parent_container.find('.zx_add_more').show();
	   }
	});
	/************************ Add More foods Code Ends here **************/
  });
  
  /* On page load */
 (function ( $ ) {
	$('.zx_root_parent_container').each(function(m,n){
		if($('.zx_clone_child',n).length >= <?php echo $max_rows;?>){
			$(n).find('.zx_add_more').hide();
		}
	});
	

}( jQuery ));
</script>