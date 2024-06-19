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
    'LONG_DESCRIPTION'          => 'Método de envío UPS',
    'STATUS_TITLE'              => '¿Activar módulo?',
    'STATUS_DESC'               => 'Permite el envío a través de UPS.',
    'TEXT_TITLE'                => 'UPS',
    'TEXT_TITLE_WEIGHT'         => 'UPS %s (%s kg)',

    /** Interface */
    'BUTTON_ADD'                => 'Añada',
    'BUTTON_APPLY'              => 'Asumir',
    'BUTTON_CANCEL'             => 'Cancelar',

    /** Required for modified compatibility */
    'ALLOWED_TITLE'             => '',
    'ALLOWED_DESC'              => '',

    /** Sort Order */
    'SORT_ORDER_TITLE'          => 'Orden de clasificación',
    'SORT_ORDER_DESC'           => 'Determina la clasificación en Admin y Checkout. Los números más bajos se muestran primero.',

    /** Debug */
    'DEBUG_ENABLE_TITLE'        => 'Modo depuración',
    'DEBUG_ENABLE_DESC'         => '¿Activar el modo de depuración? Se muestra información adicional, por ejemplo, cómo se han calculado los gastos de envío. Sólo visible para los administradores.',

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
    Group::SHIPPING_WEIGHT . '_START_DESC'  => 'Aquí encontrará todos los ajustes relativos al embalaje y al peso. Haga clic en el grupo para abrir los ajustes.',

    Group::SHIPPING_WEIGHT . '_MAX_TITLE'   => 'Peso máximo',
    Group::SHIPPING_WEIGHT . '_MAX_DESC'    => 'Peso máximo en kilogramos que puede tener un artículo. Si un artículo de la cesta de la compra supera este valor, se oculta el método de envío.',
    Group::SHIPPING_WEIGHT . '_IDEAL_TITLE' => 'Peso ideal',
    Group::SHIPPING_WEIGHT . '_IDEAL_DESC'  => 'Peso objetivo al calcular los gastos de envío, por ejemplo, para aumentar la seguridad del transporte. Los paquetes se embalan hasta este valor, a menos que un artículo pese más.',

    Group::SHIPPING_WEIGHT . '_END_TITLE'   => '',
    Group::SHIPPING_WEIGHT . '_END_DESC'    => '',
];

/**
 * Methods
 */
