<?php

/**
 * UPS
 *
 * @author  Jay Trees <ups@grandels.email>
 * @link    https://github.com/grandeljay/modified-ups
 * @package GrandelJayUPS
 */

if (rth_is_module_disabled('MODULE_SHIPPING_GRANDELJAYUPS')) {
    return;
}

/** Only enqueue JavaScript when module settings are open */
$grandeljayups_admin_screen = array(
    'set'    => 'shipping',
    'module' => grandeljayups::class,
    'action' => 'edit',
);

parse_str($_SERVER['QUERY_STRING'] ?? '', $query_string);

foreach ($grandeljayups_admin_screen as $key => $value) {
    if (!isset($query_string[$key]) || $query_string[$key] !== $value) {
        return;
    }
}

/** Enqueue JavaScript */
$file_name    = '/' . DIR_ADMIN . 'includes/javascript/' . grandeljayups::class . '.js';
$file_path    = DIR_FS_CATALOG .  $file_name;
$file_version = hash_file('crc32c', $file_path);
$file_url     = $file_name . '?v=' . $file_version;
?>
<script type="text/javascript" src="<?= $file_url ?>" defer></script>
