<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
    array('heading'=>'Dashboard','url'=>'members')
);
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
                        <a class="nav-link" aria-current="page" href="<?php echo site_url('members/wallet');?>">Payment Request</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('members/wallet/invoice');?>">Invoice and Payment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo site_url('members/wallet/user_earning');?>">User Earning Report</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('members/wallet/commission');?>">Commission Amount</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('members/wallet/revenue_graph');?>">Revenue Graph</a>
                    </li>
                </ul>
                <p class="border-bottom mb-4"></p>
                <div class="row g-2">
                    <div class="col-sm-6">
                        <p class="p-3 rounded-3 border shadow text-center lh-base text-white" style="background:#7438ab;">Requested Amount: <b class="fs-5">$0.00</b></p>
                    </div>
                    <div class="col-sm-6">
                        <p class="p-3 rounded-3 border shadow text-center lh-base text-white" style="background:#7438ab;">Available Amount: <b class="fs-5">$0.00</b></p>
                    </div>
                </div>
                <div class="row g-2 mt-4">
                    <div class="col-sm-3">
                        <label class="form-label">User</label>
                        <select class="form-select">
                           <option>Select User</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label d-block">Label</label>
                        <select class="selectpicker border w-100 form-select"  aria-label="size 3 select example">
                            <option value="">Select</option>
							<?php foreach ($labels as $label){  ?>
                                <option value="<?php echo $label['channel_name']; ?>" <?php echo set_select("label", $label['channel_name']); ?>><?php echo $label['channel_name']; ?></option>
                            <?php 
                            } ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Month/Year</label>
                        <select class="form-select">
                        <option>Month</option>
                        <option>Year</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label d-block">&nbsp;</label>
                        <button class="btn btn-purple">Submit</button>
                    </div>
                </div>
                <div class="top_paid_box">
                    <div class="bg-white p-3 rounded-3">
                        <div id="monthly_graph" style="width:99%;height:300px"></div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<style>.top_paid_box table.jqplot-table-legend{color:#000 !important;border:0}.top_paid_box table.jqplot-table-legend td:nth-child(even){padding-right:1em !important}</style>
<script>
$(window).load(function(e){
  // var Requested_amount = [200,100,50,153,133,26,99,198,10,60,100,160,50,53,63,26,99,108,181,94,60,10,50,53,33,26,299,313,21,14];
   // var Available_amount = [240,160,150,173,123,126,199,298,140,260,130,120,150,253,163,226,199,178,211,194,160,210,250,153,233,226,199,233,211,184];
  //  var legends = ['Spotify','Gana']

   var Requested_amount = [0,0,0,0,0,0,0,0,0,0,0,0];  
    var Available_amount = [0,0,0,0,0,0,0,0,0,0,0,0];
    var legends = []
    
   var months = ['Jan','Feb','March','April','May','June','July','Aug','Sept','Oct','Nov','Dec'];
   //var target = [50,100,18000000,18000000,18000000,18000000,20000000,20000000];
   var Trend_Basis_plot1 = $.jqplot('monthly_graph',[Requested_amount,Available_amount],{gridPadding:{top:0,right:0},animate:!0,seriesColors:["#52b6db","#245870"],grid:{drawGridLines:!1,background:"#fff",shadow:0,borderWidth:0},seriesDefaults:{rendererOptions:{smooth:!0}/*,pointLabels:{show:true,labels:dates}*/},series:[{showline:1,pointLabels:{show:0}}],
   axesDefaults:{tickRenderer:$.jqplot.CanvasAxisTickRenderer,tickOptions:{showGridline:!0,showGridline:!0,show:true,showLabel:true}},legend:{show:true,placement:'outsideGrid',labels:legends,location:'s',renderer: $.jqplot.EnhancedLegendRenderer,rendererOptions: {
                numberRows: 1
            },},
      axes:{
        xaxis:{
          renderer: $.jqplot.CategoryAxisRenderer,
          labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
          tickRenderer: $.jqplot.CanvasAxisTickRenderer,
          ticks:months,
          tickOptions:{angle:-30,fontFamily:'Arial',fontSize:'8pt'}}},
          highlighter:{
              show:true,showTooltip:!0,tooltipLocation:'n',yvalues:1,useAxesFormatters:true
}});
})
</script>
<script src="<?php echo resource_url();?>Scripts/jquery.jqplot.js"></script>
<script src="<?php echo resource_url();?>Scripts/plugins/jqplot.categoryAxisRenderer.js"></script>
<script src="<?php echo resource_url();?>Scripts/plugins/jqplot.pointLabels.js"></script>
<script src="<?php echo resource_url();?>Scripts/plugins/jqplot.highlighter.js"></script>
<script src="<?php echo resource_url();?>Scripts/plugins/jqplot.cursor.js"></script>
<script src="<?php echo resource_url();?>Scripts/plugins/jqplot.canvasAxisTickRenderer.js"></script>
<script src="<?php echo resource_url();?>Scripts/plugins/jqplot.canvasTextRenderer.js"></script>
<script src="<?php echo resource_url();?>Scripts/plugins/jqplot.enhancedLegendRenderer.js"></script>
<?php $this->load->view("bottom_application");?>