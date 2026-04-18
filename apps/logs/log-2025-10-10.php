<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-10-10 08:17:25 --> Severity: Warning --> mysqli::real_connect(): (HY000/1040): Too many connections /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/codeigniter/database/drivers/mysqli/mysqli_driver.php 211
ERROR - 2025-10-10 08:17:25 --> Unable to connect to the database
ERROR - 2025-10-10 08:17:25 --> Severity: error --> Exception: Class "CI_Controller" not found /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/codeigniter/core/CodeIgniter.php 370
ERROR - 2025-10-10 08:17:25 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/codeigniter/core/Exceptions.php:272) /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/codeigniter/core/Common.php 571
ERROR - 2025-10-10 08:17:43 --> Severity: Warning --> mysqli::real_connect(): (HY000/1040): Too many connections /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/codeigniter/database/drivers/mysqli/mysqli_driver.php 211
ERROR - 2025-10-10 08:17:43 --> Unable to connect to the database
ERROR - 2025-10-10 08:17:43 --> Severity: error --> Exception: Class "CI_Controller" not found /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/codeigniter/core/CodeIgniter.php 370
ERROR - 2025-10-10 08:17:43 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/codeigniter/core/Exceptions.php:272) /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/codeigniter/core/Common.php 571
ERROR - 2025-10-10 11:40:41 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-10 11:40:41 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760076641
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '78f71bd55322aea41bbf4fb8b8e7d5fd0823331b'
AND `ip_address` = '14.194.4.66'
ERROR - 2025-10-10 11:40:51 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-10 11:40:51 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760076651
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '78f71bd55322aea41bbf4fb8b8e7d5fd0823331b'
AND `ip_address` = '14.194.4.66'
ERROR - 2025-10-10 11:43:30 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-10 11:43:30 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760076810
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '78f71bd55322aea41bbf4fb8b8e7d5fd0823331b'
AND `ip_address` = '14.194.4.66'
ERROR - 2025-10-10 16:19:01 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-10 16:19:01 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760093341
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '735f5ba90f0e9c38b39ef82eb0fba4c6a3675868'
AND `ip_address` = '14.194.4.66'
