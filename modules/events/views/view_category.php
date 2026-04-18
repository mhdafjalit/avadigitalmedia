<?php $this->load->view("top_application");
$this->load->view("banner/top_inner_banner");
$breadcrumb_arr = array();
if($parent_id>0){
	$cat_breadcrumb_arr = category_breadcrumb($parent_id,2,-1);
	$breadcrumb_arr = array_merge($breadcrumb_arr,$cat_breadcrumb_arr);
}
echo navigation_breadcrumb($heading_title,$breadcrumb_arr);
?>
<div class="mid_area">
	<div class="container">
		<div class="cms_area">
			<h1><?php echo $heading_title;?></h1>

			<div class="event_cate_list">
			<?php if(is_array($res_cat) && !empty($res_cat)){
				echo form_open($base_link,'id="myform" method="get" autocomplete="off" class="dn" data-total-rec="'.$total_rec.'"');
				echo '<input type="hidden" name="offset" value="'.$offset.'">';
				echo form_close();
			?>
			<ul>
			<?php $this->load->view('events/load_category');?>
			</ul>
			<div class="mt-2 mb-2 text-center dn" id="loadingdiv"><img src="<?php echo theme_url();?>images/loader.gif" alt=""></div>
			<?php }else{
				echo '<div class="text-center b">'.$this->config->item('no_record_found').'</div>';
			}?>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view("bottom_application");?>