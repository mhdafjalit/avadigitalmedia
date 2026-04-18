<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
    array('heading'=>'Dashboard','url'=>'admin')
);
$store_config = $this->config->item('store_config');
?>
<div class="dash_outer">
    <div class="dash_container">
        <?php $this->load->view('view_left_sidebar'); ?>
        <div id="main-content" class="h-100">
            <?php $this->load->view('view_top_sidebar');?>
            <div class="top_sec d-flex justify-content-between">
                <h1 class="mt-4"><?php echo $heading_title;?></h1>
                <?php echo navigation_breadcrumb($heading_title,$bdcm_array); ?>
            </div>
            <p class="clearfix"></p>
            <div class="main-content-inner">
                <ul class="nav nav-underline tabber_style">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('admin/wallet/invoice');?>">Invoice and Payment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('admin/wallet/user_earning');?>">User Earning Report</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('admin/wallet/commission');?>">Commission Amount</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo site_url('admin/wallet/revenue_graph');?>">Revenue Graph</a>
                    </li>
                </ul>
                <p class="border-bottom mb-4"></p>
                <div class="row g-2 mt-4">
                    <div class="col-sm-3">
                        <label class="form-label">User</label>
                        <select class="form-select">
                        <option>Select User</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Label</label>
						<select class="form-select" id="label" name="label">
                        	<option value="">Select</option>
							<?php foreach ($labels as $label){  ?>
                                <option value="<?php echo $label['channel_name']; ?>" <?php echo set_select("label", $label['channel_name']); ?>><?php echo $label['channel_name']; ?></option>
                            <?php 
                            } ?>
                        </select>                         
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Store</label>                                            
                        <select name="store" class="form-select">
                             <option value="">Select</option>
                            <?php if(is_array($store_config) && !empty($store_config)){ 
                                foreach($store_config as $k=>$v){?>
                            <option value="<?php echo $k;?>"><?php echo $v;?></option>
                            <?php } }?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label d-block">&nbsp;</label>
                        <button class="btn btn-purple">Submit</button>
                    </div>
                </div>
                <div class="top_paid_box p-4 mt-3 graph1">   
                    <div id="weekly_graph" style="width:99%;height:300px"></div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<script>
$(window).load(function(e){
var bar_1 = [0,0,0,0,0,0,0,0,0,0,0,0];
var ticks = ['Nov 2023','Dec 2023','Jan 2024','Feb 2024','Mar 2024','Apr 2024','May 2024','Jun 2024','July 2024','Aug 2024','Sep 2024','Oct 2024'];   
var Trend_Basis_plot1 = $.jqplot('weekly_graph', [bar_1], {gridPadding:{top:0,right:0},animate:!0,seriesColors:["#f34994"],grid:{drawGridLines:!1,background:"transparent",shadow:0,borderWidth:0},
seriesDefaults: {
renderer:$.jqplot.BarRenderer,rendererOptions:{barPadding:2,barMargin:25,shadowOffset:0},pointLabels:{show:false}
},
series:[{renderer:$.jqplot.BarRenderer}],axesDefaults:{tickRenderer:$.jqplot.CanvasAxisTickRenderer,tickOptions:{showGridline:0,showGridline:0}},
axes: {
xaxis: {
renderer: $.jqplot.CategoryAxisRenderer,
labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
tickRenderer: $.jqplot.CanvasAxisTickRenderer,
ticks: ticks,
tickOptions: {
angle:0,
fontFamily:'Arial',
fontSize:'8pt',
textColor:'#999'
}}}});})
</script>
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