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
                        <a class="nav-link active" aria-current="page" href="<?php echo site_url('members/wallet');?>">Payment Request</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('members/wallet/invoice');?>">Invoice and Payment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('members/wallet/user_earning');?>">User Earning Report</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('members/wallet/commission');?>">Commission Amount</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('members/wallet/revenue_graph');?>">Revenue Graph</a>
                    </li>
                </ul>
                <p class="border-bottom mb-4"></p>
                <div class="dash_box p-4">
                <?php
                if($this->mres['member_type'] == '1'){?>
                    <p class="fs-1 fw-semibold">0.00 $</p>
                    <div class="row g-2 mt-3">
                        <div class="col-sm-6">
                            <p class="p-3 rounded-3 border shadow text-center lh-base text-white" style="background:#7438ab;">Available balance on: <b class="fs-5">25 May, 2024</b></p>
                        </div>
                        <div class="col-sm-6">
                            <p class="p-3 rounded-3 border shadow text-center lh-base text-white" style="background:#7438ab;">Upcoming operations: <b class="fs-5">0:00 $</b></p>
                        </div>
                    </div>
                 <?php
				}?>
                    <p class="fw-bold purple text-uppercase mt-5">Request my payment</p>
                    <?php 
                    echo error_message();
                    echo form_open_multipart(current_url_query_string(),'name="request_frm" id="request_frm" autocomplete="off"');?>
                    <div class="mt-2 row g-0 requested">
                        <div class="col-lg-6">
                            <div class="input-group">
                              <input type="text" class="form-control" placeholder="Requested Amount" aria-label="Recipient's username" aria-describedby="button-addon2">
                              <button class="btn btn-dark" type="button" id="button-addon2">Submit</button>
                            </div>
                            <p class="mt-2 fs-8 text-black">Minimum amount should be $100</p>
                            <?php echo form_error('channel_name');?>
                        </div>
                    </div>
                    <?php echo form_close();?>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<?php $this->load->view("bottom_application");?>