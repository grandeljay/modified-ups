<?php

/**
 * UPS
 *
 * @author  Jay Trees <ups@grandels.email>
 * @link    https://github.com/grandeljay/modified-ups
 * @package GrandelJayUPS
 */

use Grandeljay\Ups\Configuration\Group;

if (defined('TABLE_COUNTRIES') && defined('MODULE_SHIPPING_GRANDELJAYUPS_SHIPPING_NATIONAL_COUNTRY')) {
    $country_query = xtc_db_query(
        'SELECT *
           FROM `' . TABLE_COUNTRIES . '`
          WHERE `countries_id` = ' . MODULE_SHIPPING_GRANDELJAYUPS_SHIPPING_NATIONAL_COUNTRY
    );
    $country       = xtc_db_fetch_array($country_query);
}

/**
 * General
 */
$translations_general = array(
    /** Module */
    'TITLE'                     => 'grandeljay - UPS',
    'LONG_DESCRIPTION'          => 'UPS shipping method',
    'STATUS_TITLE'              => 'Activate module?',
    'STATUS_DESC'               => 'Enables shipping via UPS.',
    'TEXT_TITLE'                => 'UPS',

    /** Interface */
    'BUTTON_ADD'                => 'Add',
    'BUTTON_APPLY'              => 'Apply',
    'BUTTON_CANCEL'             => 'Cancel',

    /** Required for modified compatibility */
    'ALLOWED_TITLE'             => '',
    'ALLOWED_DESC'              => '',

    /** Sort Order */
    'SORT_ORDER_TITLE'                                                   => 'Sort order',
    'SORT_ORDER_DESC'                                                    => 'Determines the sorting in the Admin and Checkout. Lowest numbers are displayed first.',

    /** Debug */
    'DEBUG_ENABLE_TITLE'        => 'Debug mode',
    'DEBUG_ENABLE_DESC'         => 'Activate debug mode? Additional information is displayed, e.g. how the shipping costs were calculated. Only visible for admins.',

    /** Shipping Method */
    'SHIPPING_METHOD_STANDARD'  => 'Standard',
    'SHIPPING_METHOD_EXPEDITED' => 'Expedited',
    'SHIPPING_METHOD_SAVER'     => 'Saver 18:00',
    'SHIPPING_METHOD_1200'      => 'Express 12:00',
    'SHIPPING_METHOD_EXPRESS'   => 'Express 10:30',
    'SHIPPING_METHOD_PLUS'      => 'Express Plus',
);

/**
 * Weight
 */
$translations_weight = array(
    Group::SHIPPING_WEIGHT . '_START_TITLE' => 'Weight',
    Group::SHIPPING_WEIGHT . '_START_DESC'  => 'Here you will find all the settings regarding packing and weight. Click on the group to open the settings.',

    Group::SHIPPING_WEIGHT . '_MAX_TITLE'   => 'Maximum weight',
    Group::SHIPPING_WEIGHT . '_MAX_DESC'    => 'Maximum weight in kilograms that an item may have. If an item in the shopping basket exceeds this value, the shipping method is hidden.',
    Group::SHIPPING_WEIGHT . '_IDEAL_TITLE' => 'Ideal weight',
    Group::SHIPPING_WEIGHT . '_IDEAL_DESC'  => 'Target weight when calculating shipping costs, e.g. to increase transport security. Packages are packed up to this value, unless an item weighs more.',

    Group::SHIPPING_WEIGHT . '_END_TITLE'   => '',
    Group::SHIPPING_WEIGHT . '_END_DESC'    => '',
);

/**
 * Methods
 */
$translations_methods = array(
    Group::SHIPPING_METHODS . '_START_TITLE'     => 'Shipping methods',
    Group::SHIPPING_METHODS . '_START_DESC'      => 'Which UPS shipping methods should be offered?',
    Group::SHIPPING_METHODS . '_STANDARD_TITLE'  => $translations_general['SHIPPING_METHOD_STANDARD'],
    Group::SHIPPING_METHODS . '_STANDARD_DESC'   => '',
    Group::SHIPPING_METHODS . '_SAVER_TITLE'     => $translations_general['SHIPPING_METHOD_SAVER'],
    Group::SHIPPING_METHODS . '_SAVER_DESC'      => '',
    Group::SHIPPING_METHODS . '_1200_TITLE'      => $translations_general['SHIPPING_METHOD_1200'],
    Group::SHIPPING_METHODS . '_1200_DESC'       => '',
    Group::SHIPPING_METHODS . '_EXPRESS_TITLE'   => $translations_general['SHIPPING_METHOD_EXPRESS'],
    Group::SHIPPING_METHODS . '_EXPRESS_DESC'    => '',
    Group::SHIPPING_METHODS . '_PLUS_TITLE'      => $translations_general['SHIPPING_METHOD_PLUS'],
    Group::SHIPPING_METHODS . '_PLUS_DESC'       => '',
    Group::SHIPPING_METHODS . '_EXPEDITED_TITLE' => $translations_general['SHIPPING_METHOD_EXPEDITED'],
    Group::SHIPPING_METHODS . '_EXPEDITED_DESC'  => '',
    Group::SHIPPING_METHODS . '_END_TITLE'       => '',
    Group::SHIPPING_METHODS . '_END_DESC'        => '',
);
/** */

