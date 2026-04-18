<?php $this->load->view('top_application'); 
$site_title_text = escape_chars($this->config->item('site_name'));?>
<link href="<?php echo theme_url();?>css/jquery.jqplot.css" rel="stylesheet">
<div class="dash_outer">
	<div class="dash_container">
		<?php $this->load->view('view_left_sidebar'); ?>
		<div id="main-content" class="h-100">
			<?php $this->load->view('view_top_sidebar');?>
			<div class="d-flex justify-content-between top_sec">
				<div class="float-lg-start page_heading">
					<div>
						<h1><?php echo $heading_title;?></h1>
						<small>Welcome to <?php echo $site_title_text;?></small>
					</div>
				</div>
				<div class="float-end">
					<?php echo navigation_breadcrumb($heading_title); ?>
				</div>
			</div>
			<div class="main-content-inner">
				<?php
				$album_types = $this->config->item('album_types');
				if(is_array($album_types) && !empty($album_types)){?>
				<div class="album_tab">
					<ul class="nav nav-pills justify-content-center mb-3" id="pills-tab" role="tablist">
						<?php
						foreach ($album_types as $key => $val) {
							echo '<li class="nav-item" role="presentation">
								<button class="nav-link '.(($key==1)? ' active':'').'" id="pills-'.$key.'-tab" data-bs-toggle="pill" data-bs-target="#pills-'.$key.'" type="button" role="tab" aria-controls="pills-'.$key.'" aria-selected="true" '.(($key==2)? 'disabled="disabled"':'').'><img src="'.theme_url().'images/'.(($key==1)? 'audio':'video').'-tab-ico.svg" alt=""> '.$val.' Album</button>
							</li>';
						}?>
					</ul>
				</div>
				<div class="tab-content" id="pills-tabContent">
					<?php
					foreach ($album_types as $key => $val) {
						echo '<div class="tab-pane fade '.(($key==1)? ' show active':'').'" id="pills-'.$key.'" role="tabpanel" aria-labelledby="pills-'.$key.'-tab" tabindex="0">
							<div id="owl-'.(($key==1)? 'music':'video').'" class="owl-carousel owl-theme mt-2 scroll_arr">
								<div class="item">
								<div class="count_box count_graph1 position-relative overflow-hidden hand trans_eff" onClick="window.open(\''.site_url('admin/metas').'?album_type='.$key.'\',\'_parent\')">
								<p class="count_title purple fw-bold mb-1">Created Album</p>
								<div class="d-flex">
									<img src="'.theme_url().'images/album-ico.svg" alt="" width="39" height="32" class="count_img">
									<p class="count_total">
										<b class="d-block fw-semibold purple">'.(($key==1) ? $total_music_releases : $total_video_releases).'</b>Total
									</p>
								</div>
								</div>
								</div>
								<div class="item">
								<div class="count_box count_graph2 position-relative overflow-hidden hand trans_eff" onClick="window.open(\''.site_url('admin/metas').'?t=p&album_type='.$key.'\',\'_parent\')">
								<p class="count_title purple fw-bold mb-1">Processing Album</p>
								<div class="d-flex">
									<img src="'.theme_url().'images/album-ico.svg" alt="" width="39" height="32" class="count_img2">
									<p class="count_total">
										<b class="d-block fw-semibold purple">'.(($key==1) ? $total_music_process_releases : $total_video_process_releases).'</b>Total
									</p>
								</div>
								</div>
								</div>
								<div class="item">
								<div class="count_box count_graph3 position-relative overflow-hidden hand trans_eff" onClick="window.open(\''.site_url('admin/metas').'?t=f&album_type='.$key.'\',\'_parent\')">
								<p class="count_title purple fw-bold mb-1">Final Release Album</p>
								<div class="d-flex">
								<img src="'.theme_url().'images/album-ico.svg" alt="" width="39" height="32" class="count_img3">
									<p class="count_total">
										<b class="d-block fw-semibold purple">'.(($key==1) ? $total_music_final_releases : $total_video_final_releases).'</b>Total
									</p>
								</div>
								</div>
								</div>
								<div class="item">
								<div class="count_box count_graph1 position-relative overflow-hidden hand trans_eff disabled" style="pointer-events:none;" onClick="window.open(\''.site_url('members/album/rejected_album').'?album_type='.$key.'\',\'_parent\')">
								<p class="count_title purple fw-bold mb-1">Correction Required </p>
								<div class="d-flex">
									<img src="'.theme_url().'images/album-ico.svg" alt="" width="39" height="32" class="count_img">
									<p class="count_total">
										<b class="d-block fw-semibold purple">'.(($key==1) ? $total_music_rejected_releases : $total_video_rejected_releases).'</b>Total
									</p>
								</div>
								</div>
								</div>
								<div class="item">
									<div class="count_box count_graph2 position-relative overflow-hidden hand trans_eff disabled" style="pointer-events:none;" onClick="window.open(\''.site_url('members/album/takedown_album').'?album_type='.$key.'\',\'_parent\')">
										<p class="count_title purple fw-bold mb-1">Taken down tracks</p>
										<div class="d-flex">
											<img src="'.theme_url().'images/album-ico.svg" alt="" width="39" height="32" class="count_img2">
											<p class="count_total">
												<b class="d-block fw-semibold purple">'.(($key==1) ? $total_music_takedown_releases : $total_video_takedown_releases).'</b>Total
											</p>
										</div>
									</div>
								</div>
							</div>  
					 	</div>';
				 	}?>
				</div>
				<?php }?>
				<div class="mt-2">
					<div class="row g-0">
						<div class="col-lg-12 pe-lg-4">
							<div class="fin_box text-white">
								<p class="float-start fw-bold">Financial Details</p>
								<p class="float-end">
									<a href="<?php echo site_url('members/wallet/payment_request');?>" class="text-white fs-8 fw-bold text-uppercase">View More</a>
								</p>
								<p class="clearfix"></p>
								<div class="row g-0">
									<div class="col-lg-8">
										<div class="row g-3 mt-1">
											<div class="col-sm-4">
												<div class="bg-white text-black text-center rounded-2 pt-2 pb-3">
													<p class="mb-1 fs-4 fw-bold"><img src="<?php echo theme_url();?>images/fin-ico1.svg" alt="" height="35" class="mb-1"><br><?php echo display_price($total_earning);?></p>
													<p class="fs-7 fw-bold">Total Earning</p>
												</div>
											</div>
											<div class="col-sm-4 text-center">
												<div class="border rounded-3 p-1">
													<b class="fs-4"><?php echo display_price($balance_amt);?></b>
													<p class="fs-7">Balance Amount</p>
												</div>
												<div class="border rounded-3 p-1 mt-1">
													<b class="fs-4 d-block"><?php echo display_price($commission_amt);?></b>
													<p class="fs-7">Commission Amount</p>
												</div>
											</div>
											<div class="col-sm-4">
												<div class="bg-white text-black text-center rounded-2 pt-2 pb-3">
													<p class="mb-1 fs-4 fw-bold"><img src="<?php echo theme_url();?>images/money_icon.svg" alt="" height="35" class="mb-1"><br><?php echo display_price($total_dabit);?></p>
													<p class="fs-7 fw-bold">Total Withdrawal</p>
												</div>
											</div>
										</div>
										<p class="mt-4 text-center fin_btn">
											<a href="<?php echo site_url('members/wallet/payment_request');?>" title="Request Amount">Request Amount</a> 
											<a href="#" title="Request Amount">Convert in INR</a>
										</p>
									</div>
									<div class="col-lg-4 financial_sec">
										<div class="graph_w">
											<div id="scheme_stats" class="position-relative" style="height:160px; width:160px; margin:auto;">
												<p class="financial_score"><b><?php echo display_price($balance_amt);?></b><br>Available Amount</p>
											</div>
										</div>
										<div class="d-flex justify-content-between">
											<div class="stats_dtl pb-0 border-0 d-flex fs-8">
												<strong class="abt_stat_dot" style="background:#ff59a0"></strong>
												<p class="abt_stat_txt">Earning <br>Amount</p>
											</div>
											<div class="stats_dtl pb-0 border-0 d-flex fs-8">
												<strong class="abt_stat_dot" style="background:#00a8ff"></strong>
												<p class="abt_stat_txt">Withdrawal <br>Amount</p>
											</div>
											<div class="stats_dtl pb-0 border-0 d-flex fs-8">
												<strong class="abt_stat_dot" style="background:#fac248"></strong>
												<p class="abt_stat_txt">Commission <br>Amount</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>


				<div class="top_paid_box">
					<div class="float-sm-start">
						<p class="heading text-white">Release Overview</p>
						<p class="fs-8 text-white mt-1">My days wise Release!!</p>
					</div>
					<div class="float-sm-end">
						<?php
						//$months_range = $this->config->item('config_months_range');
						$months_range = $this->config->item('current_year_months_range');?>
						<select class="form-select" name="release_month" id="release_month">
							<?php 
							foreach ($months_range as $month) {
		            echo '<option value='.$month.' '.(($month==date("n"))? 'selected' :'').'>' . date("F", mktime(0, 0, 0, $month, 1)) . '</option>';
			        }?>
						</select>
					</div>
					<p class="clearfix"></p>
					<div class="bg-white p-3 rounded-3 mt-3 graph1">
						<div id="monthly_graph" style="width:99%;height:300px"></div>
					</div>
				</div>
				<div class="row g-3">
					<div class="col-lg-6">
						<div class="top_paid_box">
							<p class="heading text-white">Top Paid Stores</p>
							<div class="position-relative" id="product_stats" style="height:310px"></div>
							<p><a href="<?php echo site_url('members/wallet/revenue_graph');?>" class="text-primary fs-8 fw-bold text-uppercase">View More</a></p>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="dash_box p-3 mt-3">
							<p class="float-start heading">Top 10 Paid Tracks</p>
							<a href="<?php echo site_url('members/release/paid_tracks');?>" title="View More" class="mt-1 float-end text-primary fs-8 fw-bold text-uppercase">View More</a>
							<p class="clearfix"></p>
							<div class="track_scrollbar track_list mt-3">

							<div class="d-flex justify-content-between text-center pt-2 pb-2 border-bottom">
									No records found.
								</div>

								<!-- <div class="d-flex justify-content-between pt-2 pb-2 border-bottom">
									<div class="d-flex">
										<div class="list_pic text-center overflow-hidden rounded-3">
											<figure class="align-middle d-table-cell">
												<img src="<?php echo theme_url();?>images/draft-pic1.jpg" alt="" width="60" height="60" class="mw-100 mh-100">
											</figure>
										</div>
										<div class="ms-2 mt-1">
											<p class="fs-7 fw-bold purple">Ho Jati Dil Ki Two Copy</p>
											<p class="fs-9 mt-1">28 June 2024</p>
										</div>
									</div>
									<div class="text-end"><b class="purple d-block">$869.60</b> <p class="track_perctng">28.5%</p></div>
								</div> -->

							</div>
						</div>
					</div>
				</div>
				<div class="news_area mt-4">
					<p class="float-start heading ms-2">News</p>
					<a href="<?php echo site_url('members/news');?>" title="View More" class="blue2 fs-8 mt-2 me-2 fw-bold float-end">VIEW MORE</a>
					<p class="clearfix"></p>
					<div class="dash_sec_bdr mt-3">
						<div class="row g-3">
							<?php 
							$res_news = [0=>array('id'=>'1'),1=>array('id'=>'2')];
							foreach($res_news as $key=>$val){
							echo '<div class="col-lg-6">';
							$this->load->view('news/news_item',array('val'=>$val));
							echo '</div>';
							}?>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
    $('#release_month').on('change', function(){
		  var selectedMonth = $(this).val();
		  var member_id = '<?php echo $this->userId;?>';
		  $.ajax({
		    url: '<?php echo site_url("remote/load_monthly_releases_graph"); ?>',
		    type: 'POST',
		    data: { member_id: member_id, month: selectedMonth, action: 'Y' },
		    success: function(response) {
		      var monthly_releases = JSON.parse(response);
		      var months = monthly_releases.days;
		      var achieve = monthly_releases.achieved_data;
		      var target = monthly_releases.target_data;
		      updateReleaseGraph(months, achieve, target);
		    },
		    error: function(xhr, status, error) {
		      console.error('AJAX error: ' + status + ', ' + error);
		    }
		  });
		});
		$('#release_month').trigger('change');
    function updateReleaseGraph(months, achieve, target) {
      var d_achiv = [];
      $.jqplot('monthly_graph',[achieve],{gridPadding:{top:0,right:0},animate:!0,seriesColors:["#52b6db"],grid:{drawGridLines:!1,background:"#fff",shadow:0,borderWidth:0},seriesDefaults:{rendererOptions:{smooth:!0}},series:[{showline:1,labels:target,pointLabels:{show:!0,labels:d_achiv}}],
		   	axesDefaults:{tickRenderer:$.jqplot.CanvasAxisTickRenderer,tickOptions:{showGridline:!0,showGridline:!0,show:true,showLabel: true}},axes:{xaxis:{renderer: $.jqplot.CategoryAxisRenderer,labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
		          tickRenderer: $.jqplot.CanvasAxisTickRenderer,ticks:months,tickOptions:{angle:-30,fontFamily:'Arial',fontSize:'8pt'}}},highlighter:{show:true,showTooltip:false,tooltipLocation:'n',yvalues:1,useAxesFormatters:true,
					  tooltipContentEditor: function tooltipContentEditor(str, seriesIndex, pointIndex, plot){
						  $('.jqplot-highlighter-tooltip').html('<b>Target:</b> '+plot.data[seriesIndex][pointIndex].toLocaleString("en-US",{style:"currency",currency:"INR"})+ ' <br> <b>Achieved:</b> ' + plot.series[seriesIndex]["labels"][pointIndex].toLocaleString("en-US",{style:"currency",currency:"INR"})+ ' <br> <b>On:</b> ' + plot.options.axes.xaxis.ticks[pointIndex]);
						}
			}});
    }
});
</script>

