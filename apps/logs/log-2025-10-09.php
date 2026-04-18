<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-10-09 16:56:23 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '4' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-09 16:56:23 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760009183
WHERE wr.status = '4' AND wr.album_type = '1' 
AND `id` = '90674adf2ef7869f8b33f865ed13904f6e6de495'
AND `ip_address` = '49.47.135.209'
ERROR - 2025-10-09 16:56:30 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '4' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-09 16:56:30 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760009190
WHERE wr.status = '4' AND wr.album_type = '1' 
AND `id` = '90674adf2ef7869f8b33f865ed13904f6e6de495'
AND `ip_address` = '49.47.135.209'
ERROR - 2025-10-09 16:56:37 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '4' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-09 16:56:37 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760009197
WHERE wr.status = '4' AND wr.album_type = '1' 
AND `id` = '90674adf2ef7869f8b33f865ed13904f6e6de495'
AND `ip_address` = '49.47.135.209'
