<?php if ( ! defined('CRON')) exit('CLI script access allowed only');
$config['SERVER_NAME'] = "http://domshops.com/test/";
$config['CRON_TIME_LIMIT']	= 0;										// 0 = no time limit
$config['argv']				= array(									// Over-ride CLI parameters 
								1		=> 'products/run_cron'
							);
$config['CRON_BETA_MODE']	= false;									// Beta Mode (useful for blocking submissions for testing)
