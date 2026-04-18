(function($){
function validateUpload(obj){
		var ret_obj = {is_valid:true};
		var regex_file = new RegExp('^video\/');
		var file_obj = obj.file;
		var file_size_mb = typeof file_obj==='undefined' ? 0 : (file_obj.size/(1024*1024));
		file_size_mb = file_size_mb.toFixed(2);
		var is_mandatory = typeof obj.mandatory!=='undefined' && obj.mandatory!='' ? obj.mandatory : 'Y';
		var max_size = typeof obj.max_size!=='undefined' ? parseInt(obj.max_size) : 0;
		var err=0;
		var err_msg="";
		if(!err && is_mandatory=='Y' && (typeof file_obj==='undefined' || file_obj.value=='')){
			err=1;
			err_msg="Please upload file first";
		}
		if(!err && !regex_file.test(file_obj.type)){
			err=1;
			err_msg="Please upload valid video file";
		}
		if(!err && max_size>0 && file_size_mb>max_size){
			err=1;
			err_msg="Please upload file not more than "+max_size+" MB";
		}
		if(err){
			ret_obj = {is_valid:false,err_msg:err_msg};
		}
		if(typeof file_obj!=='undefined'){
			ret_obj['has_browsed'] = file_obj.value!='' ? 1 : 0;
		}
		return ret_obj;
	}
	function uploadChunk(obj){
		return new Promise(function(resolve,reject){
			var fileobj = obj.fileobj;
			var inputFileObj = obj.inputFileObj;
			var $inputFileObj = $(inputFileObj);
			var progressBar_parent = $inputFileObj.data('progress-container');
			progressBar_parent = typeof progressBar_parent!=='undefined' && progressBar_parent!='' ? $(progressBar_parent).eq(0) : $inputFileObj.parent();
			progressBar_parent = !progressBar_parent.length ? document : progressBar_parent;
			var reader = obj.frobj;
			var succ_cbk = obj.success;
			var chunk_size = typeof obj.chunkSize!=='undefined' ? obj.chunkSize : 4*1024*1024;
			var bytesUploaded=0;
			var total_size=fileobj.size;

			function resetProgressBar(){
				var percent_txt=0;
				$('.progress-bar',progressBar_parent)
					.attr('style', 'width: ' + percent_txt + '%')
					.attr('aria-valuenow', percent_txt)
					.find('span')
					.html(percent_txt + '%');
				$(progressBar_parent).filter('.progress').hide();
			}

			function updateProgressBar(){
				//console.log(bytesUploaded,total_size);
				var percent = (bytesUploaded / total_size * 100);
				percent=percent>100 ? 100 : percent;
				percent_txt=percent.toFixed(2);
				$('.progress-bar',progressBar_parent)
					.attr('style', 'width: ' + percent_txt + '%')
					.attr('aria-valuenow', percent_txt)
					.find('span')
					.html(percent_txt + '%');
				$(progressBar_parent).filter('.progress').show();
				if(percent==100){
					setTimeout(function(){
						resetProgressBar();
					},200);
				}
				//console.info('Uploaded: ' + percent + '%');
			}
			function _uploadChunk(param){
				var file=param.file;
				var offset=param.offset;
				var range=param.range;
				var unique_key=param.unique_key;
				unique_key=typeof unique_key!=='undefined' && unique_key!='' ? unique_key : '';
				var is_eof = offset >= file.size;
				// prepare reader with an event listener
				reader.addEventListener('load', function(e) {
					var filename = file.name;
					var index = offset / chunk_size;
					//var data = e.target.result.split(';base64,')[1];
					var data = new Uint8Array(e.target.result);
			 
					// build payload with indexed chunk to be sent
					var frmdata = new FormData();
					frmdata.append('file_upd',new Blob([data],{type:file.type}),file.name);
					frmdata.append('unique_key',unique_key);
					frmdata.append('filename',filename);
					frmdata.append('index',index);
					frmdata.append('is_eof',is_eof);
					
					// send payload, and buffer next chunk to be uploaded
					$.ajax({
						url:base_url+inputFileObj.dataset.url,
						type:'post',
						data:frmdata,
						headers:{XRSP:'json'},	
						contentType: false,
						processData: false,
						dataType:'json'
					}).done(function(data){
							var param_upload;
							if(data.status=='success'){
								bytesUploaded=offset;
								updateProgressBar();
								if(!is_eof){
									param_upload= {file:file,offset:(offset + range),range:chunk_size,unique_key:data.unique_key};
									_uploadChunk(param_upload);
								}else{
									inputFileObj.classList.add('completed');
									resolve({status:'success',filename:data.unique_key+'.'+data.ext});
								}
							}else{
								reject({status:'error',err_code:data.err_code,err_msg:data.err_msg});
							}
					}).fail(function(err){
							//console.log(err);
							reject({status:'error',err_code:'UNKNOWN',err_msg:'Failed to Upload'});
					});
				}, {once: true} ); // register as a once handler!
			 
				// chunk and read file data
				var chunk = file.slice(offset, offset + range);
				reader.readAsArrayBuffer(chunk);
			}
			var param_upload= {file:fileobj,offset:0,range:chunk_size};
			_uploadChunk(param_upload);
		});
	}
	window.validateUpload = validateUpload;
	window.uploadChunk = uploadChunk;
})(jQuery);