<script>
$(window).load(function(e){
var products_data = [['Saavn',1150],['Facebook (Video)',380],['Spotify',380],['Tiktok',380],['Amazon',1150],['Facebook SRP',380],['Hungama',380],['Resso',1150],['Wynk',380],['Gaana',380],['SNAP',380],['YouTube Content ID',380],['Others',380]];  
var products_data_plot = $.jqplot('product_stats',[products_data],{
seriesDefaults:{shadow:false,padding:0,
renderer:$.jqplot.DonutRenderer,
rendererOptions:{sliceMargin:0,shadow:false,background:'#fff',/*dataLabels:'value',*/totalLabel:true,startAngle:90,padding:27,innerDiameter:120
},
seriesColors:['#5e76e2','#ef3b5e']
},legend:{show:true,marginTop:'0',fontSize:'11px',rowSpacing:'1em',background:'none',border:'none',rendererOptions:{
textColor:"#FFFFFF",fontSize: "10pt"
}},
grid:{drawBorder:false,background:'transparent',shadow:false}
});
});
$(window).load(function(){
var Campaigns = [['Earning',1500],['Withdrawl',400],['Commission',400]];
var Campaigns_plot=jQuery.jqplot("scheme_stats",[Campaigns],{grid:{background:"rgba(57,57,57,0)",drawBorder:0,shadow:0},gridPadding:{top:0,bottom:0,left:0,right:0},seriesDefaults:{renderer:jQuery.jqplot.DonutRenderer,shadow:0,rendererOptions:{sliceMargin:0,dataLabels:"value",diameter:160,thickness:25,startAngle:260},seriesColors:["#ff59a0","#fac248","#00a8ff"]},legend:{show:!1}});
});
</script>
<style>#product_stats .jqplot-data-label{color:#fff;font-weight:600}</style>
<script src="<?php echo resource_url();?>Scripts/jquery.jqplot.js"></script>
<script src="<?php echo resource_url();?>Scripts/plugins/jqplot.pieRenderer.js"></script>
<script src="<?php echo resource_url();?>Scripts/plugins/jqplot.donutRenderer.js"></script>
<script src="<?php echo resource_url();?>Scripts/plugins/jqplot.barRenderer.js"></script>
<script src="<?php echo resource_url();?>Scripts/plugins/jqplot.categoryAxisRenderer.js"></script>
<script src="<?php echo resource_url();?>Scripts/plugins/jqplot.pointLabels.js"></script>
<script src="<?php echo resource_url();?>Scripts/plugins/jqplot.highlighter.js"></script>
<script src="<?php echo resource_url();?>Scripts/plugins/jqplot.canvasAxisTickRenderer.js"></script>
<script src="<?php echo resource_url();?>Scripts/plugins/jqplot.canvasTextRenderer.js"></script>
<?php $this->load->view("bottom_application");?>