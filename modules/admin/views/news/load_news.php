<?php
$sl = $offset=0;
if(is_array($res) && !empty($res)){
	foreach($res as $key=>$val){
	?>
		<div class="col-lg-6 listpager">
			<?php $this->load->view('news/news_item',array('val'=>$val));?>
		</div>
	<?php
	}
}