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
                        <a class="nav-link active" href="<?php echo site_url('members/wallet/invoice');?>">Invoice and Payment</a>
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
                <div class="dash_box p-2 mt-3">       
                    <div class="table-responsive">
                        <div class="scrollbar style-4">
                            <table class="table table-bordered mb-0 acc_table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Requested Amount</th>
                                        <th>Balance</th>
                                        <th>Download Invoice</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php /*?><tr>
                                        <td colspan="7"><div class="text-center b mt-4">No record(s) Found.</div></td>
                                    </tr><?php */
									
                					if($this->mres['member_type'] == '1'){?>
                                        <tr>
                                        <td>28 May, 2024</td>
                                        <td>Royalities</td>
                                        <td>Distribution Revenue: May 2024</td>
                                        <td>$253.53</td>
                                        <td>$451.30</td>
                                        <td><a href="#"><img src="<?php echo theme_url();?>images/download.svg" alt=""></a></td>
                                        <td class="text-success"><img src="<?php echo theme_url();?>images/check2.svg" alt=""> Approved</td>
                                    </tr>
                                    <?php
									}?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<?php $this->load->view("bottom_application");?>