$translations_methods = [
    Group::SHIPPING_METHODS . '_START_TITLE'     => 'Métodos de envío',
    Group::SHIPPING_METHODS . '_START_DESC'      => '¿Qué métodos de envío de UPS deben ofrecerse?',
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
    Group::SHIPPING_NATIONAL . '_START_TITLE'   => 'Envíos nacionales',
    Group::SHIPPING_NATIONAL . '_START_DESC'    => 'Aquí encontrará todos los ajustes relativos al envío nacional. Haga clic en el grupo para abrir los ajustes.',

    Group::SHIPPING_NATIONAL . '_COUNTRY_TITLE' => 'Envíos nacionales',
    Group::SHIPPING_NATIONAL . '_COUNTRY_DESC'  => sprintf(
        'La ubicación de la tienda en línea es actualmente %s y puede ajustarse en %s.',
        sprintf(
            '<code>%s</code>',
            $country['countries_name'] ?? 'Desconocido'
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

    $translations_national[$method_group . '_COSTS_TITLE'] = 'Peso y costes';
    $translations_national[$method_group . '_COSTS_DESC']  = 'Asignación de gastos de envío para distintos pesos.';
    $translations_national[$method_group . '_KG_TITLE']    = 'Precio por kilogramo';
    $translations_national[$method_group . '_KG_DESC']     = 'Sólo se aplica a partir del peso máximo definido (por ejemplo, 20 kg).';
    $translations_national[$method_group . '_MIN_TITLE']   = 'Tarifa mínima por envase';
    $translations_national[$method_group . '_MIN_DESC']    = 'El envío nunca se ofrece por debajo de este precio.';

    $translations_national[$method_group . '_WEIGHT_HEAD_TITLE'] = 'Peso';
    $translations_national[$method_group . '_WEIGHT_HEAD_DESC']  = 'Peso máximo autorizado (en kg) para este precio.';
    $translations_national[$method_group . '_COSTS_HEAD_TITLE']  = 'Costes';
    $translations_national[$method_group . '_COSTS_HEAD_DESC']   = 'Gastos de envío por peso en EUR.';

    $translations_national[$method_group . '_END_TITLE'] = '';
    $translations_national[$method_group . '_END_DESC']  = '';
}

/**
 * Groups
 */
$translations_groups = [];

foreach (grandeljayups::$methods_international as $group) {
    $group_letter = substr($group, -1, 1);
    $group_title  = sprintf('Grupo de países %s', $group_letter);

    $translations_groups = array_merge(
        $translations_groups,
        [
            $group . '_START_TITLE'     => $group_title,
            $group . '_START_DESC'      => 'Aquí encontrará todos los ajustes relativos al envío internacional. Haga clic en el grupo para abrir los ajustes.',

            $group . '_COUNTRIES_TITLE' => $group_title,
            $group . '_COUNTRIES_DESC'  => 'Los códigos de país pueden introducirse aquí separados por comas.',

            $group . '_END_TITLE'       => '',
            $group . '_END_DESC'        => '',
        ]
    );

    foreach (grandeljayups::$methods[$group] as $method_name) {
        $method_group = $group . '_' . $method_name;

        $translations_groups[$method_group . '_START_TITLE'] = '';
        $translations_groups[$method_group . '_START_DESC']  = '';

        $translations_groups[$method_group . '_COSTS_TITLE'] = 'Peso y costes';
        $translations_groups[$method_group . '_COSTS_DESC']  = 'Asignación de gastos de envío para distintos pesos.';
        $translations_groups[$method_group . '_KG_TITLE']    = 'Precio por kilogramo';
        $translations_groups[$method_group . '_KG_DESC']     = 'Sólo se aplica a partir del peso máximo definido (por ejemplo, 20 kg).';
        $translations_groups[$method_group . '_MIN_TITLE']   = 'Tarifa mínima por envase';
        $translations_groups[$method_group . '_MIN_DESC']    = 'El envío nunca se ofrece por debajo de este precio.';

        $translations_groups[$method_group . '_WEIGHT_HEAD_TITLE'] = 'Peso';
        $translations_groups[$method_group . '_WEIGHT_HEAD_DESC']  = 'Peso máximo autorizado (en kg) para este precio.';
        $translations_groups[$method_group . '_COSTS_HEAD_TITLE']  = 'Costes';
        $translations_groups[$method_group . '_COSTS_HEAD_DESC']   = 'Gastos de envío por peso en EUR.';

        $translations_groups[$method_group . '_END_TITLE'] = '';
        $translations_groups[$method_group . '_END_DESC']  = '';
    }
}

$translations_groups[Group::SHIPPING_GROUP_F . '_START_DESC'] = 'Este grupo se refiere a todos los países no definidos en los grupos A-E anteriores.';

/**
 * Surcharges
 */
$translations_surcharges = [
    Group::SURCHARGES . '_START_TITLE'                     => 'Impactos',
    Group::SURCHARGES . '_START_DESC'                      => 'Aquí encontrará todos los ajustes relativos a los recargos. Haga clic en el grupo para abrir los ajustes.',

    Group::SURCHARGES . '_SURCHARGES_TITLE'                => 'Impactos',
    Group::SURCHARGES . '_SURCHARGES_DESC'                 => '',

    Group::SURCHARGES . '_NAME_TITLE'                      => 'Nombre',
    Group::SURCHARGES . '_NAME_DESC'                       => 'Término para el saque.',
    Group::SURCHARGES . '_SURCHARGE_TITLE'                 => 'Impacto',
    Group::SURCHARGES . '_SURCHARGE_DESC'                  => '¿A cuánto asciende el recargo?',
    Group::SURCHARGES . '_TYPE_TITLE'                      => 'Arte',
    Group::SURCHARGES . '_TYPE_DESC'                       => '¿De qué tipo de recargo estamos hablando?',
    Group::SURCHARGES . '_TYPE_FIXED'                      => 'Fijo',
    Group::SURCHARGES . '_TYPE_PERCENT'                    => 'Porcentaje',
    Group::SURCHARGES . '_PER_PACKAGE_TITLE'               => 'Por envase',
    Group::SURCHARGES . '_PER_PACKAGE_DESC'                => 'El recargo se calcula para cada paquete.',
    Group::SURCHARGES . '_FOR_WEIGHT_TITLE'                => 'De peso',
    Group::SURCHARGES . '_FOR_WEIGHT_DESC'                 => 'El recargo se calcula para los paquetes del valor especificado.',
    Group::SURCHARGES . '_FOR_METHOD_TITLE'                => 'Para el método de envío',
    Group::SURCHARGES . '_FOR_METHOD_DESC'                 => 'A qué método de envío (Estándar, Ahorro, 12:00, Exprés, Plus) debe aplicarse el recargo.',
    Group::SURCHARGES . '_FOR_METHOD_ALL'                  => '-- Todos --',
    Group::SURCHARGES . '_FOR_METHOD_ALL_OTHERS'           => sprintf('-- Todos excepto %s --', $translations_general['SHIPPING_METHOD_STANDARD']),
    Group::SURCHARGES . '_DURATION_START_TITLE'            => 'En',
    Group::SURCHARGES . '_DURATION_START_DESC'             => 'Opcional. A partir de cuándo debe aplicarse el recargo. Los números de año se actualizan automáticamente.',
    Group::SURCHARGES . '_DURATION_END_TITLE'              => 'Hasta',
    Group::SURCHARGES . '_DURATION_END_DESC'               => 'Opcional. Hasta cuándo debe aplicarse el recargo. Los números del año se actualizan automáticamente.',

    Group::SURCHARGES . '_PICK_AND_PACK_TITLE'             => 'Recoger y envasar',
    Group::SURCHARGES . '_PICK_AND_PACK_DESC'              => 'Gastos de montaje y embalaje del pedido.',

    Group::SURCHARGES . '_PICK_AND_PACK_WEIGHT_HEAD_TITLE' => 'Peso',
    Group::SURCHARGES . '_PICK_AND_PACK_WEIGHT_HEAD_DESC'  => 'Peso máximo autorizado (en kg) para este precio.',
    Group::SURCHARGES . '_PICK_AND_PACK_COSTS_HEAD_TITLE'  => 'Costes',
    Group::SURCHARGES . '_PICK_AND_PACK_COSTS_HEAD_DESC'   => 'Gastos de envío por peso en EUR.',

    Group::SURCHARGES . '_ROUND_UP_TITLE'                  => '¿Redondear los gastos de envío?',
    Group::SURCHARGES . '_ROUND_UP_DESC'                   => 'Permite que los gastos de envío se muestren de forma más uniforme redondeando siempre los importes al alza (hasta, por ejemplo, XX,90 €).',
    Group::SURCHARGES . '_ROUND_UP_TO_TITLE'               => 'Redondee hasta',
    Group::SURCHARGES . '_ROUND_UP_TO_DESC'                => '¿A qué decimal debe redondearse siempre?',

    Group::SURCHARGES . '_END_TITLE'                       => '',
    Group::SURCHARGES . '_END_DESC'                        => '',
];

/**
 * Bulk Price Change Preview
 */
$translations_bulk_price = [
    Group::BULK_PRICE . '_START_TITLE'          => 'Cambio de precio a granel',
    Group::BULK_PRICE . '_START_DESC'           => 'Multiplica todos los precios de envío del módulo por un factor. Los cambios son sólo una vista previa. Los valores no son definitivos hasta que se guardan. Antes, el factor puede modificarse tantas veces como sea necesario sin que cambien realmente los precios.',

    Group::BULK_PRICE . '_FACTOR_TITLE'         => 'Factor',
    Group::BULK_PRICE . '_FACTOR_DESC'          => '¿Por qué factor deben ajustarse los precios de envío?',
    Group::BULK_PRICE . '_FACTOR_PREVIEW_TITLE' => 'Vista previa',
    Group::BULK_PRICE . '_FACTOR_PREVIEW_DESC'  => 'La vista previa de los factores está activa. Compruebe todos los precios y haga clic en "Actualizar" para aplicar los ajustes de forma permanente. De lo contrario, haga clic en "Cancelar".',
    Group::BULK_PRICE . '_FACTOR_RESET_TITLE'   => 'Restablecer',

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
