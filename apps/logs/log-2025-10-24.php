<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-10-24 11:07:07 --> The provided image is not valid.
ERROR - 2025-10-24 11:07:07 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 11:13:08 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-24 11:13:08 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1761284588
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = 'dc51198450463dd28950cad654c48a5c11541b6d'
AND `ip_address` = '49.47.135.11'
ERROR - 2025-10-24 11:13:38 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-24 11:13:38 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1761284618
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = 'dc51198450463dd28950cad654c48a5c11541b6d'
AND `ip_address` = '49.47.135.11'
ERROR - 2025-10-24 11:14:42 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-24 11:14:42 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1761284682
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = 'b3d61b5849f0e02a01044b526a15eb6e73fc982d'
AND `ip_address` = '49.47.135.11'
ERROR - 2025-10-24 11:15:27 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '4' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-24 11:15:27 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1761284727
WHERE wr.status = '4' AND wr.album_type = '1' 
AND `id` = 'b3d61b5849f0e02a01044b526a15eb6e73fc982d'
AND `ip_address` = '49.47.135.11'
ERROR - 2025-10-24 11:18:39 --> Could not find the language line "form_validation_valid_url"
ERROR - 2025-10-24 11:18:47 --> Could not find the language line "form_validation_valid_url"
ERROR - 2025-10-24 11:22:13 --> The provided image is not valid.
ERROR - 2025-10-24 11:22:13 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 11:22:24 --> The provided image is not valid.
ERROR - 2025-10-24 11:22:24 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 11:26:09 --> The provided image is not valid.
ERROR - 2025-10-24 11:26:09 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 11:29:46 --> The provided image is not valid.
ERROR - 2025-10-24 11:29:46 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 11:29:52 --> The provided image is not valid.
ERROR - 2025-10-24 11:29:52 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 11:29:55 --> The provided image is not valid.
ERROR - 2025-10-24 11:29:55 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 11:30:08 --> The provided image is not valid.
ERROR - 2025-10-24 11:30:08 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 11:30:17 --> The provided image is not valid.
ERROR - 2025-10-24 11:30:17 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 11:30:20 --> The provided image is not valid.
ERROR - 2025-10-24 11:30:20 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 11:34:18 --> The provided image is not valid.
ERROR - 2025-10-24 11:34:18 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 11:34:36 --> The provided image is not valid.
ERROR - 2025-10-24 11:34:36 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 11:45:12 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-24 11:45:12 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1761286512
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '668fb9fde8a93f809026957a07833daa56e9a418'
AND `ip_address` = '14.194.4.66'
ERROR - 2025-10-24 11:45:32 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-24 11:45:32 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1761286532
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '668fb9fde8a93f809026957a07833daa56e9a418'
AND `ip_address` = '14.194.4.66'
ERROR - 2025-10-24 11:45:41 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '4' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-24 11:45:41 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1761286541
WHERE wr.status = '4' AND wr.album_type = '1' 
AND `id` = '668fb9fde8a93f809026957a07833daa56e9a418'
AND `ip_address` = '14.194.4.66'
ERROR - 2025-10-24 11:45:58 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-24 11:45:58 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1761286558
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '668fb9fde8a93f809026957a07833daa56e9a418'
AND `ip_address` = '14.194.4.66'
ERROR - 2025-10-24 11:46:20 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '4' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-24 11:46:20 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1761286580
WHERE wr.status = '4' AND wr.album_type = '1' 
AND `id` = '668fb9fde8a93f809026957a07833daa56e9a418'
AND `ip_address` = '14.194.4.66'
ERROR - 2025-10-24 11:51:49 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '4' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-24 11:51:49 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1761286909
WHERE wr.status = '4' AND wr.album_type = '1' 
AND `id` = '123ae773b2a8843921b015367e0ee8be4d2c2602'
AND `ip_address` = '203.115.97.154'
ERROR - 2025-10-24 11:52:22 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-24 11:52:22 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1761286942
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = '123ae773b2a8843921b015367e0ee8be4d2c2602'
AND `ip_address` = '203.115.97.154'
ERROR - 2025-10-24 11:58:59 --> The provided image is not valid.
ERROR - 2025-10-24 11:58:59 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 12:02:24 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '4' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-24 12:02:24 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1761287544
WHERE wr.status = '4' AND wr.album_type = '1' 
AND `id` = '8181d0487009dc6294b71c8ab12779ebba909652'
AND `ip_address` = '14.194.4.66'
ERROR - 2025-10-24 12:18:56 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '4' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-24 12:22:33 --> Query error: Unknown column 'wr.album_type' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, cus.first_name, lab.channel_name, lab.created_date as label_date
FROM `wl_signed_albums` as `wr`
LEFT JOIN `wl_labels` as `lab` ON `lab`.`label_id`=`wr`.`label_id` AND `lab`.`status`='1'
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status = '3' AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
ERROR - 2025-10-24 12:22:33 --> Query error: Unknown column 'wr.status' in 'where clause' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1761288753
WHERE wr.status = '3' AND wr.album_type = '1' 
AND `id` = 'fe274d7073686cb9d4d23459f425e3cccbc7bb2e'
AND `ip_address` = '203.115.97.154'
ERROR - 2025-10-24 14:52:02 --> The provided image is not valid.
ERROR - 2025-10-24 14:52:02 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 17:01:37 --> The provided image is not valid.
ERROR - 2025-10-24 17:01:37 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 17:01:49 --> The provided image is not valid.
ERROR - 2025-10-24 17:01:49 --> Your server does not support the GD function required to process this type of image.
ERROR - 2025-10-24 17:01:57 --> The provided image is not valid.
ERROR - 2025-10-24 17:01:57 --> Your server does not support the GD function required to process this type of image.
