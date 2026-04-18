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
                        <a class="nav-link" href="<?php echo site_url('members/wallet/user_earning');?>">User Earning Report</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo site_url('members/wallet/commission');?>">Commission Amount</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('members/wallet/revenue_graph');?>">Revenue Graph</a>
                    </li>
                </ul>
                <p class="border-bottom mb-4"></p>
                <div class="text-center">
                    <select class="form-select d-inline-block" style="width:280px;">
                        <option>Select User</option>
                    </select>
                </div>
                <div class="offset-xl-2">
                    <div class="col-lg-8">
                        <div class="position-relative" id="user_stats"></div>
                    </div>
                </div>
                <div class="dash_box p-2 mt-2">
                    <div class="table-responsive">
                        <div class="scrollbar style-4">
                            <table class="table table-bordered mb-0 acc_table table-striped">
                                <thead>
                                    <tr>
                                        <th>User Name</th>
                                        <th>Commission %</th>
                                        <th>Month, Year</th>
                                        <th>Applicable on</th>
                                        <th>Commission calculated</th>
                                        <th>User Earning</th>
                                    </tr>
                                </thead>
                                <tbody>

                               <?php /*?> <tr>
                                        <td colspan="6"><div class="text-center b mt-4">No record(s) Found.</div></td>
                                    </tr><?php */
									
                				if($this->mres['member_type'] == '1'){?>
                                    <tr>
                                        <td>Puneet Chauhan</td>
                                        <td>10%</td>
                                        <td>June, 2024</td>
                                        <td class="lh-sm">Ajay Kumar, <br>Shashi Bhushan Himta, <br>Darshan Rawat, <br>Vikas Sharma</td>
                                        <td>$20.00</td>
                                        <td>$220.00</td>
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