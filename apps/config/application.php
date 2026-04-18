<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
$config['meta_title'] = "Auva Digital Media";
$config['meta_keyword']	= "Auva Digital Media";
$config['meta_description']	= "Auva Digital Media";

$config['bottom.debug'] = 0;
$config['site.status']	= '1';
$config['site_name']	= "Auva Digital Media";

$config['auth.password_min_length']	= '6';
$config['auth.password_force_numbers']	= '1';
$config['auth.password_force_symbols']	= '1';
$config['auth.password_force_mixed_case']	= '1';
$config['password.suggestion'] =  "6 to 10 characters, atleast 1 uppercase, lowercase, numerics with special
signs.";
$config['allow.imgage.dimension']	= '6000x6000';
$config['allow.cover_img.dimension']	= '6000x6000';
$config['allow.file.size']	        = 15*1024*1024; //Bytes
$config['allow.cover_file.size']	= 15*1024*1024; //Bytes

$config['allow.release_file.size']	= 15*1024*1024; //Bytes

$config['allow_discount_option'] = 1;
$config['site_start_date'] 	="2024-12-04";
$config['config.date.time']	= date('Y-m-d H:i:s');
$config['config.date']	    	= date('Y-m-d');

$config['analytics_id']	    	= '';

$config['per_page'] = 24;

$config['frontPageOpt']  = array($config['per_page'],2*$config['per_page'],3*$config['per_page'],4*$config['per_page'],5*$config['per_page'],6*$config['per_page'],7*$config['per_page'],8*$config['per_page'],9*$config['per_page'],10*$config['per_page'],20*$config['per_page']);

$config['no_record_found'] = "No record(s) Found.";

$config['total_addmore_limit'] = 5;

$config['music_types'] =  array(
	'1'=>"Music",
	'2'=>"Classic Music",
	'3'=>"Jazz Music",
);

$config['album_types'] =  array(
	'1'=>"Music",
	'2'=>"Video",
);

$config['album_status_arr'] =  array(
	'0'=>"Pending",
	'1'=>"Released",
	'2'=>"Deleted",
	'3'=>"Correction Required",
	'4'=>"Takendown",
	'5'=>"Waiting to Approval",
	'6'=>"Approved",
);

$config['label_status_arr'] = array(
	'0'=>'Pending',
	'1'=>'Approved',
	'2'=>'Deleted',
	'3'=>'Disapprove'
);

$config['request_types'] = array(
'1'=>'Channel Add Request',
'2'=>'Channel White List Request',
'3'=>'Claim Release Request'
);

