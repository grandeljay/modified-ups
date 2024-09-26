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
    'LONG_DESCRIPTION'          => 'Mode d\'expédition UPS',
    'STATUS_TITLE'              => 'Activer le module ? ',
    'STATUS_DESC'               => 'Permet l\'envoi via UPS.',
    'TEXT_TITLE'                => 'UPS',
    'TEXT_TITLE_WEIGHT'         => 'UPS %s (%s kg)',

    /** Interface */
    'BUTTON_ADD'                => 'Ajouter',
    'BUTTON_APPLY'              => 'Reprendre',
    'BUTTON_CANCEL'             => 'Annuler',

    /** Required for modified compatibility */
    'ALLOWED_TITLE'             => '',
    'ALLOWED_DESC'              => '',

    /** Sort Order */
    'SORT_ORDER_TITLE'          => 'Ordre de tri',
    'SORT_ORDER_DESC'           => 'Détermine le tri dans Admin et Checkout. Les chiffres les plus bas sont affichés en premier.',

    /** Debug */
    'DEBUG_ENABLE_TITLE'        => 'Mode de débogage',
    'DEBUG_ENABLE_DESC'         => 'Activer le mode de débogage ? Des informations supplémentaires sont affichées, par exemple comment les frais de port ont été calculés. Visible uniquement par les admins.',

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
    Group::SHIPPING_WEIGHT . '_START_TITLE' => 'Poids',
    Group::SHIPPING_WEIGHT . '_START_DESC'  => 'C\'est ici que se trouvent tous les paramètres relatifs à l\'emballage et au poids. Cliquez sur le groupe pour ouvrir les paramètres.',

    Group::SHIPPING_WEIGHT . '_MAX_TITLE'   => 'Poids maximal',
    Group::SHIPPING_WEIGHT . '_MAX_DESC'    => 'Poids maximal en kilogrammes qu\'un article peut avoir. Si un article du panier dépasse cette valeur, le mode d\'expédition est masqué.',
    Group::SHIPPING_WEIGHT . '_IDEAL_TITLE' => 'Poids idéal',
    Group::SHIPPING_WEIGHT . '_IDEAL_DESC'  => 'Poids cible lors du calcul des frais d\'expédition afin d\'augmenter la sécurité du transport, par exemple. Les colis sont emballés jusqu\'à cette valeur, sauf si un article pèse plus.',

    Group::SHIPPING_WEIGHT . '_END_TITLE'   => '',
    Group::SHIPPING_WEIGHT . '_END_DESC'    => '',
];

/**
 * Methods
 */
