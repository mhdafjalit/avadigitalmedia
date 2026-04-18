<?php $this->load->view("top_application", array('is_dashboard'=>true));
$dashboard_link = site_url('members/dashboard');
$wallet_link = site_url('members/wallet');
$bdcm_array = array(
											array('heading'=>'Dashboard','url'=>'members'.$this->common_qs_link),
											array('heading'=>'My Wallet Coins','url'=>$wallet_link.$this->common_qs_link)
										);
echo navigation_breadcrumb($page_heading, $bdcm_array);
$this->load->view('members/page_top', array('page_heading'=>$page_heading));?>
<div class="bg-light pt-4 pb-4">
 <div class="container">
  <div class="acc_box">
   <div class="row">
    <div class="col-12 no_pad acc_cont_box">
     <div class="fs15" id="my_data">
      <?php
      if(is_array($res) && !empty($res))
      {
	      ?>
	      <div class="d-none d-md-block">
	       <div class="p-1 weight600 black row bb2">
	        <p class="col-md-1 p-1">S. No.</p>
	        <p class="col-md-3 p-1">Transaction ID</p>
	        <p class="col-md-3 p-1">Credited/Debited</p>
	        <p class="col-md-2 p-1">Coins</p>
	        <p class="col-md-2 p-1">Date</p>
	        <p class="col-md-1 p-1">Expiry Date</p>
	       </div>
	      </div>
	      <?php
	      $cnt = $offset_rec+1;
	      foreach($res as $oval)
	      {
		      //trace($oval);
		      ?>
		      <div class="p-1 row bb2">
		       <p class="col-md-1 p-1"><b class="d-inline-block d-md-none">S. No.: </b> <?php echo $cnt;?></p>
		       <div class="col-md-3 p-1"><b class="d-block d-md-none">Transaction ID: <br></b><?php echo $oval['id'];?></div>
		       <div class="col-md-3 p-1"><b class="d-block d-md-none">Credited/Debited: <br></b><?php echo $oval['tx_type_text'];?></div>
		       <div class="col-4 col-md-2 p-1"><b class="d-block d-md-none">Coins: <br></b> <?php echo $oval['points'];?></p></div>
		       <div class="col-4 col-md-2 p-1"><b class="d-block d-md-none">Date: <br></b><p><?php echo getDateFormat($oval['txn_date'],1);?></p></div>
		       <div class="col-4 col-md-1 p-1"><b class="d-block d-md-none">Expiry Date: <br></b><p><?php echo getDateFormat($oval['expire_date'],1);?></p></div>
		      </div>
		      <?php
		      $cnt++;
	      }
	      if($page_links!='')
	      {
		      ?>
		      <div class="mt-3"><?php echo $page_links; ?></div>
		      <?php
	      }
      }?>
     </div> 
    </div>
   </div>
  </div>     
 </div>      
</div>
<?php
echo form_open($base_link,'id="myform" method="get" autocomplete="off" class="dn"');
echo '<input type="hidden" name="offset" value="'.$offset.'">';
echo form_close();?>
<?php $this->load->view("bottom_application");?>