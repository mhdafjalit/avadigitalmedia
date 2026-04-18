<?php
$notification_hint_arr = array(
				'cat_sub_expiry'=>'Category Subscription Expiry Reminder',
				'course_sub_expiry'=>'Course Subscription Expiry Reminder',
				'vcourse_sub_expiry'=>'Video Course Subscription Expiry Reminder',
				'notes_sub_expiry'=>'Notes Subscription Expiry Reminder',
				'ts_sub_expiry'=>'Test Series Subscription Expiry Reminder',
				'live_class_dtls'=>'Live Class Detail',
				'live_mt_dtls'=>'Live Mock Test Detail',
				'live_result_dtls'=>'Live Result',
				'order_purchased'=>'Order Purchased',
				'wallet'=>'Coins Gained',
				'nf_course_by_expired'=>'Course Expired',
				'nf_ts_by_expired'=>'Test Series Expired',
				'nf_vc_by_expired'=>'Video Course Expired',
				'nf_notes_by_expired'=>'Notes Expired',
				'nf_sub_by_expired'=>'Subscription Expired',
				'video_course_detail'=>'Video Course Details',
				'course_detail'=>'Course Details',
				'mtp_detail'=>'Test Series Details',
				'notes_detail'=>'Notes Details',
				'subscription'=>'Subscription'
			);
echo form_open('','method="get"');
echo '<select name="hint" style="padding:10px;">';
echo '<option value="">Select</option>';
foreach($notification_hint_arr as $key=>$val){
	echo '<option value="'.$key.'">'.$val.'</option>';
}
echo '</select>';
echo '<p><input type="submit" value="Submit" /></p>';
echo form_close();