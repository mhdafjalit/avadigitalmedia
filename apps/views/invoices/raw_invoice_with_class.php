<div style="padding:10px 0px;">
	<table width="100%" border="0" align="center" cellpadding="4" cellspacing="1">
	<tbody><tr>
		<td align="left">
			<p style="padding-top:2px;font-size:13px;margin:0px;color:#333; line-height:18px;">
			<b style="font-size:17px;color:#000;display:block; margin-bottom:5px"><?php echo $admin_info_data['company_name'];?></b><?php echo $admin_address;?>
			<span style=" padding-top:3px; display:block">Email Us : <b style="color:#000; font-weight:bold;"><?php echo $admin_info_data['admin_email'];?></b></span> <?php if($admin_info_data['phone']){ echo 'Phone :'.$admin_info_data['phone']; }?> </p>
			<br>
		</td>
		<td align="right" valign="middle" style="padding-right:10px;"><img src="<?php echo theme_url();?>images/logo.jpg" alt=""></td>
	</tr>
	<tr>
		<td colspan="2" align="left" valign="top">
			<div style="border:2px solid #eee; padding:10px 10px; margin-bottom:10px">
				<div style="margin-top:3px; font:600 12px/20px Arial, Helvetica, sans-serif">
					<b style="float:left; display:block">Invoice No. : <?php echo $ordmaster['invoice_number'];?><span style="color:#f00;">[ <?php echo $ordmaster['payment_status'];?> ]</span>
					<br>Donated On: <?php echo date("d M, Y",strtotime($ordmaster['order_date']));?></b> 
					<span style="float:right; display:block">Email ID : <?php echo $ordmaster['email'];?>
					<br>Mobile : <?php echo $ordmaster['mobile_number'];?></span>
					<div style="clear:both"></div>
				</div>
			</div>
		</td>
	</tr>
	</tbody></table>
</div>


<div style="border:1px solid #ddd; padding:10px 10px;">
	<table width="100%" border="0" align="center" cellpadding="4" cellspacing="1" style="margin-top:10px;">
	<tbody>
	<tr style="font-size:13px; color:#000; text-transform:uppercase; line-height:20px; background:#f7f5f5; border-bottom:#ccc 1px solid;">
		<td align="left"><strong>Details</strong></td>
		<td align="left" ><strong>Amount</strong></td>
	</tr>

	<tr>       
		<td width="49%" align="left" valign="top" style="border-bottom:1px solid #ddd; padding-bottom:10px;">
			<div style="color:#333; font-size:13px; padding-top:5px; margin:0px; line-height:22px">
				<strong style="font-size:15px; font-weight:600"><?php echo $ordmaster['billing_name'];?></strong> 
				<span style="font-size:12px; display:block;"><b>Phone No.:</b> <?php echo $ordmaster['mobile_number'];?></span>
				<span style="font-size:12px; display:block;"><b>Email:</b> <?php echo $ordmaster['email'];?></span>
				<?php if(!empty($orddetail[0]['service_name'])){?>
					<span style="font-size:12px; display:block;"><b>For:</b> <?php echo $orddetail[0]['service_name'];?></span>
				<?php }?>
			</div>
		</td>
		<td align="left" valign="top" style="width:15%; border-bottom:1px solid #ddd; padding-bottom:10px;"><strong style="font-size:14px; color:#000;"><?php echo display_price($invoice_amount,2);?></strong></td>
	</tr>
	</tbody></table>
</div>