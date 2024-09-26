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
$translations_general = [
    /** Module */
    'TITLE'                     => 'grandeljay - UPS',
    'LONG_DESCRIPTION'          => 'UPS Versandart',
    'STATUS_TITLE'              => 'Modul aktivieren?',
    'STATUS_DESC'               => 'Ermöglicht den Versand via UPS.',
    'TEXT_TITLE'                => 'UPS',
    'TEXT_TITLE_WEIGHT'         => 'UPS %s (%s kg)',

    /** Interface */
    'BUTTON_ADD'                => 'Hinzufügen',
    'BUTTON_APPLY'              => 'Übernehmen',
    'BUTTON_CANCEL'             => 'Abbrechen',

    /** Required for modified compatibility */
    'ALLOWED_TITLE'             => '',
    'ALLOWED_DESC'              => '',

    /** Sort Order */
    'SORT_ORDER_TITLE'          => 'Sortierreihenfolge',
    'SORT_ORDER_DESC'           => 'Bestimmt die Sortierung im Admin und Checkout. Niedrigste Zahlen werden zuerst angezeigt.',

    /** Debug */
    'DEBUG_ENABLE_TITLE'        => 'Debug-Modus',
    'DEBUG_ENABLE_DESC'         => 'Debug-Modus aktivieren? Es werden zusätzliche Informationen angezeigt z. B. wie die Versandkosten errechnet wurden. Nur für Admins sichtbar.',

    /** Shipping Method */
    'SHIPPING_METHOD_STANDARD'  => 'Standard',
    'SHIPPING_METHOD_EXPEDITED' => 'Expedited',
    'SHIPPING_METHOD_SAVER'     => 'Saver 18:00',
    'SHIPPING_METHOD_1200'      => 'Express 12:00',
    'SHIPPING_METHOD_EXPRESS'   => 'Express 10:30',
    'SHIPPING_METHOD_PLUS'      => 'Express Plus',
];

/**
 * Weight
 */
$translations_weight = [
    Group::SHIPPING_WEIGHT . '_START_TITLE' => 'Gewicht',
    Group::SHIPPING_WEIGHT . '_START_DESC'  => 'Hier befinden sich alle Einstellungen bezüglich des Verpackens und Gewichts. Klicken Sie auf die Gruppe um die Einstellungen zu öffnen.',

    Group::SHIPPING_WEIGHT . '_MAX_TITLE'   => 'Maximalgewicht',
    Group::SHIPPING_WEIGHT . '_MAX_DESC'    => 'Maximalgewicht in Kilogramm, das ein Artikel haben darf. Wenn ein Artikel im Warenkorb diesen Wert überschreitet, wird die Versandart ausgeblendet.',
    Group::SHIPPING_WEIGHT . '_IDEAL_TITLE' => 'Idealgewicht',
    Group::SHIPPING_WEIGHT . '_IDEAL_DESC'  => 'Zielgewicht beim berechnen der Versandkosten um z. B. die Transportsicherheit zu erhöhen. Pakete werden bis zu diesem Wert gepackt, außer ein Artikel wiegt mehr.',

    Group::SHIPPING_WEIGHT . '_END_TITLE'   => '',
    Group::SHIPPING_WEIGHT . '_END_DESC'    => '',
];

/**
 * Methods
 */
$translations_methods = [
    Group::SHIPPING_METHODS . '_START_TITLE'     => 'Versandarten',
    Group::SHIPPING_METHODS . '_START_DESC'      => 'Welche UPS Versandarten sollen angeboten werden?',
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
];
/** */

/**
 * Shipping
 */
require_once DIR_FS_CATALOG . 'includes/modules/shipping/grandeljayups.php';

/**
 * National
 */
