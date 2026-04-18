$(function(){

    var ul = $('#my_upload ul');
	
	ul.sortable();
	
	var jx_waiting_queue = [];

    $('#drop a').click(function(){
        // Simulate a click on the file input button
        // to show the file browser dialog
				
        $(this).parent().find('input').click();
				
				
    });

    // Initialize the jQuery File Upload plugin
    $('#upload').each(function(m,n){
		var uploader_obj = $(n);
		
		uploader_obj.fileupload({
		
		formData: {tbl_section: uploader_obj.data('section')},

        // This element will accept file drag/drop uploading
        dropZone: $('#drop'),

        // This function is called when a file is added to the queue;
        // either via the browse button, or via drag/drop:
        add: function (e, data) {
			var total_browsed_images = ul.children().length;
			//alert(total_browsed_images);
			if(total_browsed_images < img_limit){
			   if(data.files[0].type.match('image.*')){
				   console.log(data.files[0]);
				   var imgsize = data.files[0].size/(1024*1024);//MB
				   if(imgsize <= 2){
					   var tpl = $('<li class="working post-photo"><div class="loader" style="color:#f00;text-align:center;"><img src="'+site_url+'assets/developers/images/loader2.gif" /></div></li>');
					   
					   data.context = tpl.appendTo(ul);
						
							

						total_browsed_images++;
	
						if(total_browsed_images >= img_limit){
							$('.toolsopen').hide();
						}
					   
					   var reader = new FileReader();
                		//Read the contents of Image File.
                		reader.readAsDataURL(data.files[0]);
                		reader.onload = function (e) {
                    		//Initiate the JavaScript Image object.
                    		var image = new Image();
                    		//Set the Base64 string return from FileReader as source.
                    		image.src = e.target.result;
                    		image.onload = function () {
                        		//Determine the Height and Width.
                        		var height = this.height;
                        		var width = this.width;
								if(width > gObj.min_image_width && height > gObj.min_image_height){//Change this dimension if required
								
								////////////////////////////////////////////////////////////
								
		
								// Initialize the knob plugin
								//tpl.find('input').knob();
		
								// Listen for clicks on the cancel icon
								tpl.find('span').click(function(){
		
									if(tpl.hasClass('working')){
										jqXHR.abort();
									}
		
									tpl.fadeOut(function(){
										tpl.remove();
									});
		
								});
		
								// Automatically upload the file once it is added to the queue
								var jqXHR = data.submit();
								var uploded_file_name='';
								var myinterval=setInterval(
								function() 
								{
									var uploded_file_name=jqXHR.responseText;
		
									if(uploded_file_name && uploded_file_name.length>10)
									{
										
										data.context.find('.loader').replaceWith("<img style='max-width:60px;height:56px;' src='"+image_url+"/"+uploded_file_name+"' class='uploaded_image' data-browse-type='add' data-imgname='"+uploded_file_name+"' /><img src='"+theme_url+"images/cross.png' alt='' class='abs' style='right:2px; top:2px;cursor:pointer;' onclick=\"unlink_image(this,'"+uploded_file_name+"')\">");
										//data.context.append('<span onclick="unlink_image(this,\''+uploded_file_name+'\')"></span>');
										clearInterval(myinterval);	
									}
								}
								,2000);
								
								
								
								//////////////////////////////////////////////////////////////
								}else{
									total_browsed_images--;
									data.context.remove();
									if(total_browsed_images < img_limit){
										$('.toolsopen').show();
									}
									//alert("Dimension is improper.Height >= "+gObj.min_image_height+" & Width >="+gObj.min_image_width);	
								}
								
								
								
							}
						}
					   
					   
					   
					   
					   
					   /* var tpl = $('<li class="working"><input type="text" value="'+data.files[0].name+'" data-width="48" data-height="48"'+
							' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><p></p>');*/

						

						
						

						// Append the file name and file size
					   // tpl.find('p').text(data.files[0].name);
									// .append('<i>' + formatFileSize(data.files[0].size) + '</i>');

						// Add the HTML to the UL element
						
				   }
			   }
			   else{
				
				alert("Some file(s) are not of image formats");
			   }


			}
			else{
				//jx_waiting_queue.push(data);
				$('.toolsopen').hide();
			}
        },

        progress: function(e, data){

            // Calculate the completion percentage of the upload
            var progress = parseInt(data.loaded / data.total * 100, 10);
           // alert(progress);

            // Update the hidden input field and trigger a change
            // so that the jQuery knob plugin knows to update the dial
            data.context.find('input').val(progress).change();

            if(progress == 100){
                data.context.removeClass('working');
            }
        },

        fail:function(e, data){
            // Something has gone wrong!
            data.context.addClass('error');
        }

    });
	});


    // Prevent the default action when a file is dropped on the window
    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

	// Helper function that formats the file sizes
    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }

        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }

        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }

        return (bytes / 1000).toFixed(2) + ' KB';
    }
	
	$('.db_del').click(function(){
		
		
		
		var lobj = $(this);
		
		var cfm = confirm('Are you sure you want to delete this');
		if(cfm){
			$.post(base_url+'member/unlink_edit_file',{id:lobj.data('id'),'section':lobj.data('section'),'sectionid':lobj.data('sectionid')},function(data){
				if(data=='success'){
					lobj.parent().remove();
					$('.toolsopen').show();
				}
			});
		}
	});

});

function unlink_image(obj,img)
{
	$(obj).parent().remove();
	$.post(base_url+'member/unlink_single_file',{'file_name':img},function(data){
		$('.toolsopen').show();
		//alert('deleted...');
		});
}
