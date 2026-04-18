<?php
$ci = & get_instance();
if(!isset($has_footer) || $has_footer){
$this->load->view('project_footer');
}else{
	echo '<div class="clearfix"></div>';
}
if ($this->config->item('bottom.debug')){?>
<p class="mt5 mb5" align="center"><?php $this->output->enable_profiler($this->config->item('bottom.debug')); ?><p>
<?php } ?>
<script src="<?php echo resource_url();?>Scripts/bootstrap.js"></script>
<script type="text/javascript">
<?php /*Pass it from the controller to set page value*/?>
var Page='<?php echo (!isset($x_dsg_page) ? '' : $x_dsg_page);?>';
</script> 
<script type="text/javascript" src="<?php echo resource_url();?>Scripts/script.int.dg.js"></script>

<?php if(is_array($ci->inject_footer_js_files) && !empty($ci->inject_footer_js_files)){
	foreach($ci->inject_footer_js_files as $key=>$val){
		if($val['insert']==1){
			echo '<script type="text/javascript" src="'.$val['path'].'"></script>';
		}
	}
}
?>
<script type="text/javascript">
	<?php $login_change_trk_key = 'dt_px_'.md5($this->config->item('site_name'));?>
	if(window.localStorage){
		localStorage.setItem('<?php echo $login_change_trk_key;?>','<?php echo $this->userId;?>');
		function onStorageEvent(e){
			if(e.key=='<?php echo $login_change_trk_key;?>'){
				if(e.oldValue!=e.newValue){
					top.location.reload();
				}
			}
		}
		window.addEventListener('storage', onStorageEvent, false);
	}
</script>

<script>
// Session timeout handler - only for logged-in users
var sessionCheckInterval;
var sessionTimeoutUrl = site_url + 'user/check_session_status';

function initSessionMonitoring() {
    // Only initialize if user is logged in
    if($('body').hasClass('logged-in') || $('body').data('logged-in') === true) {
        if(sessionCheckInterval) {
            clearInterval(sessionCheckInterval);
        }
        
        sessionCheckInterval = setInterval(function() {
            $.ajax({
                url: sessionTimeoutUrl,
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    if(data.status == 'expired') {
                        clearInterval(sessionCheckInterval);
                        if(typeof swal !== 'undefined') {
                            swal({
                                title: 'Session Expired',
                                text: 'Your session has expired due to inactivity. Please login again.',
                                icon: 'warning',
                                button: 'OK'
                            }).then(function() {
                                window.location.href = site_url + 'login';
                            });
                        } else {
                            alert('Your session has expired due to inactivity. Please login again.');
                            window.location.href = site_url + 'login';
                        }
                    }
                },
                error: function() {
                    // Silently fail
                }
            });
        }, 60000); // Check every minute
    }
}

$(document).ready(function() {
    initSessionMonitoring();
});
</script>

</body>
</html>