$translations_national = [
    Group::SHIPPING_NATIONAL . '_START_TITLE'   => 'Nationaler Versand',
    Group::SHIPPING_NATIONAL . '_START_DESC'    => 'Hier befinden sich alle Einstellungen bezüglich des nationalen Versands. Klicken Sie auf die Gruppe um die Einstellungen zu öffnen.',

    Group::SHIPPING_NATIONAL . '_COUNTRY_TITLE' => 'Nationaler Versand',
    Group::SHIPPING_NATIONAL . '_COUNTRY_DESC'  => sprintf(
        'Standort des Online Shops ist aktuell %s und kann unter %s angepasst werden.',
        sprintf(
            '<code>%s</code>',
            $country['countries_name'] ?? 'Unbekannt'
        ),
        sprintf(
            '<a href="/' . DIR_ADMIN . 'configuration.php?gID=1">%s -> %s</a>',
            defined('BOX_HEADING_CONFIGURATION') ? BOX_HEADING_CONFIGURATION : 'BOX_HEADING_CONFIGURATION',
            defined('BOX_CONFIGURATION_1') ? BOX_CONFIGURATION_1 : 'BOX_CONFIGURATION_1',
        )
    ),

    Group::SHIPPING_NATIONAL . '_END_TITLE'     => '',
    Group::SHIPPING_NATIONAL . '_END_DESC'      => '',
];

foreach (grandeljayups::$methods[Group::SHIPPING_NATIONAL] as $method_name) {
    $method_group = Group::SHIPPING_NATIONAL . '_' . $method_name;

    $translations_national[$method_group . '_START_TITLE'] = '';
    $translations_national[$method_group . '_START_DESC']  = '';

    $translations_national[$method_group . '_COSTS_TITLE'] = 'Gewicht & Kosten';
    $translations_national[$method_group . '_COSTS_DESC']  = 'Zuordnung der Versandkosten für verschiedene Gewichte.';
    $translations_national[$method_group . '_KG_TITLE']    = 'Kilogramm-Preis';
    $translations_national[$method_group . '_KG_DESC']     = 'Gilt erst ab dem definierten Maximalgewicht (z. B. 20 Kg).';
    $translations_national[$method_group . '_MIN_TITLE']   = 'Mindestrate pro Paket';
    $translations_national[$method_group . '_MIN_DESC']    = 'Versand wird nie unter diesem Preis angeboten.';

    $translations_national[$method_group . '_EXCLUDED_TITLE'] = 'Ausgeschlossene Postleitzahlen';
    $translations_national[$method_group . '_EXCLUDED_DESC']  = 'Diese Versandmethode wird ausgeblendet wenn der Kunde in seiner Versandadresse eines dieser gelisteten Postleitzahlen angegeben hat.';

    $translations_national[$method_group . '_WEIGHT_HEAD_TITLE'] = 'Gewicht';
    $translations_national[$method_group . '_WEIGHT_HEAD_DESC']  = 'Maximal zulässiges Gewicht (in Kg) für diesen Preis.';
    $translations_national[$method_group . '_COSTS_HEAD_TITLE']  = 'Kosten';
    $translations_national[$method_group . '_COSTS_HEAD_DESC']   = 'Versandkosten für Gewicht in EUR.';

    $translations_national[$method_group . '_END_TITLE'] = '';
    $translations_national[$method_group . '_END_DESC']  = '';
}

/**
 * Groups
 */
$translations_groups = [];

