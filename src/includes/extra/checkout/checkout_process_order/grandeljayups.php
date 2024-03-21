<?php

/**
 * UPS
 *
 * @author  Jay Trees <ups@grandels.email>
 * @link    https://github.com/grandeljay/modified-ups
 * @package GrandelJayUPS
 */

namespace Grandeljay\Ups;

if (\rth_is_module_disabled(\grandeljayups::NAME)) {
    return;
}

$methods = [
    'grandeljayups_standard',
    'grandeljayups_expedited',
    'grandeljayups_saver',
    'grandeljayups_1200',
    'grandeljayups_express',
    'grandeljayups_plus',
];

if (!in_array($order->info['shipping_class'], $methods, true)) {
    return;
}

/**
 * Simplify and update shipping name
 */
$language_file = \sprintf(
    DIR_FS_CATALOG . 'lang/%s/modules/shipping/%s.php',
    $_SESSION['language'],
    \grandeljayups::class
);

require $language_file;

$simplified_method = \substr($order->info['shipping_class'], \strlen(\grandeljayups::class . '_'));
$simplified_name   = sprintf(
    constant(\grandeljayups::NAME . '_TEXT_TITLE_WEIGHT'),
    constant(\grandeljayups::NAME . '_SHIPPING_METHOD_' . \strtoupper($simplified_method)),
    round($_SESSION['cart']->weight, 2)
);

/**
 * Table `orders`
 */
$sql_data_array = [
    'shipping_method' => $simplified_name,
];

\xtc_db_perform(\TABLE_ORDERS, $sql_data_array, 'update', sprintf('orders_id = %d', $insert_id));

/**
 * Table `orders_total`
 */
\xtc_db_query(
    \sprintf(
        'UPDATE `%s`
            SET `title`     = "%s"
          WHERE `orders_id` = %d
            AND `class`     = "ot_shipping"',
        \TABLE_ORDERS_TOTAL,
        $simplified_name,
        $insert_id
    )
);
