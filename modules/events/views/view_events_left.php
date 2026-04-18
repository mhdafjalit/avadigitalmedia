<?php
/*Posted Data*/
$posted_from_date = $this->input->get_post('from_date',TRUE);
$posted_from_date = escape_chars($posted_from_date);
$posted_to_date = $this->input->get_post('to_date',TRUE);
$posted_to_date = escape_chars($posted_to_date);
$posted_sort_by = $this->input->get_post('sort_by',TRUE);
$posted_sort_by = escape_chars($posted_sort_by);
$show_clear_all = ($posted_from_date!='' || $posted_to_date!='' || $posted_sort_by!=$default_sort_by || $category_id>0) ? 1 : 0;
/*Posted Data ends*/
//Our Category
$where_event_category = "cat.status='1' AND cat.cat_type='2' AND parent_id='0'";
$params_event_category = array(
									'fields'=>"cat.category_id,cat.category_name,cat.friendly_url",
									'from'=>'wl_categories as cat',
									'orderby'=>'cat.sort_order',
									'limit'=>8,
									'where'=>$where_event_category,
									'debug'=>FALSE
									);
$res_event_category   = $this->utils_model->custom_query_builder($params_event_category);
$total_event_category = $this->utils_model->total_rec_found;
?>
<div class="list_left">
	<?php if($show_clear_all){?>
	<p class="fs11 float-right clear_all"><a href="<?php echo site_url('events');?>" class="uu ">Clear All</a></p>
	<?php }?>
	<h2 class="fs14 d-none d-lg-block"><b>Filter Results</b></h2>
	<h2 class="d-md-block d-lg-none showhide hand">Filter Results <i class="fas fa-bars" aria-hidden="true"></i><p class="clearfix"></p></h2>

	<div class="filter_dis">
		<?php if(!empty($res_event_category)){?>
		<div class="flter_bx">
			<div class="filt_hed showhide">Categories</div>
			<div id="categories_list" class="list_area">
				<div class="scroll_bar" id="style1">
					<?php foreach($res_event_category as $key1=>$val1){
					$escaped_title=escape_chars($val1['category_name']);
					?>
					<p class="left_attribute1"><a href="<?php echo site_url($val1['friendly_url']);?>" title="<?php echo $escaped_title;?>"><?php echo char_limiter($val1['category_name'],50);?> </a></p>
					<?php }?>
				</div>
			</div>
		</div>
		<?php }?>
		<?php echo form_open($base_link,'id="myform1" method="get" autocomplete="off"');?>
		<!--Date-->
		<div class="flter_bx">
			<div class="filt_hed showhide">Date Range</div>
			<div class="dis_cate">
				<div class="date_range">
					<p class="mt-1">
					<label>Date From :</label>
					<input type="text" name="from_date" id="evt_start_date1"  placeholder="From" class="p5" value="<?php echo $posted_from_date;?>" style="width:90%;">
					</p>
					<p class="mt-1">
					<label>Date To :</label>
					<input type="text" name="to_date" id="evt_end_date1" placeholder="To" class="p5" value="<?php echo $posted_to_date;?>" style="width:90%;">
					</p>
					<div class="clearfix"></div>
					<div class="mt-2">
						<input type="submit" name="button" id="button" value="Submit" class=" btn-sm">
					</div>
				</div>
			</div>
		</div>
		<!--Date-->
		<?php echo form_close();?>
	</div>
</div>
<?php echo form_open($base_link,'id="myform" method="get" autocomplete="off" data-total-rec="'.$total_rec.'"');
echo '<input type="hidden" name="offset" value="'.$offset.'">';
if($posted_from_date!=''){
	echo '<input type="hidden" name="from_date" value="'.$posted_from_date.'">';
}
if($posted_to_date!=''){
	echo '<input type="hidden" name="to_date" value="'.$posted_to_date.'">';
}
if($posted_sort_by!=''){
	echo '<input type="hidden" name="sort_by" value="'.$posted_sort_by.'">';
}
echo form_close();
?>