foreach (grandeljayups::$methods_international as $group) {
    $group_letter = substr($group, -1, 1);
    $group_title  = sprintf('Ländergruppe %s', $group_letter);

    $translations_groups = array_merge(
        $translations_groups,
        [
            $group . '_START_TITLE'     => $group_title,
            $group . '_START_DESC'      => 'Hier befinden sich alle Einstellungen bezüglich des internationalen Versands. Klicken Sie auf die Gruppe um die Einstellungen zu öffnen.',

            $group . '_COUNTRIES_TITLE' => $group_title,
            $group . '_COUNTRIES_DESC'  => 'Ländercodes können hier Komma-getrennt angegeben werden.',

            $group . '_END_TITLE'       => '',
            $group . '_END_DESC'        => '',
        ]
    );

    foreach (grandeljayups::$methods[$group] as $method_name) {
        $method_group = $group . '_' . $method_name;

        $translations_groups[$method_group . '_START_TITLE'] = '';
        $translations_groups[$method_group . '_START_DESC']  = '';

        $translations_groups[$method_group . '_COSTS_TITLE'] = 'Gewicht & Kosten';
        $translations_groups[$method_group . '_COSTS_DESC']  = 'Zuordnung der Versandkosten für verschiedene Gewichte.';
        $translations_groups[$method_group . '_KG_TITLE']    = 'Kilogramm-Preis';
        $translations_groups[$method_group . '_KG_DESC']     = 'Gilt erst ab dem definierten Maximalgewicht (z. B. 20 Kg).';
        $translations_groups[$method_group . '_MIN_TITLE']   = 'Mindestrate pro Paket';
        $translations_groups[$method_group . '_MIN_DESC']    = 'Versand wird nie unter diesem Preis angeboten.';

        $translations_groups[$method_group . '_EXCLUDED_TITLE'] = 'Ausgeschlossene Postleitzahlen';
        $translations_groups[$method_group . '_EXCLUDED_DESC']  = 'Diese Versandmethode wird ausgeblendet wenn der Kunde in seiner Versandadresse eines dieser gelisteten Postleitzahlen angegeben hat.';

        $translations_groups[$method_group . '_WEIGHT_HEAD_TITLE'] = 'Gewicht';
        $translations_groups[$method_group . '_WEIGHT_HEAD_DESC']  = 'Maximal zulässiges Gewicht (in Kg) für diesen Preis.';
        $translations_groups[$method_group . '_COSTS_HEAD_TITLE']  = 'Kosten';
        $translations_groups[$method_group . '_COSTS_HEAD_DESC']   = 'Versandkosten für Gewicht in EUR.';

        $translations_groups[$method_group . '_END_TITLE'] = '';
        $translations_groups[$method_group . '_END_DESC']  = '';
    }
}

$translations_groups[Group::SHIPPING_GROUP_F . '_START_DESC'] = 'Diese Gruppe betrifft alle Länder, die nicht in den obigen Gruppen A-E definiert wurden.';

/**
 * Surcharges
 */
