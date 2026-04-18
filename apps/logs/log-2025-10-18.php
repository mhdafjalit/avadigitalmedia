<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-10-18 12:01:28 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '1
WHERE `id` = 'wl_auto_respond_mails'' at line 2 - Invalid query: SELECT *
FROM 1
WHERE `id` = 'wl_auto_respond_mails'
ERROR - 2025-10-18 12:10:55 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '4' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-18 12:10:55 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760769655
WHERE wr.status = '4' AND wr.album_type = '1' 
AND `id` = 'ad19d65e8b46fe15f26f51614c01ec7376f8e840'
AND `ip_address` = '49.47.135.11'
ERROR - 2025-10-18 12:11:13 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '4' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-18 12:11:13 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760769673
WHERE wr.status = '4' AND wr.album_type = '1' 
AND `id` = 'ad19d65e8b46fe15f26f51614c01ec7376f8e840'
AND `ip_address` = '49.47.135.11'
ERROR - 2025-10-18 12:11:16 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '4' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-18 12:11:16 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760769676
WHERE wr.status = '4' AND wr.album_type = '1' 
AND `id` = 'ad19d65e8b46fe15f26f51614c01ec7376f8e840'
AND `ip_address` = '49.47.135.11'
ERROR - 2025-10-18 12:11:41 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-18 12:11:41 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760769701
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = 'ad19d65e8b46fe15f26f51614c01ec7376f8e840'
AND `ip_address` = '49.47.135.11'
ERROR - 2025-10-18 12:11:52 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '4' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-18 12:11:52 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760769712
WHERE wr.status = '4' AND wr.album_type = '1' 
AND `id` = 'ad19d65e8b46fe15f26f51614c01ec7376f8e840'
AND `ip_address` = '49.47.135.11'
ERROR - 2025-10-18 12:35:26 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '4' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-18 12:35:26 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760771126
WHERE wr.status = '4' AND wr.album_type = '1' 
AND `id` = '6814ffc1f8ae88b112feb7d439ebca099a79ed9c'
AND `ip_address` = '49.47.135.11'
ERROR - 2025-10-18 12:35:37 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-18 12:35:37 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760771137
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '6814ffc1f8ae88b112feb7d439ebca099a79ed9c'
AND `ip_address` = '49.47.135.11'
ERROR - 2025-10-18 12:36:12 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-18 12:36:12 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760771172
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '6814ffc1f8ae88b112feb7d439ebca099a79ed9c'
AND `ip_address` = '49.47.135.11'
ERROR - 2025-10-18 12:36:18 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-18 12:36:18 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760771178
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '6814ffc1f8ae88b112feb7d439ebca099a79ed9c'
AND `ip_address` = '49.47.135.11'
ERROR - 2025-10-18 12:36:25 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-18 12:36:25 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1760771185
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '6814ffc1f8ae88b112feb7d439ebca099a79ed9c'
AND `ip_address` = '49.47.135.11'
