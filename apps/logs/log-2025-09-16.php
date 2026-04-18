<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-09-16 13:36:15 --> Could not find the language line "success"
ERROR - 2025-09-16 21:40:27 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-09-16 21:40:27 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1758039027
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '26d3ba17a89e8459032adfad7b42c9c98231cebb'
AND `ip_address` = '49.47.133.220'
ERROR - 2025-09-16 21:40:32 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-09-16 21:40:32 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1758039032
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '26d3ba17a89e8459032adfad7b42c9c98231cebb'
AND `ip_address` = '49.47.133.220'
ERROR - 2025-09-16 21:40:36 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-09-16 21:40:36 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1758039036
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '26d3ba17a89e8459032adfad7b42c9c98231cebb'
AND `ip_address` = '49.47.133.220'
ERROR - 2025-09-16 21:58:39 --> Severity: Warning --> Trying to access array offset on value of type null /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/modules/admin/controllers/Admin.php 1930
ERROR - 2025-09-16 21:58:39 --> Severity: Warning --> Trying to access array offset on value of type null /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/modules/admin/controllers/Admin.php 1936
ERROR - 2025-09-16 21:58:39 --> Severity: Warning --> Trying to access array offset on value of type null /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/modules/admin/controllers/Admin.php 1944
ERROR - 2025-09-16 21:58:39 --> Severity: 8192 --> str_replace(): Passing null to parameter #2 ($replace) of type array|string is deprecated /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/modules/admin/controllers/Admin.php 1944
ERROR - 2025-09-16 21:58:39 --> Severity: Warning --> Trying to access array offset on value of type null /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/modules/admin/controllers/Admin.php 1951