/**
 * Shipping
 */
require_once DIR_FS_CATALOG . 'includes/modules/shipping/grandeljayups.php';

/**
 * National
 */
$translations_national = array(
    Group::SHIPPING_NATIONAL . '_START_TITLE'   => 'National shipping',
    Group::SHIPPING_NATIONAL . '_START_DESC'    => 'Here you will find all the settings relating to national dispatch. Click on the group to open the settings.',

    Group::SHIPPING_NATIONAL . '_COUNTRY_TITLE' => 'National shipping',
    Group::SHIPPING_NATIONAL . '_COUNTRY_DESC'  => sprintf(
        'Location of the online shop is currently %s and can be adjusted under %s.',
        sprintf(
            '<code>%s</code>',
            $country['countries_name'] ?? 'Unknown'
        ),
        sprintf(
            '<a href="/' . DIR_ADMIN . 'configuration.php?gID=1">%s -> %s</a>',
            defined('BOX_HEADING_CONFIGURATION') ? BOX_HEADING_CONFIGURATION : 'BOX_HEADING_CONFIGURATION',
            defined('BOX_CONFIGURATION_1') ? BOX_CONFIGURATION_1 : 'BOX_CONFIGURATION_1',
        )
    ),

    Group::SHIPPING_NATIONAL . '_END_TITLE'     => '',
    Group::SHIPPING_NATIONAL . '_END_DESC'      => '',
);

foreach (grandeljayups::$methods[Group::SHIPPING_NATIONAL] as $method_name) {
    $method_group = Group::SHIPPING_NATIONAL . '_' . $method_name;

    $translations_national[$method_group . '_START_TITLE'] = '';
    $translations_national[$method_group . '_START_DESC']  = '';

    $translations_national[$method_group . '_COSTS_TITLE'] = 'Weight & Costs';
    $translations_national[$method_group . '_COSTS_DESC']  = 'Allocation of shipping costs for different weights.';
    $translations_national[$method_group . '_KG_TITLE']    = 'Price per kilogram';
    $translations_national[$method_group . '_KG_DESC']     = 'Applies only from the defined maximum weight (e.g. 20 kg).';
    $translations_national[$method_group . '_MIN_TITLE']   = 'Minimum rate per package';
    $translations_national[$method_group . '_MIN_DESC']    = 'Shipping is never offered below this price.';

    $translations_national[$method_group . '_WEIGHT_HEAD_TITLE'] = 'Weight';
    $translations_national[$method_group . '_WEIGHT_HEAD_DESC']  = 'Maximum permissible weight (in kg) for this price.';
    $translations_national[$method_group . '_COSTS_HEAD_TITLE']  = 'Costs';
    $translations_national[$method_group . '_COSTS_HEAD_DESC']   = 'Shipping costs for weight in EUR.';

    $translations_national[$method_group . '_END_TITLE'] = '';
    $translations_national[$method_group . '_END_DESC']  = '';
}

/**
 * Groups
 */
$translations_groups = array();

foreach (grandeljayups::$methods_international as $group) {
    $group_letter = substr($group, -1, 1);
    $group_title  = sprintf('Ländergruppe %s', $group_letter);

    $translations_groups = array_merge(
        $translations_groups,
        array(
            $group . '_START_TITLE'     => $group_title,
            $group . '_START_DESC'      => 'Here you will find all the settings relating to international shipping. Click on the group to open the settings.',

            $group . '_COUNTRIES_TITLE' => $group_title,
            $group . '_COUNTRIES_DESC'  => 'Country codes can be entered here comma-separated.',

            $group . '_END_TITLE'       => '',
            $group . '_END_DESC'        => '',
        )
    );

    foreach (grandeljayups::$methods[$group] as $method_name) {
        $method_group = $group . '_' . $method_name;

        $translations_groups[$method_group . '_START_TITLE'] = '';
        $translations_groups[$method_group . '_START_DESC']  = '';

        $translations_groups[$method_group . '_COSTS_TITLE'] = 'Weight & Costs';
        $translations_groups[$method_group . '_COSTS_DESC']  = 'Allocation of shipping costs for different weights.';
        $translations_groups[$method_group . '_KG_TITLE']    = 'Price per kilogram';
        $translations_groups[$method_group . '_KG_DESC']     = 'Applies only from the defined maximum weight (e.g. 20 kg).';
        $translations_groups[$method_group . '_MIN_TITLE']   = 'Minimum rate per package';
        $translations_groups[$method_group . '_MIN_DESC']    = 'Shipping is never offered below this price.';

        $translations_groups[$method_group . '_WEIGHT_HEAD_TITLE'] = 'Weight';
        $translations_groups[$method_group . '_WEIGHT_HEAD_DESC']  = 'Maximum permissible weight (in kg) for this price.';
        $translations_groups[$method_group . '_COSTS_HEAD_TITLE']  = 'Costs';
        $translations_groups[$method_group . '_COSTS_HEAD_DESC']   = 'Shipping costs for weight in EUR.';

        $translations_groups[$method_group . '_END_TITLE'] = '';
        $translations_groups[$method_group . '_END_DESC']  = '';
    }
}

