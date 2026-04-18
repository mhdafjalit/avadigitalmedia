<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-09-17 10:48:44 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-09-17 10:48:44 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1758086324
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '9d3a6c6df32b735f6a00207cb81884147337b7ed'
AND `ip_address` = '49.47.133.134'
ERROR - 2025-09-17 12:35:26 --> Severity: Warning --> Trying to access array offset on value of type null /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/modules/admin/controllers/Admin.php 1930
ERROR - 2025-09-17 12:35:39 --> Severity: Warning --> Trying to access array offset on value of type null /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/modules/admin/controllers/Admin.php 1930
ERROR - 2025-09-17 12:41:13 --> Could not find the language line "success"