$config['genre_arr'] = [
    'Hip-Hop/Rap' => ['Alternative Hip-Hop', 'Concious Hip-Hop', 'Country Rap', 'Emo Rap', 'Hip-Hop', 'Jazz Rap', 'Pop Rap', 'Trap', 'Trap Beats'],
    'Hindustani Classsical' => ['Instrumental', 'Vocal'],
    'Devotional' => ['Aarti', 'Bhajan', 'Carol', 'Chalisa', 'Chant', 'Geet', 'Gospel', 'Gurbani', 'Hymn', 'Kirtan', 'Mantra', 'Instrumental', 'Paath', 'Islamic', 'Shabad'],
    'Carnatic Classical' => ['Instrumental', 'Vocal'],
    'Ambient / Instrumental' => ['Soft', 'Easy Listening', 'Electronic', 'Fusion', 'Lounge'],
    'Film' => ['Devotional', 'Dialogue', 'Ghazal', 'Hip-Hop/ Rap', 'Instrumental', 'Patriotic', 'Remix', 'Romantic', 'Sad', 'Unplugged', 'Item Song', 'Dance'],
    'Pop' => ['Acoustic Pop', 'Band Songs', 'Chill Pop', 'Contemporary Pop', 'Country Pop/ Regional Pop', 'Dance Pop', 'Electro Pop', 'Lo-Fi Pop', 'Love Songs', 'Pop Rap', 'Pop Singer-Songwriter', 'Sad Songs', 'Soft Pop'],
    'Folk' => ['Ainchaliyan', 'Alha', 'Atulprasadi', 'Baalgeet/ Children Song', 'Banvarh', 'Barhamasa', 'Basant Geet', 'Baul Geet', 'Bhadu Gaan', 'Bhagawati', 'Bhand', 'Bhangra', 'Bhatiali', 'Bhavageete', 'Bhawaiya', 'Bhuta song', 'Bihugeet', 'Birha', 'Borgeet', 'Burrakatha', 'Chappeli', 'Daff', 'Dandiya Raas', 'Dasakathia', 'Deijendrageeti', 'Deknni', 'Dhamal', 'Gadhwali', 'Gagor', 'Garba', 'Ghasiyari Geet', 'Ghoomar', 'Gidda', 'Gugga', 'Hafiz Nagma', 'Heliam', 'Hereileu', 'Hori', 'Jaanapada Geethe', 'Jaita', 'Jhoori', 'Jhora', 'Jhumur', 'Jugni', 'Kajari', 'Kajari/ Kajri /Kajri', 'Karwa Chauth Songs', 'Khor', 'Koligeet', 'Kumayuni', 'Kummi Paatu', 'Lagna Geet /Marriage Song', 'Lalongeeti', 'Lavani', 'Lokgeet', 'Loor', 'Maand', 'Madiga Dappu', 'Mando', 'Mapilla', 'Naatupura Paadalgal', 'Naqual', 'Nati', 'Nautanki', 'Nazrulgeeti', 'Neuleu', 'Nyioga', 'Oggu Katha', 'Paani Hari', 'Pai Song', 'Pandavani', 'Pankhida', 'Patua Sangeet', 'Phag Dance', 'Powada', 'Qawwali', 'Rabindra Sangeet', 'Rajanikanta geeti', 'Ramprasadi', 'Rasiya', 'Rasiya Geet', 'Raslila', 'Raut Nacha', 'Saikuthi Zai', 'Sana Lamok', 'Shakunakhar-Mangalgeet', 'Shyama Sangeet', 'Sohar', 'Sumangali', 'Surma', 'Suvvi paatalu', 'Tappa', 'Teej songs', 'Tusu Gaan', 'Villu Pattu'],
    'Indie' => ['Indian Indie', 'Indie Dance', 'Indie Folk', 'Indie Hip-Hop', 'Indie Lo-Fi', 'Indie Pop', 'Indie Rock', 'Classical Fusion', 'Indie Singer -Songwriter']
];

$config['moods_arr'] = ['Romantic', 'Happy', 'Sad', 'Dance', 'Bhangra', 'Patriotic', 'Nostalgic', 'Inspirational', 'Enthusiastic', 'Optimistic', 'Passion', 'Pessimistic', 'Spiritual', 'Peppy', 'Philosophical', 'Mellow', 'Calm'];

$config['prim_track_types'] = array(
'1'=>"Original",
'2'=>"Karaoke",
'3'=>"Medley",
'4'=>"Cover",
'5'=>"Cover by Cover Brand"
);

$config['content_types_arr'] = array(
'1'=>"Album",
'2'=>"Single",
'3'=>"Compilation",
'4'=>"Remix"
);

$config['lang_arr']	= ['Ahirani', 'Arabic', 'Assamese', 'Awadhi', 'Banjara', 'Bengali', 'Bhojpuri', 'Burmese', 'Chhattisgarhi', 'Chinese', 'Dogri', 'English', 'French', 'Garhwali', 'Garo', 'Gujarati', 'Haryanvi', 'Himachali', 'Hindi', 'Iban', 'Indonesian', 'Instrumental', 'Italian', 'Japanese', 'Javanese', 'Kannada', 'Kashmiri', 'Khasi', 'Kokborok', 'Konkani', 'Korean', 'Kumauni', 'Latin', 'Maithili', 'Malay', 'Malayalam', 'Mandarin', 'Manipuri', 'Marathi', 'Marwari', 'Naga', 'Nagpuri', 'Nepali', 'Odia', 'Pali', 'Persian', 'Punjabi', 'Rajasthani', 'Sainthili', 'Sambalpuri', 'Sanskrit', 'Santali', 'Sindhi', 'Sinhala', 'Spanish', 'Swahili', 'Tamil', 'Telugu', 'Thai', 'Tibetan', 'Tulu', 'Turkish', 'Ukrainian', 'Urdu'];

