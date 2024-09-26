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
    'LONG_DESCRIPTION'          => 'Metodo di spedizione UPS',
    'STATUS_TITLE'              => 'Attivare il modulo?',
    'STATUS_DESC'               => 'Consente la spedizione tramite UPS.',
    'TEXT_TITLE'                => 'UPS',
    'TEXT_TITLE_WEIGHT'         => 'UPS %s (%s kg)',

    /** Interface */
    'BUTTON_ADD'                => 'Aggiungi',
    'BUTTON_APPLY'              => 'Prendi il testimone',
    'BUTTON_CANCEL'             => 'Annullamento',

    /** Required for modified compatibility */
    'ALLOWED_TITLE'             => '',
    'ALLOWED_DESC'              => '',

    /** Sort Order */
    'SORT_ORDER_TITLE'          => 'Ordinamento',
    'SORT_ORDER_DESC'           => 'Determina l\'ordinamento nell\'Admin e nel Checkout. I numeri più bassi vengono visualizzati per primi.',

    /** Debug */
    'DEBUG_ENABLE_TITLE'        => 'Modalità Debug',
    'DEBUG_ENABLE_DESC'         => 'Attivare la modalità di debug? Vengono visualizzate informazioni aggiuntive, ad esempio come sono stati calcolati i costi di spedizione. Visibile solo per gli amministratori.',

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
    Group::SHIPPING_WEIGHT . '_START_TITLE' => 'Peso',
    Group::SHIPPING_WEIGHT . '_START_DESC'  => 'Qui troverà tutte le impostazioni relative all\'imballaggio e al peso. Clicchi sul gruppo per aprire le impostazioni.',

    Group::SHIPPING_WEIGHT . '_MAX_TITLE'   => 'Peso massimo',
    Group::SHIPPING_WEIGHT . '_MAX_DESC'    => 'Peso massimo in chilogrammi che un articolo può avere. Se un articolo nel carrello supera questo valore, il metodo di spedizione viene nascosto.',
    Group::SHIPPING_WEIGHT . '_IDEAL_TITLE' => 'Peso ideale',
    Group::SHIPPING_WEIGHT . '_IDEAL_DESC'  => 'Peso target nel calcolo dei costi di spedizione, ad esempio per aumentare la sicurezza del trasporto. I pacchi vengono imballati fino a questo valore, a meno che un articolo non pesi di più.',

    Group::SHIPPING_WEIGHT . '_END_TITLE'   => '',
    Group::SHIPPING_WEIGHT . '_END_DESC'    => '',
];

/**
 * Methods
 */
