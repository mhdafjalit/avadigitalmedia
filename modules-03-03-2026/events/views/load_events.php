<?php
$sl = $offset;
if(is_array($res_events) && !empty($res_events)){
	foreach($res_events as $key=>$val){
	?>
		<div class="col-lg-6 listpager">
			<?php $this->load->view('events/events_item',array('val'=>$val));?>
		</div>
	<?php
	}
}