$config['user_title'] =  array(""=>"Select","Mr."=>"Mr.","Ms."=>"Ms.","Miss."=>"Miss.","Mrs."=>"Mrs.","Dr"=>"Dr","Shri"=>"Shri","Smt"=>"Smt","Madam"=>"Madam");

$config['register_thanks']            = "Thanks for registering with <site_name>. We look forward to serving you. ";

$config['register_thanks_activate']   = "Thanks for registering with <website name>. Please Check your mail account to activate your account on the <website name>. ";

$config['enquiry_success']              = "Your enquiry has been submitted successfully. We will revert back to you soon.";
$config['feedback_success']             = "Your Feedback has been submitted successfully. We will revert back to you soon.";
$config['product_enquiry_success']      = "Your product enquiry  has been submitted successfully.We will revert back to you soon.";
$config['product_referred_success']     = "This product has been referred to your friend successfully.";
$config['site_referred_success']        = "Site has been referred to your friend successfully.";
$config['forgot_password_success']      = "Your password has been send to your email address. Please check your email account.";

$config['exists_user_id']              = "Email id  already exists. Please use different email id.";
$config['email_not_exist']             = "Email id does not exist.";

$config['login_failed']             = "Invalid Username/Password";

$config['newsletter_subscribed']           =  "You have been subscribed successfully for our newsletter service.";
$config['newsletter_already_subscribed']   =  "This Email address already exist.";
$config['newsletter_unsubscribed']         =  "You have been unsubscribed from our newsletter service.";
$config['newsletter_not_subscribe']        =  "You are not the subscribe member of our news letter service.";
$config['newsletter_already_unsubscribed']   =  "You have already un-subscribed the newsletter service.";

$config['testimonial_post_success']     = "Thank you for your testimonial to <site_name>. Your message will be posted after review by the <site_name> team.";

$config['advertisement_request']          = "Your advertisement request has been submitted successfully. We will revert back to you soon.";
$config['myaccount_update']               = "Your account information has been updated successfully.";
$config['myaccount_password_changed']     = "Password has been changed successfully.";
$config['myaccount_password_not_match']   = "Old Password does  not match. Please try again.";
$config['member_logout']                  = "Logout successfully.";

$config['shipping_required']         =  "Shipping selection is required.";
$config['payment_success']           =  "Your Order has been placed successfully. A confirmation email and invoice have been sent to your email id";
$config['payment_failed']            =  "Your transaction is canceled.";

$config['arr_rating'] =  array(
'1'=>'1',
'2'=>'2',
'3'=>'3',
'4'=>'4',
'5'=>'5'
);	

$config['bannersz'] =  array(
'Left Panel'=>'300x135',
'Page Bottom'=>'645x80'
);	

$config['bannersections'] = array(
'common'=>"All Pages"
);
/* KEY PAIR OF SECTION AND POSTION */
$config['banner_section_positions'] = array(
'product'=>array('Left','Bottom')
);

$config['trans_options'] = array(
    	'1'=>'Debited',
		'2'=>'Credited',
		'3'=>'Cash Back',
		'4'=>'Return'
);

$config['durArr'] = array(
    	'30'=>'1 Month',
		'60'=>'2 Months',
		'180'=>'6 Months',
		'365'=>'1 Year'
);


$config['config_months_range'] = range(1, 12);
$currentYear = date("Y");
$currentYearMonth = date("n");
$config['current_year_months_range'] = range(1, $currentYearMonth);


$config['total_blog_images'] =1;
$config['total_events_images'] =1;
$config['total_services_images'] =5;
$config['product.best.image.view'] = "( File should be .jpg, .png, .gif format and file size should not be more then 1 MB (1024 KB)) ( Best image size 600X600 )";
$config['photo.best.image.view'] = "( File should be .jpg, .png, .gif format and file size should not be more then 1 MB (1024 KB)) ( Best image size 400X600 )";