$translations_methods = [
    Group::SHIPPING_METHODS . '_START_TITLE'     => 'Metodi di spedizione',
    Group::SHIPPING_METHODS . '_START_DESC'      => 'Quali metodi di spedizione UPS dovrebbe offrire?',
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
    Group::SHIPPING_NATIONAL . '_START_TITLE'   => 'Spedizione nazionale',
    Group::SHIPPING_NATIONAL . '_START_DESC'    => 'Qui troverà tutte le impostazioni relative all\'invio nazionale. Clicchi sul gruppo per aprire le impostazioni.',

    Group::SHIPPING_NATIONAL . '_COUNTRY_TITLE' => 'Spedizione nazionale',
    Group::SHIPPING_NATIONAL . '_COUNTRY_DESC'  => sprintf(
        'La posizione del negozio online è attualmente %s e può essere modificata sotto %s.',
        sprintf(
            '<code>%s</code>',
            $country['countries_name'] ?? 'Sconosciuto'
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

    $translations_national[$method_group . '_COSTS_TITLE'] = 'Peso e costi';
    $translations_national[$method_group . '_COSTS_DESC']  = 'Allocazione dei costi di spedizione per pesi diversi.';
    $translations_national[$method_group . '_KG_TITLE']    = 'Prezzo al chilogrammo';
    $translations_national[$method_group . '_KG_DESC']     = 'Si applica solo a partire dal peso massimo definito (ad esempio, 20 kg).';
    $translations_national[$method_group . '_MIN_TITLE']   = 'Tariffa minima per pacchetto';
    $translations_national[$method_group . '_MIN_DESC']    = 'La spedizione non viene mai offerta al di sotto di questo prezzo.';

    $translations_groups[$method_group . '_EXCLUDED_TITLE'] = 'Codici postali esclusi';
    $translations_groups[$method_group . '_EXCLUDED_DESC']  = 'Questo metodo di spedizione è nascosto se il cliente ha inserito uno dei codici postali elencati nell\'indirizzo di spedizione.';

    $translations_national[$method_group . '_WEIGHT_HEAD_TITLE'] = 'Peso';
    $translations_national[$method_group . '_WEIGHT_HEAD_DESC']  = 'Peso massimo consentito (in kg) per questo prezzo.';
    $translations_national[$method_group . '_COSTS_HEAD_TITLE']  = 'Costi';
    $translations_national[$method_group . '_COSTS_HEAD_DESC']   = 'Spese di spedizione per peso in EUR.';

    $translations_national[$method_group . '_END_TITLE'] = '';
    $translations_national[$method_group . '_END_DESC']  = '';
}

/**
 * Groups
 */
$translations_groups = [];

foreach (grandeljayups::$methods_international as $group) {
    $group_letter = substr($group, -1, 1);
    $group_title  = sprintf('Gruppo di Paesi %s', $group_letter);

    $translations_groups = array_merge(
        $translations_groups,
        [
            $group . '_START_TITLE'     => $group_title,
            $group . '_START_DESC'      => 'Qui troverà tutte le impostazioni relative alla spedizione internazionale. Clicchi sul gruppo per aprire le impostazioni.',

            $group . '_COUNTRIES_TITLE' => $group_title,
            $group . '_COUNTRIES_DESC'  => 'I codici paese possono essere inseriti qui separati da virgole.',

            $group . '_END_TITLE'       => '',
            $group . '_END_DESC'        => '',
        ]
    );

    foreach (grandeljayups::$methods[$group] as $method_name) {
        $method_group = $group . '_' . $method_name;

        $translations_groups[$method_group . '_START_TITLE'] = '';
        $translations_groups[$method_group . '_START_DESC']  = '';

        $translations_groups[$method_group . '_COSTS_TITLE'] = 'Peso e costi';
        $translations_groups[$method_group . '_COSTS_DESC']  = 'Allocazione dei costi di spedizione per pesi diversi.';
        $translations_groups[$method_group . '_KG_TITLE']    = 'Prezzo al chilogrammo';
        $translations_groups[$method_group . '_KG_DESC']     = 'Si applica solo a partire dal peso massimo definito (ad esempio, 20 kg).';
        $translations_groups[$method_group . '_MIN_TITLE']   = 'Tariffa minima per pacchetto';
        $translations_groups[$method_group . '_MIN_DESC']    = 'La spedizione non viene mai offerta al di sotto di questo prezzo.';

        $translations_groups[$method_group . '_EXCLUDED_TITLE'] = 'Codici postali esclusi';
        $translations_groups[$method_group . '_EXCLUDED_DESC']  = 'Questo metodo di spedizione è nascosto se il cliente ha inserito uno dei codici postali elencati nell\'indirizzo di spedizione.';

        $translations_groups[$method_group . '_WEIGHT_HEAD_TITLE'] = 'Peso';
        $translations_groups[$method_group . '_WEIGHT_HEAD_DESC']  = 'Peso massimo consentito (in kg) per questo prezzo.';
        $translations_groups[$method_group . '_COSTS_HEAD_TITLE']  = 'Costi';
        $translations_groups[$method_group . '_COSTS_HEAD_DESC']   = 'Spese di spedizione per peso in EUR.';

        $translations_groups[$method_group . '_END_TITLE'] = '';
        $translations_groups[$method_group . '_END_DESC']  = '';
    }
}

$translations_groups[Group::SHIPPING_GROUP_F . '_START_DESC'] = 'Questo gruppo riguarda tutti i Paesi non definiti nei gruppi A-E di cui sopra.';

/**
 * Surcharges
 */
$translations_surcharges = [
    Group::SURCHARGES . '_START_TITLE'                     => 'Impatti',
    Group::SURCHARGES . '_START_DESC'                      => 'Qui troverà tutte le impostazioni relative ai supplementi. Clicchi sul gruppo per aprire le impostazioni.',

    Group::SURCHARGES . '_SURCHARGES_TITLE'                => 'Impatti',
    Group::SURCHARGES . '_SURCHARGES_DESC'                 => '',

    Group::SURCHARGES . '_NAME_TITLE'                      => 'Nome',
    Group::SURCHARGES . '_NAME_DESC'                       => 'Termine per il servizio.',
    Group::SURCHARGES . '_SURCHARGE_TITLE'                 => 'Impatto',
    Group::SURCHARGES . '_SURCHARGE_DESC'                  => 'A quanto ammonta il supplemento?',
    Group::SURCHARGES . '_TYPE_TITLE'                      => 'Arte',
    Group::SURCHARGES . '_TYPE_DESC'                       => 'Di che tipo di sovrapprezzo stiamo parlando?',
    Group::SURCHARGES . '_TYPE_FIXED'                      => 'Fisso',
    Group::SURCHARGES . '_TYPE_PERCENT'                    => 'Percentuale',
    Group::SURCHARGES . '_PER_PACKAGE_TITLE'               => 'Per confezione',
    Group::SURCHARGES . '_PER_PACKAGE_DESC'                => 'Il supplemento viene calcolato per ogni pacchetto.',
    Group::SURCHARGES . '_FOR_WEIGHT_TITLE'                => 'Dal peso',
    Group::SURCHARGES . '_FOR_WEIGHT_DESC'                 => 'Il supplemento viene calcolato per i pacchi del valore specificato.',
    Group::SURCHARGES . '_FOR_METHOD_TITLE'                => 'Per il metodo di spedizione',
    Group::SURCHARGES . '_FOR_METHOD_DESC'                 => 'Per quale metodo di spedizione (Standard, Saver, 12:00, Express, Plus) deve applicare il supplemento.',
    Group::SURCHARGES . '_FOR_METHOD_ALL'                  => '-- Tutti...',
    Group::SURCHARGES . '_FOR_METHOD_ALL_OTHERS'           => sprintf('-- Tutti tranne %s --', $translations_general['SHIPPING_METHOD_STANDARD']),
    Group::SURCHARGES . '_DURATION_START_TITLE'            => 'Da',
    Group::SURCHARGES . '_DURATION_START_DESC'             => 'Opzionale. A partire da quando deve essere applicato il supplemento. I numeri degli anni si aggiornano automaticamente.',
    Group::SURCHARGES . '_DURATION_END_TITLE'              => 'Fino a quando',
    Group::SURCHARGES . '_DURATION_END_DESC'               => 'Opzionale. Fino al momento in cui deve essere applicato il supplemento. I numeri degli anni si aggiornano automaticamente.',

    Group::SURCHARGES . '_PICK_AND_PACK_TITLE'             => 'Pick &amp; Pack',
    Group::SURCHARGES . '_PICK_AND_PACK_DESC'              => 'I costi sostenuti per l\'assemblaggio e l\'imballaggio dell\'ordine.',

    Group::SURCHARGES . '_PICK_AND_PACK_WEIGHT_HEAD_TITLE' => 'Peso',
    Group::SURCHARGES . '_PICK_AND_PACK_WEIGHT_HEAD_DESC'  => 'Peso massimo consentito (in kg) per questo prezzo.',
    Group::SURCHARGES . '_PICK_AND_PACK_COSTS_HEAD_TITLE'  => 'Costi',
    Group::SURCHARGES . '_PICK_AND_PACK_COSTS_HEAD_DESC'   => 'Spese di spedizione per peso in EUR.',

    Group::SURCHARGES . '_ROUND_UP_TITLE'                  => 'Arrotondare i costi di spedizione?',
    Group::SURCHARGES . '_ROUND_UP_DESC'                   => 'Consente di visualizzare le spese di spedizione in modo più uniforme, arrotondando sempre per eccesso gli importi (ad esempio, a XX,90 €).',
    Group::SURCHARGES . '_ROUND_UP_TO_TITLE'               => 'Arrotondare fino a',
    Group::SURCHARGES . '_ROUND_UP_TO_DESC'                => 'A quale cifra decimale si deve sempre arrotondare per eccesso?',

    Group::SURCHARGES . '_END_TITLE'                       => '',
    Group::SURCHARGES . '_END_DESC'                        => '',
];

/**
 * Bulk Price Change Preview
 */
$translations_bulk_price = [
    Group::BULK_PRICE . '_START_TITLE'          => 'Variazione del prezzo alla rinfusa',
    Group::BULK_PRICE . '_START_DESC'           => 'Moltiplica tutti i prezzi di spedizione nel modulo per un fattore. Le modifiche sono solo un\'anteprima. I valori non sono definitivi finché non vengono salvati. Prima di allora, il fattore può essere modificato un numero qualsiasi di volte senza che i prezzi cambino effettivamente.',

    Group::BULK_PRICE . '_FACTOR_TITLE'         => 'Fattore',
    Group::BULK_PRICE . '_FACTOR_DESC'          => 'In base a quale fattore dovrebbero essere adeguati i prezzi di spedizione?',
    Group::BULK_PRICE . '_FACTOR_PREVIEW_TITLE' => 'Anteprima',
    Group::BULK_PRICE . '_FACTOR_PREVIEW_DESC'  => 'L\'anteprima del fattore è attiva! Controllare tutti i prezzi e fare clic su "Aggiorna" per applicare le impostazioni in modo permanente. Altrimenti, fare clic su "Annulla".',
    Group::BULK_PRICE . '_FACTOR_RESET_TITLE'   => 'Reset',

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