$translations_groups[Group::SHIPPING_GROUP_F . '_START_DESC'] = 'This group concerns all countries not defined in groups A-E above.';

/**
 * Surcharges
 */
$translations_surcharges = array(
    Group::SURCHARGES . '_START_TITLE'                     => 'Impacts',
    Group::SURCHARGES . '_START_DESC'                      => 'Here you will find all the settings regarding the surcharges. Click on the group to open the settings.',

    Group::SURCHARGES . '_SURCHARGES_TITLE'                => 'Surcharges',
    Group::SURCHARGES . '_SURCHARGES_DESC'                 => '',

    Group::SURCHARGES . '_NAME_TITLE'                      => 'Name',
    Group::SURCHARGES . '_NAME_DESC'                       => 'Term for the serve.',
    Group::SURCHARGES . '_SURCHARGE_TITLE'                 => 'Impact',
    Group::SURCHARGES . '_SURCHARGE_DESC'                  => 'How much is the surcharge?',
    Group::SURCHARGES . '_TYPE_TITLE'                      => 'Art',
    Group::SURCHARGES . '_TYPE_DESC'                       => 'What kind of surcharge are we talking about?',
    Group::SURCHARGES . '_TYPE_FIXED'                      => 'Fixed',
    Group::SURCHARGES . '_TYPE_PERCENT'                    => 'Percentage',
    Group::SURCHARGES . '_PER_PACKAGE_TITLE'               => 'Per package',
    Group::SURCHARGES . '_PER_PACKAGE_DESC'                => 'The surcharge is calculated for each package.',
    Group::SURCHARGES . '_FOR_WEIGHT_TITLE'                => 'From weight',
    Group::SURCHARGES . '_FOR_WEIGHT_DESC'                 => 'The surcharge is calculated for parcels of the specified value.',
    Group::SURCHARGES . '_FOR_METHOD_TITLE'                => 'For shipping method',
    Group::SURCHARGES . '_FOR_METHOD_DESC'                 => 'For which shipping method (Standard, Saver, 12:00, Express, Plus) the surcharge should apply.',
    Group::SURCHARGES . '_FOR_METHOD_ALL'                  => '-- All --',
    Group::SURCHARGES . '_FOR_METHOD_ALL_OTHERS'           => sprintf('-- All except %s --', $translations_general['SHIPPING_METHOD_STANDARD']),
    Group::SURCHARGES . '_DURATION_START_TITLE'            => 'From',
    Group::SURCHARGES . '_DURATION_START_DESC'             => 'Optional. From when the surcharge should apply. Year numbers update automatically.',
    Group::SURCHARGES . '_DURATION_END_TITLE'              => 'Until',
    Group::SURCHARGES . '_DURATION_END_DESC'               => 'Optional. Until when the surcharge is to apply. Year numbers update automatically.',

    Group::SURCHARGES . '_PICK_AND_PACK_TITLE'             => 'Pick & Pack',
    Group::SURCHARGES . '_PICK_AND_PACK_DESC'              => 'Costs incurred in assembling and packing the order.',

    Group::SURCHARGES . '_PICK_AND_PACK_WEIGHT_HEAD_TITLE' => 'Weight',
    Group::SURCHARGES . '_PICK_AND_PACK_WEIGHT_HEAD_DESC'  => 'Maximum permissible weight (in kg) for this price.',
    Group::SURCHARGES . '_PICK_AND_PACK_COSTS_HEAD_TITLE'  => 'Costs',
    Group::SURCHARGES . '_PICK_AND_PACK_COSTS_HEAD_DESC'   => 'Shipping costs for weight in EUR.',

    Group::SURCHARGES . '_ROUND_UP_TITLE'                  => 'Round up shipping costs?',
    Group::SURCHARGES . '_ROUND_UP_DESC'                   => 'Allows the shipping costs to be displayed more uniformly by always rounding up the amounts (to e.g. XX.90 €).',
    Group::SURCHARGES . '_ROUND_UP_TO_TITLE'               => 'Round up to',
    Group::SURCHARGES . '_ROUND_UP_TO_DESC'                => 'To which decimal place should always be rounded up?',

    Group::SURCHARGES . '_END_TITLE'                       => '',
    Group::SURCHARGES . '_END_DESC'                        => '',
);

/**
 * Define
 */
$translations = array_merge(
    $translations_general,
    $translations_methods,
    $translations_weight,
    $translations_national,
    $translations_groups,
    $translations_surcharges,
);

foreach ($translations as $key => $value) {
    $constant = 'MODULE_SHIPPING_' . strtoupper(pathinfo(__FILE__, PATHINFO_FILENAME)) . '_' . $key;

    define($constant, $value);
}