$config['release_banner.best.image.view'] = "( File should be .jpg, .png, .gif format and file size should not be more then 5 MB (5140 KB)) ( Best image size 3000X3000)";

$config['member_doc.best.image.view'] = "( File should be .pdf format and file size should not be more then 1 MB ) ";

$config['profile_pic.best.image.view'] = "( File should be .jpg, .png, .gif format and file size should not be more then 1 MB (1024 KB)) ( Best image size 100X100)";
$config['shop_banner_pic.best.image.view'] = "( File should be .jpg, .png, .gif format and file size should not be more then 1 MB (1024 KB)) ( Best image size 903X322)";
$config['offset_start_button_lt']=-10;

$config['mdf_temp_path']=UPLOAD_DIR."/tmp_path";

$config['config_tour_days_range_add'] = range(1,20);
$config['config_tour_night_range_add'] = range(0,end($config['config_tour_days_range_add'])+1);

$cur_symbol = '{{currency_symbol}}';

$config['filter_tp_duration_config'] = array(
			array('title'=>'1 Day 2 Nights','value'=>'1-2'),
			array('title'=>'2 Days 3 Nights','value'=>'2-3'),
			array('title'=>'3 Days 4 Nights','value'=>'3-4'),
			array('title'=>'4 Days 5 Nights','value'=>'4-5'),
			array('title'=>'5 Days 6 Nights','value'=>'5-6'),
			array('title'=>'>5 Days','value'=>'>5'),
		);																			
																				
$config['filter_prd_price_config'] = array(
	array('title'=>'Below '.$cur_symbol.'1000','value'=>'0-999.99'),
	array('title'=>$cur_symbol.'1000 - '.$cur_symbol.'2000','value'=>'1000-2000'),
	array('title'=>$cur_symbol.'2000 - '.$cur_symbol.'5000','value'=>'2000-5000'),
	array('title'=>$cur_symbol.'5000 - '.$cur_symbol.'10000','value'=>'5000-10000'),
	array('title'=>$cur_symbol.'10000 - '.$cur_symbol.'20000','value'=>'10000-20000'),
	array('title'=>'>'.$cur_symbol.'20000','value'=>'>20000')
);

$config['subadmin_privileges'] = array(
	1 => 'View',
	2 => 'Add',
	3 => 'Edit',
	4 => 'Delete',
	5 => 'Activate',
	6 => 'Deactivate',
	7 => 'Approve',
	8 => 'Reject',
	9 => 'Download',
	10 => 'Send Mail',
	12 => 'All'
);

$config['max_profile_photos_limit'] = 1;
$config['max_profile_docs_limit'] = 4;
$config['config_profile_img_note'] = "( File should be jpg, jpeg, png,gif format and file size should not be more then 2 MB )";

$config['config_profile_doc_note'] = "( File should be rtf, doc, docx, pdf, txt, jpg, jpeg, png,gif format and file size should not be more then 2 MB )";
$config['config_profile_doc_file_max_size'] = 2*1024*1024;//Bytes

$config['filter_prd_discount_config'] = array(
	array('title'=>'Below 5%','value'=>'0-4.99'),
	array('title'=>'5% - 10%','value'=>'5-10'),
	array('title'=>'10% - 20%','value'=>'10-20'),
	array('title'=>'20% - 30%','value'=>'20-30'),
	array('title'=>'30% - 50%','value'=>'30-50'),
	array('title'=>'50% - 70%','value'=>'50-70'),
	array('title'=>'Above 70%','value'=>'>70')
);

