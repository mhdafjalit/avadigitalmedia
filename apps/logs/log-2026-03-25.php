<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2026-03-25 07:40:14 --> Query error: Table 'decnpxmm_crmauvadigitalmedia.wl_youtube_requests' doesn't exist - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT req.*, cus.first_name
FROM `wl_youtube_requests` as `req`
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`req`.`member_id` AND `cus`.`status`='1'
WHERE req.status != '2' AND req.request_type='1'  
GROUP BY `req`.`request_id`
) CI_count_all_results
ERROR - 2026-03-25 07:40:14 --> Query error: Unknown column 'req.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1774404614
WHERE req.status != '2' AND req.request_type='1'  
AND `id` = 'mkuulpei7saljqfht28gisrls9pd5dbm'
AND `ip_address` = '::1'
ERROR - 2026-03-25 07:43:44 --> Could not find the language line "form_validation_valid_url"
