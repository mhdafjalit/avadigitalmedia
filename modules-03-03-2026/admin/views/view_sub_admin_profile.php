<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
	array('heading'=>'Sub User Manage','url'=>'admin/sub_admins'),
	array('heading'=>'Dashboard','url'=>'admin')
);
$uerId= $this->uri->segment(3);
$mem_name = trim($mres['first_name'].' '.$mres['last_name']);
if($mres['profile_photo']!='' && file_exists(UPLOAD_DIR.'/profiles/'.$mres['profile_photo'])){
	$profile_photo = base_url().'uploaded_files/profiles/'.$mres['profile_photo'];
}else{
	$profile_photo = theme_url().'images/no-img.jpg';
}
$loop_address = $mres['address'];
function getRecordName($id, $table, $column) {
  return $id > 0 ? log_fetched_rec($id, $table, $column)['rec_data'][$column] ?? '' : '';
}
$city_name = getRecordName($mres['city'], 'city', 'title');
$state_name = getRecordName($mres['state'], 'state', 'title');
$country = getRecordName($mres['country'], 'country', 'country_name');
$zipcode = $mres['pin_code'] > 0 ? $mres['pin_code'] : '';
$loop_address .= $city_name ? ' ' . $city_name : '';
$loop_address .= $state_name ? ', ' . $state_name : '';
$loop_address .= $zipcode ? ' - ' . $zipcode : '';
$loop_address .= $country ? ' (' . $country . ')' : '';
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
    		<div class="dash_box p-4">
    			<div class="d-sm-flex justify-content-between">
						<div class="user_pic text-center overflow-hidden rounded-3">
							<span class="align-middle d-table-cell">
								<img src="<?php echo $profile_photo;?>" alt="<?php echo $mem_name;?>" class="mw-100 mh-100">
							</span>
						</div>
						<div class="user_rgt mt-3 mt-sm-0">
							<p class="fw-semibold fs-5"><?php echo $mem_name;?></p>
							<div class="user_contact d-flex justify-content-between mt-1">
								<img src="<?php echo theme_url();?>images/mail.svg" width="15" alt="" class="mt-1"> 
								<p><?php echo $mres['user_name'];?></p>
							</div>
							<div class="user_contact d-flex justify-content-between mt-1">
								<img src="<?php echo theme_url();?>images/login.svg" width="15" alt="" class="mt-1"> 
								<p><?php echo $this->mres['sponsor_id'];?></p>
							</div>
							<div class="user_contact d-flex justify-content-between mt-1">
								<img src="<?php echo theme_url();?>images/call2.svg" width="15" alt="" class="mt-1"> 
								<p><?php echo $mres['mobile_number'];?></p>
							</div>
							<div class="user_contact d-flex justify-content-between mt-1">
								<img src="<?php echo theme_url();?>images/location.svg" height="15" alt="" class="mt-1 ms-1"> 
								<p><?php echo $loop_address;?></p>
							</div>
							<div class="row g-2 mt-1">
								<?php
								if($mres['agreement_doc']!='' && file_exists(UPLOAD_DIR.'/members/'.$mres['agreement_doc'])){?>
								<p class="col-sm-6 col-lg-4">
									User Agreement : <b class="fw-semibold"><a href="<?php echo base_url();?>pages/download_file/members/<?php echo $mres['agreement_doc'];?>" class="text-primary text-uppercase fw-medium" title="Download User Agreement"><img src="<?php echo theme_url();?>images/download.svg" width="16" alt="Download"></a></b>
								</p>
								<?php 
								}if($mres['aadhar_doc']!='' && file_exists(UPLOAD_DIR.'/members/'.$mres['aadhar_doc'])){?>
								<p class="col-sm-6 col-lg-4">
									Government Id : <b class="fw-semibold"><a href="<?php echo base_url();?>pages/download_file/members/<?php echo $mres['aadhar_doc'];?>" class="text-primary text-uppercase fw-medium" title="Download Government Id"><img src="<?php echo theme_url();?>images/download.svg" width="16" alt="Download"></a></b>
								</p>
								<?php
								}if($mres['pancard_doc']!='' && file_exists(UPLOAD_DIR.'/members/'.$mres['pancard_doc'])){?>
								<p class="col-sm-6 col-lg-4">
									PAN Card : <b class="fw-semibold"><a href="<?php echo base_url();?>pages/download_file/members/<?php echo $mres['pancard_doc'];?>" class="text-primary text-uppercase fw-medium" title="Download User PAN Card"><img src="<?php echo theme_url();?>images/download.svg" width="16" alt="Download"></a></b>
								</p>
								<?php }if($mres['bank_passbook']!='' && file_exists(UPLOAD_DIR.'/members/'.$mres['bank_passbook'])){?>
								<p class="col-sm-6 col-lg-4">
									Bank Passbook : <b class="fw-semibold"><a href="<?php echo base_url();?>pages/download_file/members/<?php echo $mres['bank_passbook'];?>" class="text-primary text-uppercase fw-medium" title="Download Bank Passbook"><img src="<?php echo theme_url();?>images/download.svg" width="16" alt="Download"></a></b>
								</p>
								<?php }?>
								<p class="col-sm-6 col-lg-4">User Rate %: <b class="fw-semibold"><?php echo $mres['commission'];?></b></p>
								<?php
								echo ($mres['is_gst']>0) ? '<p class="col-sm-6 col-lg-4">GST Number: <b class="fw-semibold">'.$mres['gst_number'].'</b></p>' : '';
								?>
								<p class="col-sm-6 col-lg-4">
									Account Type: 
									<b class="fw-semibold"><?php echo ($mres['bank_account_type']>0) ? 'Outside India':'India' ;?></b>
								</p>
								<div class="col-12">
									<div class="mt-2 border p-3 rounded-3">
										<div class="row gx-0 gy-3">
											<?php
											if($mres['bank_account_type']>0){?>
											<p class="col-sm-6 col-lg-4">
												<b class="fw-semibold fs-7">Bank Email Id:</b><br> <?php echo $mres['bank_email'];?>
											</p>
											<p class="col-sm-6 col-lg-4">
												<b class="fw-semibold fs-7">Bank Customer Id :</b><br> <?php echo $mres['bank_customer_id'];?>
											</p>
											<p class="col-sm-6 col-lg-4">
												<b class="fw-semibold fs-7">Bank Service Provider :</b><br> <?php echo $mres['bank_service_provider'];?>
											</p>
											<?php }else{?>
											<p class="col-sm-6 col-lg-4">
												<b class="fw-semibold fs-7">Bank Name:</b><br> <?php echo $mres['bank_name'];?>
											</p>
											<p class="col-sm-6 col-lg-4">
												<b class="fw-semibold fs-7">Account Number :</b><br> <?php echo $mres['account_no'];?>
											</p>
											<p class="col-sm-6 col-lg-4">
												<b class="fw-semibold fs-7">IFSC Code:</b><br> <?php echo $mres['ifsc_code'];?>
											</p>
											<p class="col-sm-6 col-lg-4">
												<b class="fw-semibold fs-7">Account Holder Name :</b><br> <?php echo $mres['ac_holder_name'];?>
											</p>
											<p class="col-12">
												<b class="fw-semibold fs-7">Branch Address:</b><br> <?php echo $mres['bank_address'];?>
											</p>
											<?php } ?>
										</div>
			    				</div>
			    			</div>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
<!-- MIDDLE ENDS -->
<?php $this->load->view("bottom_application");?>