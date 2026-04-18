<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2026-03-05 12:57:08 --> Severity: Warning --> Undefined variable $inserted_release_id /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/modules/admin/controllers/Release.php 159
ERROR - 2026-03-05 13:02:03 --> Severity: Warning --> Undefined variable $inserted_release_id /home/auvadigitalmedia/public_html/crm.auvadigitalmedia.com/modules/admin/controllers/Release.php 159
ERROR - 2026-03-05 16:00:41 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '(cus.parent_id = '16' OR wr.member_id = '16') AND wr.album_type = '1' 
GROUP BY ' at line 7 - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.*, wsa.release_ref_id, wsa.is_verify_meta, wsa.is_pdl_submit, cus.first_name
FROM `wl_releases` as `wr`
LEFT JOIN `wl_signed_albums` as `wsa` ON `wsa`.`release_ref_id`=`wr`.`release_id`
LEFT JOIN `wl_customers` as `cus` ON `cus`.`customers_id`=`wr`.`member_id` AND `cus`.`status`='1'
WHERE wr.status='0'(cus.parent_id = '16' OR wr.member_id = '16') AND wr.album_type = '1' 
GROUP BY `wr`.`release_id`
) CI_count_all_results