$translations_methods = [
    Group::SHIPPING_METHODS . '_START_TITLE'     => 'Modes d\'expédition',
    Group::SHIPPING_METHODS . '_START_DESC'      => 'Quels sont les modes d\'expédition UPS à proposer ?',
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
    Group::SHIPPING_NATIONAL . '_START_TITLE'   => 'Envoi national',
    Group::SHIPPING_NATIONAL . '_START_DESC'    => 'C\'est ici que se trouvent tous les paramètres relatifs à l\'envoi national. Cliquez sur le groupe pour ouvrir les paramètres.',

    Group::SHIPPING_NATIONAL . '_COUNTRY_TITLE' => 'Envoi national',
    Group::SHIPPING_NATIONAL . '_COUNTRY_DESC'  => sprintf(
        'L\'emplacement de la boutique en ligne est actuellement %s et peut être modifié sous %s.',
        sprintf(
            '<code>%s</code>',
            $country['countries_name'] ?? 'Inconnu'
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

    $translations_national[$method_group . '_COSTS_TITLE'] = 'Poids et coûts';
    $translations_national[$method_group . '_COSTS_DESC']  = 'Affectation des frais de port pour différents poids.';
    $translations_national[$method_group . '_KG_TITLE']    = 'Prix au kilogramme';
    $translations_national[$method_group . '_KG_DESC']     = 'Ne s\'applique qu\'à partir du poids maximum défini (par exemple 20 Kg).';
    $translations_national[$method_group . '_MIN_TITLE']   = 'Taux minimum par paquet';
    $translations_national[$method_group . '_MIN_DESC']    = 'L\'expédition n\'est jamais proposée en dessous de ce prix.';

    $translations_groups[$method_group . '_EXCLUDED_TITLE'] = 'Codes postaux exclus';
    $translations_groups[$method_group . '_EXCLUDED_DESC']  = 'Cette méthode d\'expédition est masquée si le client a indiqué dans son adresse d\'expédition un de ces codes postaux listés.';

    $translations_national[$method_group . '_WEIGHT_HEAD_TITLE'] = 'Poids';
    $translations_national[$method_group . '_WEIGHT_HEAD_DESC']  = 'Poids maximum autorisé (en Kg) pour ce prix.';
    $translations_national[$method_group . '_COSTS_HEAD_TITLE']  = 'Coûts';
    $translations_national[$method_group . '_COSTS_HEAD_DESC']   = 'Frais d\'expédition pour le poids en EUR.';

    $translations_national[$method_group . '_END_TITLE'] = '';
    $translations_national[$method_group . '_END_DESC']  = '';
}

/**
 * Groups
 */
$translations_groups = [];

foreach (grandeljayups::$methods_international as $group) {
    $group_letter = substr($group, -1, 1);
    $group_title  = sprintf('Groupe de pays %s', $group_letter);

    $translations_groups = array_merge(
        $translations_groups,
        [
            $group . '_START_TITLE'     => $group_title,
            $group . '_START_DESC'      => 'C\'est ici que se trouvent tous les paramètres relatifs à l\'expédition internationale. Cliquez sur le groupe pour ouvrir les paramètres.',

            $group . '_COUNTRIES_TITLE' => $group_title,
            $group . '_COUNTRIES_DESC'  => 'Les codes de pays peuvent être indiqués ici en les séparant par une virgule.',

            $group . '_END_TITLE'       => '',
            $group . '_END_DESC'        => '',
        ]
    );

    foreach (grandeljayups::$methods[$group] as $method_name) {
        $method_group = $group . '_' . $method_name;

        $translations_groups[$method_group . '_START_TITLE'] = '';
        $translations_groups[$method_group . '_START_DESC']  = '';

        $translations_groups[$method_group . '_COSTS_TITLE'] = 'Poids et coûts';
        $translations_groups[$method_group . '_COSTS_DESC']  = 'Affectation des frais de port pour différents poids.';
        $translations_groups[$method_group . '_KG_TITLE']    = 'Prix au kilogramme';
        $translations_groups[$method_group . '_KG_DESC']     = 'Ne s\'applique qu\'à partir du poids maximum défini (par exemple 20 Kg).';
        $translations_groups[$method_group . '_MIN_TITLE']   = 'Taux minimum par paquet';
        $translations_groups[$method_group . '_MIN_DESC']    = 'L\'expédition n\'est jamais proposée en dessous de ce prix.';

        $translations_groups[$method_group . '_EXCLUDED_TITLE'] = 'Codes postaux exclus';
        $translations_groups[$method_group . '_EXCLUDED_DESC']  = 'Cette méthode d\'expédition est masquée si le client a indiqué dans son adresse d\'expédition un de ces codes postaux listés.';

        $translations_groups[$method_group . '_WEIGHT_HEAD_TITLE'] = 'Poids';
        $translations_groups[$method_group . '_WEIGHT_HEAD_DESC']  = 'Poids maximum autorisé (en Kg) pour ce prix.';
        $translations_groups[$method_group . '_COSTS_HEAD_TITLE']  = 'Coûts';
        $translations_groups[$method_group . '_COSTS_HEAD_DESC']   = 'Frais d\'expédition pour le poids en EUR.';

        $translations_groups[$method_group . '_END_TITLE'] = '';
        $translations_groups[$method_group . '_END_DESC']  = '';
    }
}

$translations_groups[Group::SHIPPING_GROUP_F . '_START_DESC'] = 'Ce groupe concerne tous les pays qui ne sont pas définis dans les groupes A-E ci-dessus.';

/**
 * Surcharges
 */
$translations_surcharges = [
    Group::SURCHARGES . '_START_TITLE'                     => 'Suppléments',
    Group::SURCHARGES . '_START_DESC'                      => 'C\'est ici que se trouvent tous les paramètres relatifs aux majorations. Cliquez sur le groupe pour ouvrir les paramètres.',

    Group::SURCHARGES . '_SURCHARGES_TITLE'                => 'Suppléments',
    Group::SURCHARGES . '_SURCHARGES_DESC'                 => '',

    Group::SURCHARGES . '_NAME_TITLE'                      => 'Nom',
    Group::SURCHARGES . '_NAME_DESC'                       => 'Terme désignant le service.',
    Group::SURCHARGES . '_SURCHARGE_TITLE'                 => 'Service',
    Group::SURCHARGES . '_SURCHARGE_DESC'                  => 'Quel est le montant de la majoration ?',
    Group::SURCHARGES . '_TYPE_TITLE'                      => 'Art',
    Group::SURCHARGES . '_TYPE_DESC'                       => 'De quelle majoration s\'agit-il ?',
    Group::SURCHARGES . '_TYPE_FIXED'                      => 'Fixe',
    Group::SURCHARGES . '_TYPE_PERCENT'                    => 'Pourcentage',
    Group::SURCHARGES . '_PER_PACKAGE_TITLE'               => 'Par paquet',
    Group::SURCHARGES . '_PER_PACKAGE_DESC'                => 'Le supplément est calculé pour chaque paquet.',
    Group::SURCHARGES . '_FOR_WEIGHT_TITLE'                => 'À partir du poids',
    Group::SURCHARGES . '_FOR_WEIGHT_DESC'                 => 'Le supplément est calculé pour les colis de la valeur indiquée.',
    Group::SURCHARGES . '_FOR_METHOD_TITLE'                => 'Pour le type d\'expédition',
    Group::SURCHARGES . '_FOR_METHOD_DESC'                 => 'à quel type d\'expédition (standard, saver, 12:00, express, plus) la majoration doit s\'appliquer.',
    Group::SURCHARGES . '_FOR_METHOD_ALL'                  => '-- Tous --',
    Group::SURCHARGES . '_FOR_METHOD_ALL_OTHERS'           => sprintf('-- Tous sauf %s --', $translations_general['SHIPPING_METHOD_STANDARD']),
    Group::SURCHARGES . '_DURATION_START_TITLE'            => 'De',
    Group::SURCHARGES . '_DURATION_START_DESC'             => 'En option, vous pouvez choisir. Date à partir de laquelle le supplément doit s\'appliquer. Les années sont automatiquement mises à jour.',
    Group::SURCHARGES . '_DURATION_END_TITLE'              => 'Jusqu\'à',
    Group::SURCHARGES . '_DURATION_END_DESC'               => 'En option, vous pouvez choisir. Jusqu\'à quelle date le supplément doit s\'appliquer. Les années sont automatiquement mises à jour.',

    Group::SURCHARGES . '_PICK_AND_PACK_TITLE'             => 'Pick &amp; Pack',
    Group::SURCHARGES . '_PICK_AND_PACK_DESC'              => 'Frais encourus pour rassembler et emballer la commande.',

    Group::SURCHARGES . '_PICK_AND_PACK_WEIGHT_HEAD_TITLE' => 'Poids',
    Group::SURCHARGES . '_PICK_AND_PACK_WEIGHT_HEAD_DESC'  => 'Poids maximum autorisé (en Kg) pour ce prix.',
    Group::SURCHARGES . '_PICK_AND_PACK_COSTS_HEAD_TITLE'  => 'Coûts',
    Group::SURCHARGES . '_PICK_AND_PACK_COSTS_HEAD_DESC'   => 'Frais d\'expédition pour le poids en EUR.',

    Group::SURCHARGES . '_ROUND_UP_TITLE'                  => 'Arrondir les frais de port ?',
    Group::SURCHARGES . '_ROUND_UP_DESC'                   => 'Permet de présenter les frais d\'expédition de manière plus cohérente en arrondissant toujours les montants (à XX,90 € par exemple) vers le haut.',
    Group::SURCHARGES . '_ROUND_UP_TO_TITLE'               => 'Arrondir à l\'unité supérieure',
    Group::SURCHARGES . '_ROUND_UP_TO_DESC'                => 'A quelle décimale doit-on toujours arrondir ?',

    Group::SURCHARGES . '_END_TITLE'                       => '',
    Group::SURCHARGES . '_END_DESC'                        => '',
];

/**
 * Bulk Price Change Preview
 */
$translations_bulk_price = [
    Group::BULK_PRICE . '_START_TITLE'          => 'Changement de prix en vrac',
    Group::BULK_PRICE . '_START_DESC'           => 'Multiplie tous les prix d\'expédition du module par un facteur. Les modifications ne sont qu\'un aperçu. Ce n\'est qu\'au moment de l\'enregistrement que les valeurs sont définitivement prises en compte. Avant cela, le facteur peut être modifié autant de fois que nécessaire, sans que les prix ne changent réellement.',

    Group::BULK_PRICE . '_FACTOR_TITLE'         => 'Facteur',
    Group::BULK_PRICE . '_FACTOR_DESC'          => 'De quel facteur les prix d\'expédition doivent-ils être adaptés ?',
    Group::BULK_PRICE . '_FACTOR_PREVIEW_TITLE' => 'Aperçu',
    Group::BULK_PRICE . '_FACTOR_PREVIEW_DESC'  => 'L\'aperçu des facteurs est actif ! Veuillez vérifier tous les prix et cliquer sur "Actualiser" afin d\'appliquer les paramètres de manière permanente. Dans le cas contraire, clique sur "Annuler".',
    Group::BULK_PRICE . '_FACTOR_RESET_TITLE'   => 'Réinitialiser',

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
