<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2026-03-09 14:56:38 --> Query error: Unknown column 'wr.id' in 'SELECT' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM (
SELECT wr.id, wr.album_name, wr.song_name, pa.name, wr.album_media, wr.artist_id
FROM `wl_releases` as `wr`
LEFT JOIN `wl_artists` as `pa` ON `pa`.`pdl_id`=`wr`.`artist_id`
WHERE wr.status = '1'  
GROUP BY `wr`.`id`
) CI_count_all_results
ERROR - 2026-03-09 14:56:38 --> Query error: Unknown column 'wr.status' in 'WHERE' - Invalid query: UPDATE `wl_sessions` SET `timestamp` = 1773048398
WHERE wr.status = '1'  
AND `id` = '881i5veak49ur4nct3vutu6vkjss5ivv'
AND `ip_address` = '160.202.37.24'