// test crediential for PDL COSMOS APIU
//$config['pdl_api_token'] = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiNjg0YzI4ZTk1NzlmYjk1MTJhMjI4MDc1Iiwic2NvcGUiOlsiTGFiZWwiXSwiZXh0cmEiOnt9LCJleHAiOjE3ODQzNTIxNjEsImlhdCI6MTc1MzI0ODE2MSwiaXNzIjoiVFNNIiwic3ViIjoiNjg0YzI4ZTk1NzlmYjk1MTJhMjI4MDc1In0.SQUzhACQIBtA7o8nCjgECYNyxpvZZmaSv3nj3PCsDFriPovXsleQQQ6uGCvUPhwwTrFr2QRFtV01TfGXFaaddkhXrEnjg7NbHschC62pYaX6-DOTJtuhjk5a88a9rZakY3dJ8c1X1GpL3Ghi0YY7CqcxFov4BbEp77dAE3wjUzY3k_5AmXGk4a8xexoZJMCiPKOO5Au7UgEtUPAKe8feylUq9pax3lPUv11FSc8nH5nzzwbCsYcmX_f7GuE2f1oLQiLYi1-BI8__BwDNrFx6takk31RVt7g2ROwUIyZlI9F69WLFdx9vPP36Ic-eyNL5RqdNXxQShmY-Wv2Zb_w-Eg';

// Live crediential for PDL COSMOS APIU
 //$config['pdl_api_token'] = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiNjFmZTQ4NjQyZDU2YjYyYTkyN2JkODFiIiwic2NvcGUiOlsiTGFiZWwiXSwiZXh0cmEiOnt9LCJleHAiOjE3ODg0MzI3MzAsImlhdCI6MTc1NzMyODczMCwiaXNzIjoiVFNNIiwic3ViIjoiNjFmZTQ4NjQyZDU2YjYyYTkyN2JkODFiIn0.hDqyZCKIitDwkQI9xtPfmMucEI1QbNwJNcYY3tnAaeaVRBM9jJ0ByS3Sz-9xRXCCjqbEa42CYHrC-OonseJKa2ypR781efNROxP4nuAfGHzI6hA0Pi7QgkLwZRCf_fDRv1MvA2hEep_t3dTsbADMeANyXsXVQNqQwnZqFLxU3xAeUQl9uR8hmKZ1WtMpOMZz7tLA15qogruqPyrysj-GgFv8-_iRl3-kZFVJDqseZzVnQKcW_GyQwB09IqfboRXZISp3LdZk3sDOssIPL2CP0cWVWNdMWX3a7_uByM6YF7BBxw0-kjhAi28-3JUwkn1ND54JTcqQIiMWkWtZNS8zzg';

// new token add
 $config['pdl_api_token'] = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiNjFmZTQ4NjQyZDU2YjYyYTkyN2JkODFiIiwic2NvcGUiOlsiTGFiZWwiXSwiZXh0cmEiOnt9LCJleHAiOjE4MDQ0MjU1NDcsImlhdCI6MTc3MzMyMTU0NywiaXNzIjoiVFNNIiwic3ViIjoiNjFmZTQ4NjQyZDU2YjYyYTkyN2JkODFiIn0.eJ8h4Biffq0Wfmkq-oDFN4AP_cbJmS-9JNThIucOSE3V9toxYdVi5V7BOFVWIp0a4JeFKFgQxvCCHNqJAWQB7SHyMFUnojAGmHmcfJSgHzE-bgpoJgdR6PgWE-Yd0vbYyrPv1xK_4s7shc1ZljEEMRJGHZDluAl0C8mxh-o9-5Pv-6QIeCsDaEqLQQbLS5Db9OKJAhJp0xJR47b20xRPUil5CHYp1o2fN0FCKdZDdM8NbXxuOu3bzAplr3Oqtse8geO3WVrutWgvWQMM19T_umePZ5dvaCXQyaM4vm1PT_nGwoOPgFrCTdZcQmBgLUHQR8MNdroTtXU1p66pW_UCRg';

$config['store_config'] = array(
	1 => 'Spotify',
	2 => 'Apple Music',
	3 => 'Tidal',
	4 => 'JioSaavan',
	5 => 'You Tube Music',
	6 => 'Gaana',
	7 => 'Facebook',
	8 => 'Instagram',
	9 => 'Tencent',
	10 => 'Amazon Music',
	11 => 'Deezer'
);

$config['pdl_release_platform'] = array(
	1 => 'AllPlatformWithYouTube',
	2 => 'AllPlatformNotOnYouTube',
	3 => 'OnlyYouTube'	
);