$translations_surcharges = [
    Group::SURCHARGES . '_START_TITLE'                     => 'Aufschläge',
    Group::SURCHARGES . '_START_DESC'                      => 'Hier befinden sich alle Einstellungen bezüglich der Aufschläge. Klicken Sie auf die Gruppe um die Einstellungen zu öffnen.',

    Group::SURCHARGES . '_SURCHARGES_TITLE'                => 'Aufschläge',
    Group::SURCHARGES . '_SURCHARGES_DESC'                 => '',

    Group::SURCHARGES . '_NAME_TITLE'                      => 'Name',
    Group::SURCHARGES . '_NAME_DESC'                       => 'Bezeichnung für den Aufschlag.',
    Group::SURCHARGES . '_SURCHARGE_TITLE'                 => 'Aufschlag',
    Group::SURCHARGES . '_SURCHARGE_DESC'                  => 'Wie hoch ist der Aufschlag?',
    Group::SURCHARGES . '_TYPE_TITLE'                      => 'Art',
    Group::SURCHARGES . '_TYPE_DESC'                       => 'Um was für einen Aufschlag handelt es sich?',
    Group::SURCHARGES . '_TYPE_FIXED'                      => 'Fest',
    Group::SURCHARGES . '_TYPE_PERCENT'                    => 'Prozentual',
    Group::SURCHARGES . '_PER_PACKAGE_TITLE'               => 'Pro Paket',
    Group::SURCHARGES . '_PER_PACKAGE_DESC'                => 'Der Aufschlag wird für jedes Paket berechnet.',
    Group::SURCHARGES . '_FOR_WEIGHT_TITLE'                => 'Ab Gewicht',
    Group::SURCHARGES . '_FOR_WEIGHT_DESC'                 => 'Der Aufschlag wird für Pakete des angegebenen Wertes berechnet.',
    Group::SURCHARGES . '_FOR_METHOD_TITLE'                => 'Für Versandart',
    Group::SURCHARGES . '_FOR_METHOD_DESC'                 => 'Für welche Versandart (Standard, Saver, 12:00, Express, Plus) der Aufschlag gelten soll.',
    Group::SURCHARGES . '_FOR_METHOD_ALL'                  => '-- Alle --',
    Group::SURCHARGES . '_FOR_METHOD_ALL_OTHERS'           => sprintf('-- Alle außer %s --', $translations_general['SHIPPING_METHOD_STANDARD']),
    Group::SURCHARGES . '_DURATION_START_TITLE'            => 'Von',
    Group::SURCHARGES . '_DURATION_START_DESC'             => 'Optional. Ab wann der Zuschlag gelten soll. Jahreszahlen aktualisieren sich automatisch.',
    Group::SURCHARGES . '_DURATION_END_TITLE'              => 'Bis',
    Group::SURCHARGES . '_DURATION_END_DESC'               => 'Optional. Bis wann der Zuschlag gelten soll. Jahreszahlen aktualisieren sich automatisch.',

    Group::SURCHARGES . '_PICK_AND_PACK_TITLE'             => 'Pick & Pack',
    Group::SURCHARGES . '_PICK_AND_PACK_DESC'              => 'Kosten die beim zusammenstellen und verpacken der Bestellung entstehen.',

    Group::SURCHARGES . '_PICK_AND_PACK_WEIGHT_HEAD_TITLE' => 'Gewicht',
    Group::SURCHARGES . '_PICK_AND_PACK_WEIGHT_HEAD_DESC'  => 'Maximal zulässiges Gewicht (in Kg) für diesen Preis.',
    Group::SURCHARGES . '_PICK_AND_PACK_COSTS_HEAD_TITLE'  => 'Kosten',
    Group::SURCHARGES . '_PICK_AND_PACK_COSTS_HEAD_DESC'   => 'Versandkosten für Gewicht in EUR.',

    Group::SURCHARGES . '_ROUND_UP_TITLE'                  => 'Versandkosten aufrunden?',
    Group::SURCHARGES . '_ROUND_UP_DESC'                   => 'Ermöglicht es die Versandkosten einheitlicher darzustellen, indem die Beträge immer (auf z. B. XX,90 €) aufgerundet werden.',
    Group::SURCHARGES . '_ROUND_UP_TO_TITLE'               => 'Aufrunden auf',
    Group::SURCHARGES . '_ROUND_UP_TO_DESC'                => 'Auf welche Nachkommastelle soll immer aufgerundet werden?',

    Group::SURCHARGES . '_END_TITLE'                       => '',
    Group::SURCHARGES . '_END_DESC'                        => '',
];

/**
 * Bulk Price Change Preview
 */
$translations_bulk_price = [
    Group::BULK_PRICE . '_START_TITLE'          => 'Bulk Preisänderung',
    Group::BULK_PRICE . '_START_DESC'           => 'Multipliziert alle Versandpreise im Modul um einen Faktor. Die Änderungen sind hierbei lediglich eine Vorschau. Erst beim Speichern werden die Werte final übernommen. Davor kann der Faktor beliebig oft geändert werden, ohne dass sich die Preise tatsächlich verändern.',

    Group::BULK_PRICE . '_FACTOR_TITLE'         => 'Faktor',
    Group::BULK_PRICE . '_FACTOR_DESC'          => 'Um welchen Faktor sollen die Versandpreise angepasst werden?',
    Group::BULK_PRICE . '_FACTOR_PREVIEW_TITLE' => 'Vorschau',
    Group::BULK_PRICE . '_FACTOR_PREVIEW_DESC'  => 'Faktor-Vorschau ist aktiv! Bitte prüfe alle Preise und klicke auf "Aktualisieren", um die Einstellungen dauerhaft zu übernehmen. Andernfalls, klicke auf "Abbrechen".',
    Group::BULK_PRICE . '_FACTOR_RESET_TITLE'   => 'Zurücksetzen',

    Group::BULK_PRICE . '_END_TITLE'            => '',
    Group::BULK_PRICE . '_END_DESC'             => '',
];

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
    $translations_bulk_price,
);

foreach ($translations as $key => $value) {
    $constant = 'MODULE_SHIPPING_' . strtoupper(pathinfo(__FILE__, PATHINFO_FILENAME)) . '_' . $key;

    define($constant, $value);
}
