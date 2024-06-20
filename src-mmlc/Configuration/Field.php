<?php

namespace Grandeljay\Ups\Configuration;

use Grandeljay\Ups\Constants;

class Field
{
    public static function applyBulkPriceChangeKgMin(string $value, string $option): float
    {
        $value = (float) $value;

        if (!isset($_GET['factor']) || !\is_numeric($_GET['factor'])) {
            return $value;
        }

        if ('_KG' !== \substr($option, -3) && '_MIN' !== \substr($option, -4)) {
            return $value;
        }

        $factor = (float) $_GET['factor'];

        $value = $value * $factor;

        return $value;
    }

    public static function applyBulkPriceChangeShipping(array $shipping_costs): array
    {
        if (!isset($_GET['factor']) || !\is_numeric($_GET['factor'])) {
            return $shipping_costs;
        }

        $factor = (float) $_GET['factor'];

        $shipping_costs = \array_map(
            function (array $entry) use ($factor) {
                $entry['weight-costs'] = $entry['weight-costs'] * $factor;

                return $entry;
            },
            $shipping_costs
        );

        return $shipping_costs;
    }

    /**
     * Number type for option inputs
     */
    public static function inputNumber(string $value, string $option): string
    {
        $value = self::applyBulkPriceChangeKgMin($value, $option);

        $html  = '';
        $html .= xtc_draw_input_field(
            'configuration[' . $option . ']',
            $value,
            sprintf('step="any" min="0.00"'),
            false,
            'number'
        );

        return $html;
    }

    public static function inputNumberRoundUp(string $value, string $option): string
    {
        $html  = '';
        $html .= xtc_draw_input_field(
            'configuration[' . $option . ']',
            $value,
            'step="0.01" min="0.00" max="0.99"',
            false,
            'number'
        );

        return $html;
    }
    /** */

    /**
     * National
     */
    public static function shippingNationalCountry(string $countryID, string $option): string
    {
        $html  = '';
        $html .= xtc_draw_input_field(
            'configuration[' . $option . ']',
            $countryID,
            'readonly="readonly"
             style="opacity: 0.4;"'
        );

        return $html;
    }

    public static function getShippingNationalMethodCosts(): array
    {
        /** Standard */
        $national_costs_standard = [
            [
                'weight-max'   => '2',
                'weight-costs' => '3.40',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '3.75',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '4.25',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '5.10',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '6.45',
            ],
        ];

        /** Saver */
        $national_costs_saver = [
            [
                'weight-max'   => '2',
                'weight-costs' => '4.50',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '4.95',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '5.40',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '6.30',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '8.10',
            ],
        ];

        /** 12:00 */
        $national_costs_1200 = [
            [
                'weight-max'   => '2',
                'weight-costs' => '5.45',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '6.00',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '6.55',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '7.65',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '9.80',
            ],
        ];

        /** Express */
        $national_costs_express = [
            [
                'weight-max'   => '2',
                'weight-costs' => '6.60',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '7.25',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '7.90',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '9.25',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '11.90',
            ],
        ];

        /** Plus */
        $national_costs_plus = [
            [
                'weight-max'   => '2',
                'weight-costs' => '31.50',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '34.65',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '37.80',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '44.10',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '56.70',
            ],
        ];

        $national_costs = [
            Group::SHIPPING_NATIONAL . '_STANDARD' => [
                'costs' => json_encode($national_costs_standard),
                'kg'    => '0.33',
                'min'   => '3.40',
            ],
            Group::SHIPPING_NATIONAL . '_SAVER'    => [
                'costs' => json_encode($national_costs_saver),
                'kg'    => '0.42',
                'min'   => '4.50',
            ],
            Group::SHIPPING_NATIONAL . '_1200'     => [
                'costs' => json_encode($national_costs_1200),
                'kg'    => '0.50',
                'min'   => '5.45',
            ],
            Group::SHIPPING_NATIONAL . '_EXPRESS'  => [
                'costs' => json_encode($national_costs_express),
                'kg'    => '0.61',
                'min'   => '6.60',
            ],
            Group::SHIPPING_NATIONAL . '_PLUS'     => [
                'costs' => json_encode($national_costs_plus),
                'kg'    => '2.85',
                'min'   => '31.50',
            ],
        ];

        return $national_costs;
    }

    public static function shippingMethodCosts(string $value, string $option): string
    {
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5);
        $group = substr($option, 0, -6);

        $html  = '';
        $html .= xtc_draw_input_field(
            'configuration[' . $option . ']',
            $value
        );

        ob_start();
        ?>
        <dialog id="<?= $option ?>">
            <div class="modulbox">
                <table class="contentTable">
                    <tbody>
                        <tr class="infoBoxHeading">
                            <td class="infoBoxHeading">
                                <div class="infoBoxHeadingTitle"><b><?= constant($group . '_COSTS_TITLE') ?></b></div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="contentTable">
                    <tbody>
                        <tr class="infoBoxContent">
                            <td class="infoBoxContent">
                                <div class="container">
                                    <template id="grandeljayups_row">
                                        <div class="row">
                                            <div class="column">
                                                <input type="number" step="any" min="0.00" name="weight-max" /> Kg
                                            </div>

                                            <div class="column">
                                                <input type="number" step="any" min="0.00" name="weight-costs" /> EUR
                                            </div>
                                        </div>
                                    </template>

                                    <div class="row">
                                        <div class="column">
                                            <div>
                                                <b><?= constant($group . '_WEIGHT_HEAD_TITLE') ?></b><br>
                                                <?= constant($group . '_WEIGHT_HEAD_DESC') ?><br>
                                            </div>
                                        </div>

                                        <div class="column">
                                            <div>
                                                <b><?= constant($group . '_COSTS_HEAD_TITLE') ?></b><br>
                                                <?= constant($group . '_COSTS_HEAD_DESC') ?><br>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    $shipping_costs = json_decode($value, true);
                                    $shipping_costs = self::applyBulkPriceChangeShipping($shipping_costs);

                                    asort($shipping_costs);

                                    foreach ($shipping_costs as $shipping_cost) {
                                        ?>
                                        <div class="row">
                                            <div class="column">
                                                <input type="number" step="any" min="0.00" value="<?= $shipping_cost['weight-max'] ?>" name="weight-max" /> Kg
                                            </div>

                                            <div class="column">
                                                <input type="number" step="any" min="0.00" value="<?= $shipping_cost['weight-costs'] ?>" name="weight-costs" /> EUR
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <div class="row">
                                        <button name="grandeljayups_add" type="button"><?= constant(Constants::MODULE_SHIPPING_NAME . '_BUTTON_ADD') ?></button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="buttons">
                <button name="grandeljayups_apply" value="default" type="button"><?= constant(Constants::MODULE_SHIPPING_NAME . '_BUTTON_APPLY') ?></button>
                <button name="grandeljayups_cancel" value="cancel" type="button"><?= constant(Constants::MODULE_SHIPPING_NAME . '_BUTTON_CANCEL') ?></button>
            </div>
        </dialog>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    /**
     * Group A
     */
    public static function getShippingCountryGroupACosts(): array
    {
        /** Standard */
        $group_a_costs_standard = [
            [
                'weight-max'   => '2',
                'weight-costs' => '5.65',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '6.20',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '6.80',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '7.90',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '9.90',
            ],
        ];

        /** Saver */
        $group_a_costs_saver = [
            [
                'weight-max'   => '2',
                'weight-costs' => '11.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '14.30',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '17.85',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '23.80',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '29.75',
            ],
        ];

        /** Express */
        $group_a_costs_express = [
            [
                'weight-max'   => '2',
                'weight-costs' => '13.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '16.70',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '20.85',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '27.80',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '34.75',
            ],
        ];

        /** Plus */
        $group_a_costs_plus = [
            [
                'weight-max'   => '2',
                'weight-costs' => '39.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '45.90',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '53.85',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '67.85',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '79.80',
            ],
        ];

        $group_a_costs = [
            Group::SHIPPING_GROUP_A . '_STANDARD' => [
                'costs' => json_encode($group_a_costs_standard),
                'kg'    => '0.33',
                'min'   => '3.40',
            ],
            Group::SHIPPING_GROUP_A . '_SAVER'    => [
                'costs' => json_encode($group_a_costs_saver),
                'kg'    => '0.42',
                'min'   => '4.50',
            ],
            Group::SHIPPING_GROUP_A . '_EXPRESS'  => [
                'costs' => json_encode($group_a_costs_express),
                'kg'    => '0.61',
                'min'   => '6.60',
            ],
            Group::SHIPPING_GROUP_A . '_PLUS'     => [
                'costs' => json_encode($group_a_costs_plus),
                'kg'    => '2.85',
                'min'   => '31.50',
            ],
        ];

        return $group_a_costs;
    }

    /**
     * Group B
     */
    public static function getShippingCountryGroupBCosts(): array
    {
        /** Standard */
        $group_b_costs_standard = [
            [
                'weight-max'   => '2',
                'weight-costs' => '6.70',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '7.35',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '8.05',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '9.40',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '11.75',
            ],
        ];

        /** Saver */
        $group_b_costs_saver = [
            [
                'weight-max'   => '2',
                'weight-costs' => '13.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '16.70',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '20.85',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '29.20',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '38.25',
            ],
        ];

        /** Express */
        $group_b_costs_express = [
            [
                'weight-max'   => '2',
                'weight-costs' => '15.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '19.10',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '23.85',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '33.40',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '43.75',
            ],
        ];

        /** Plus */
        $group_b_costs_plus = [
            [
                'weight-max'   => '2',
                'weight-costs' => '41.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '48.20',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '56.55',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '71.25',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '83.80',
            ],
        ];

        $group_b_costs = [
            Group::SHIPPING_GROUP_B . '_STANDARD' => [
                'costs' => json_encode($group_b_costs_standard),
                'kg'    => '0.60',
                'min'   => '5.65',
            ],
            Group::SHIPPING_GROUP_B . '_SAVER'    => [
                'costs' => json_encode($group_b_costs_saver),
                'kg'    => '1.92',
                'min'   => '11.90',
            ],
            Group::SHIPPING_GROUP_B . '_EXPRESS'  => [
                'costs' => json_encode($group_b_costs_express),
                'kg'    => '2.20',
                'min'   => '13.90',
            ],
            Group::SHIPPING_GROUP_B . '_PLUS'     => [
                'costs' => json_encode($group_b_costs_plus),
                'kg'    => '4.20',
                'min'   => '39.90',
            ],
        ];

        return $group_b_costs;
    }

    /**
     * Group C
     */
    public static function getShippingCountryGroupCCosts(): array
    {
        /** Standard */
        $group_c_costs_standard = [
            [
                'weight-max'   => '2',
                'weight-costs' => '7.70',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '8.45',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '9.25',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '10.80',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '13.50',
            ],
        ];

        /** Saver */
        $group_c_costs_saver = [
            [
                'weight-max'   => '2',
                'weight-costs' => '15.50',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '20.60',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '26.35',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '37.20',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '48.05',
            ],
        ];

        /** Express */
        $group_c_costs_express = [
            [
                'weight-max'   => '2',
                'weight-costs' => '17.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '23.80',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '30.45',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '42.95',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '55.50',
            ],
        ];

        /** Plus */
        $group_c_costs_plus = [
            [
                'weight-max'   => '2',
                'weight-costs' => '43.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '50.50',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '59.25',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '74.65',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '87.80',
            ],
        ];

        $group_c_costs = [
            Group::SHIPPING_GROUP_C . '_STANDARD' => [
                'costs' => json_encode($group_c_costs_standard),
                'kg'    => '0.69',
                'min'   => '5.65',
            ],
            Group::SHIPPING_GROUP_C . '_SAVER'    => [
                'costs' => json_encode($group_c_costs_saver),
                'kg'    => '2.41',
                'min'   => '11.90',
            ],
            Group::SHIPPING_GROUP_C . '_EXPRESS'  => [
                'costs' => json_encode($group_c_costs_express),
                'kg'    => '2.79',
                'min'   => '13.90',
            ],
            Group::SHIPPING_GROUP_C . '_PLUS'     => [
                'costs' => json_encode($group_c_costs_plus),
                'kg'    => '4.40',
                'min'   => '39.90',
            ],
        ];

        return $group_c_costs;
    }

    /**
     * Group D
     */
    public static function getShippingCountryGroupDCosts(): array
    {
        /** Standard */
        $group_d_costs_standard = [
            [
                'weight-max'   => '2',
                'weight-costs' => '9.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '10.90',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '11.90',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '13.85',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '17.35',
            ],
        ];

        /** Saver */
        $group_d_costs_saver = [
            [
                'weight-max'   => '2',
                'weight-costs' => '15.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '21.15',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '27.05',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '38.15',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '49.30',
            ],
        ];

        /** Express */
        $group_d_costs_express = [
            [
                'weight-max'   => '2',
                'weight-costs' => '18.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '25.15',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '32.15',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '45.35',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '58.60',
            ],
        ];

        /** Plus */
        $group_d_costs_plus = [
            [
                'weight-max'   => '2',
                'weight-costs' => '44.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '51.65',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '60.60',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '76.35',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '89.80',
            ],
        ];

        $group_d_costs = [
            Group::SHIPPING_GROUP_D . '_STANDARD' => [
                'costs' => json_encode($group_d_costs_standard),
                'kg'    => '0.88',
                'min'   => '9.90',
            ],
            Group::SHIPPING_GROUP_D . '_SAVER'    => [
                'costs' => json_encode($group_d_costs_saver),
                'kg'    => '2.48',
                'min'   => '15.90',
            ],
            Group::SHIPPING_GROUP_D . '_EXPRESS'  => [
                'costs' => json_encode($group_d_costs_express),
                'kg'    => '2.94',
                'min'   => '18.90',
            ],
            Group::SHIPPING_GROUP_D . '_PLUS'     => [
                'costs' => json_encode($group_d_costs_plus),
                'kg'    => '4.50',
                'min'   => '44.90',
            ],
        ];

        return $group_d_costs;
    }

    /**
     * Group E
     */
    public static function getShippingCountryGroupECosts(): array
    {
        /** Expedited */
        $group_e_costs_expedited = [
            [
                'weight-max'   => '2',
                'weight-costs' => '17.40',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '28.70',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '49.60',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '73.95',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '98.30',
            ],
        ];

        /** Saver */
        $group_e_costs_saver = [
            [
                'weight-max'   => '2',
                'weight-costs' => '18.40',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '30.35',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '52.45',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '78.20',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '104.00',
            ],
        ];

        /** Express */
        $group_e_costs_express = [
            [
                'weight-max'   => '2',
                'weight-costs' => '21.95',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '36.20',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '62.55',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '93.30',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '124.00',
            ],
        ];

        /** Plus */
        $group_e_costs_plus = [
            [
                'weight-max'   => '2',
                'weight-costs' => '52.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '63.50',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '79.35',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '119.05',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '158.70',
            ],
        ];

        $group_e_costs = [
            Group::SHIPPING_GROUP_E . '_EXPEDITED' => [
                'costs' => json_encode($group_e_costs_expedited),
                'kg'    => '4.92',
                'min'   => '15.90',
            ],
            Group::SHIPPING_GROUP_E . '_SAVER'     => [
                'costs' => json_encode($group_e_costs_saver),
                'kg'    => '5.20',
                'min'   => '15.90',
            ],
            Group::SHIPPING_GROUP_E . '_EXPRESS'   => [
                'costs' => json_encode($group_e_costs_express),
                'kg'    => '6.20',
                'min'   => '18.90',
            ],
            Group::SHIPPING_GROUP_E . '_PLUS'      => [
                'costs' => json_encode($group_e_costs_plus),
                'kg'    => '7.94',
                'min'   => '44.90',
            ],
        ];

        return $group_e_costs;
    }

    /**
     * Group F
     */
    public static function getShippingCountryGroupFCosts(): array
    {
        /** Expedited */
        $group_f_costs_expedited = [
            [
                'weight-max'   => '2',
                'weight-costs' => '24.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '36.10',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '70.95',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '105.85',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '140.70',
            ],
        ];

        /** Saver */
        $group_f_costs_saver = [
            [
                'weight-max'   => '2',
                'weight-costs' => '25.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '37.55',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '73.80',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '110.10',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '146.35',
            ],
        ];

        /** Express */
        $group_f_costs_express = [
            [
                'weight-max'   => '2',
                'weight-costs' => '28.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '41.90',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '82.35',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '122.85',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '163.30',
            ],
        ];

        /** Plus */
        $group_f_costs_plus = [
            [
                'weight-max'   => '2',
                'weight-costs' => '57.90',
            ],
            [
                'weight-max'   => '5',
                'weight-costs' => '69.50',
            ],
            [
                'weight-max'   => '10',
                'weight-costs' => '86.85',
            ],
            [
                'weight-max'   => '15',
                'weight-costs' => '130.30',
            ],
            [
                'weight-max'   => '20',
                'weight-costs' => '173.70',
            ],
        ];

        $group_f_costs = [
            Group::SHIPPING_GROUP_F . '_EXPEDITED' => [
                'costs' => json_encode($group_f_costs_expedited),
                'kg'    => '7.04',
                'min'   => '15.90',
            ],
            Group::SHIPPING_GROUP_F . '_SAVER'     => [
                'costs' => json_encode($group_f_costs_saver),
                'kg'    => '7.32',
                'min'   => '15.90',
            ],
            Group::SHIPPING_GROUP_F . '_EXPRESS'   => [
                'costs' => json_encode($group_f_costs_express),
                'kg'    => '8.17',
                'min'   => '18.90',
            ],
            Group::SHIPPING_GROUP_F . '_PLUS'      => [
                'costs' => json_encode($group_f_costs_plus),
                'kg'    => '8.69',
                'min'   => '44.90',
            ],
        ];

        return $group_f_costs;
    }

    /**
     * Surcharges
     */
    public static function surchargesSurcharges(string $value, string $option): string
    {
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5);

        $html  = '';
        $html .= xtc_draw_input_field(
            'configuration[' . $option . ']',
            $value
        );

        $for_methods = [
            'all'        => Constants::MODULE_SHIPPING_NAME . '_' . Group::SURCHARGES . '_FOR_METHOD_ALL',
            'all-others' => Constants::MODULE_SHIPPING_NAME . '_' . Group::SURCHARGES . '_FOR_METHOD_ALL_OTHERS',
            'standard'   => Constants::MODULE_SHIPPING_NAME . '_SHIPPING_METHOD_STANDARD',
            'expedited'  => Constants::MODULE_SHIPPING_NAME . '_SHIPPING_METHOD_EXPEDITED',
            'saver'      => Constants::MODULE_SHIPPING_NAME . '_SHIPPING_METHOD_SAVER',
            '1200'       => Constants::MODULE_SHIPPING_NAME . '_SHIPPING_METHOD_1200',
            'express'    => Constants::MODULE_SHIPPING_NAME . '_SHIPPING_METHOD_EXPRESS',
            'plus'       => Constants::MODULE_SHIPPING_NAME . '_SHIPPING_METHOD_PLUS',
        ];

        ob_start();
        ?>
        <dialog id="<?= $option ?>">
            <div class="modulbox">
                <table class="contentTable">
                    <tbody>
                        <tr class="infoBoxHeading">
                            <td class="infoBoxHeading">
                                <div class="infoBoxHeadingTitle"><b><?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_SURCHARGES_TITLE') ?></b></div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="contentTable">
                    <tbody>
                        <tr class="infoBoxContent">
                            <td class="infoBoxContent">
                                <div class="container">
                                    <template id="grandeljayups_row">
                                        <div class="row">
                                            <div class="column name">
                                                <input type="text" name="name" />
                                            </div>

                                            <div class="column amount">
                                                <input type="number" step="any" name="surcharge" />
                                            </div>

                                            <div class="column type">
                                                <select name="type">
                                                    <option value="fixed"><?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_TYPE_FIXED') ?></option>
                                                    <option value="percent"><?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_TYPE_PERCENT') ?></option>
                                                </select>
                                            </div>

                                            <div class="column per-package">
                                                <label>
                                                    <?= xtc_cfg_select_option(['true', 'false'], 'false') ?>
                                                </label>
                                            </div>

                                            <div class="column for-weight">
                                                <input type="number" step="any" name="for-weight" value="0" />
                                            </div>

                                            <div class="column for-method">
                                                <select name="for-method">
                                                    <?php foreach ($for_methods as $m_value => $method) { ?>
                                                        <option value="<?= $m_value ?>"><?= constant($method) ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="column duration-start">
                                                <input type="date" name="duration-start" />
                                            </div>

                                            <div class="column duration-end">
                                                <input type="date" name="duration-end" />
                                            </div>
                                        </div>
                                    </template>

                                    <div class="row">
                                        <div class="column name">
                                            <div>
                                                <b><?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_NAME_TITLE') ?></b><br>
                                                <?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_NAME_DESC') ?><br>
                                            </div>
                                        </div>

                                        <div class="column amount">
                                            <div>
                                                <b><?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_SURCHARGE_TITLE') ?></b><br>
                                                <?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_SURCHARGE_DESC') ?><br>
                                            </div>
                                        </div>

                                        <div class="column type">
                                            <div>
                                                <b><?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_TYPE_TITLE') ?></b><br>
                                                <?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_TYPE_DESC') ?><br>
                                            </div>
                                        </div>

                                        <div class="column per-package select-option">
                                            <div>
                                                <b><?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_PER_PACKAGE_TITLE') ?></b><br>
                                                <?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_PER_PACKAGE_DESC') ?><br>
                                            </div>
                                        </div>

                                        <div class="column for-weight">
                                            <div>
                                                <b><?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_FOR_WEIGHT_TITLE') ?></b><br>
                                                <?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_FOR_WEIGHT_DESC') ?><br>
                                            </div>
                                        </div>

                                        <div class="column for-method">
                                            <div>
                                                <b><?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_FOR_METHOD_TITLE') ?></b><br>
                                                <?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_FOR_METHOD_DESC') ?><br>
                                            </div>
                                        </div>

                                        <div class="column duration-start">
                                            <div>
                                                <b><?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_DURATION_START_TITLE') ?></b><br>
                                                <?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_DURATION_START_DESC') ?><br>
                                            </div>
                                        </div>

                                        <div class="column duration-end">
                                            <div>
                                                <b><?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_DURATION_END_TITLE') ?></b><br>
                                                <?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_DURATION_END_DESC') ?><br>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    $surcharges = json_decode($value, true);
                                    ?>
                                    <?php foreach ($surcharges as $surcharge_index => $surcharge) { ?>
                                        <div class="row">
                                            <div class="column name">
                                                <input type="text" name="name" value="<?= $surcharge['name'] ?>" />
                                            </div>

                                            <div class="column amount">
                                                <input type="number" step="any" name="surcharge" value="<?= $surcharge['surcharge'] ?>" />
                                            </div>

                                            <div class="column type">
                                                <select name="type">
                                                    <?php
                                                    $fixedText   = constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_TYPE_FIXED');
                                                    $percentText = constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_TYPE_PERCENT');
                                                    $types       = [
                                                        'fixed'   => $fixedText,
                                                        'percent' => $percentText,
                                                    ];

                                                    foreach ($types as $type => $text) {
                                                        $selected = $type === $surcharge['type'] ? ' selected' : '';

                                                        echo '<option value="' . $type . '"' . $selected . '>' . $text . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="column per-package select-option">
                                                <?php
                                                $key_write = sprintf('per-package-%d', $surcharge_index);
                                                $key_read  = sprintf('configuration[%s]', $key_write);
                                                ?>
                                                <label>
                                                    <?= xtc_cfg_select_option(['true', 'false'], $surcharge[$key_read], $key_write) ?>
                                                </label>
                                            </div>

                                            <div class="column for-weight">
                                                <input type="number" step="any" name="for-weight" value="<?= $surcharge['for-weight'] ?? 0 ?>" />
                                            </div>

                                            <div class="column for-method">
                                                <?php
                                                $method_selection = $surcharge['for-method'] ?? 'all';
                                                ?>
                                                <select name="for-method">
                                                    <?php foreach ($for_methods as $m_value => $method) { ?>
                                                        <?php if ($method_selection === $m_value) { ?>
                                                            <option value="<?= $m_value ?>" selected><?= constant($method) ?></option>
                                                        <?php } else { ?>
                                                            <option value="<?= $m_value ?>"><?= constant($method) ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="column duration-start">
                                                <input type="date" name="duration-start" value="<?= $surcharge['duration-start'] ?? '' ?>" />
                                            </div>

                                            <div class="column duration-end">
                                                <input type="date" name="duration-end" value="<?= $surcharge['duration-end'] ?? '' ?>" />
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <div class="row">
                                        <button name="grandeljayups_add" type="button"><?= constant(Constants::MODULE_SHIPPING_NAME . '_BUTTON_ADD') ?></button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="buttons">
                <button name="grandeljayups_apply" value="default" type="button"><?= constant(Constants::MODULE_SHIPPING_NAME . '_BUTTON_APPLY') ?></button>
                <button name="grandeljayups_cancel" value="cancel" type="button"><?= constant(Constants::MODULE_SHIPPING_NAME . '_BUTTON_CANCEL') ?></button>
            </div>
        </dialog>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    public static function surchargesPickAndPack(string $value, string $option): string
    {
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5);

        $html  = '';
        $html .= xtc_draw_input_field(
            'configuration[' . $option . ']',
            $value
        );

        ob_start();
        ?>
        <dialog id="<?= $option ?>">
            <div class="modulbox">
                <table class="contentTable">
                    <tbody>
                        <tr class="infoBoxHeading">
                            <td class="infoBoxHeading">
                                <div class="infoBoxHeadingTitle"><b><?= constant(Constants::MODULE_SHIPPING_NAME . '_SURCHARGES_PICK_AND_PACK_TITLE') ?></b></div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="contentTable">
                    <tbody>
                        <tr class="infoBoxContent">
                            <td class="infoBoxContent">
                                <div class="container">
                                    <template id="grandeljayups_row">
                                        <div class="row">
                                            <div class="column">
                                                <input type="number" step="any" min="0.00" name="weight-max" /> Kg
                                            </div>

                                            <div class="column">
                                                <input type="number" step="any" min="0.00" name="weight-costs" /> EUR
                                            </div>
                                        </div>
                                    </template>

                                    <div class="row">
                                        <div class="column">
                                            <div>
                                                <b><?= constant(Constants::MODULE_SHIPPING_NAME . '_' . Group::SURCHARGES . '_PICK_AND_PACK_WEIGHT_HEAD_TITLE') ?></b><br>
                                                <?= constant(Constants::MODULE_SHIPPING_NAME . '_' . Group::SURCHARGES . '_PICK_AND_PACK_WEIGHT_HEAD_DESC') ?>
                                            </div>
                                        </div>

                                        <div class="column">
                                            <div>
                                                <b><?= constant(Constants::MODULE_SHIPPING_NAME . '_' . Group::SURCHARGES . '_PICK_AND_PACK_COSTS_HEAD_TITLE') ?></b><br>
                                                <?= constant(Constants::MODULE_SHIPPING_NAME . '_' . Group::SURCHARGES . '_PICK_AND_PACK_COSTS_HEAD_DESC') ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    $pick_and_pack_costs = json_decode($value, true);

                                    asort($pick_and_pack_costs);

                                    foreach ($pick_and_pack_costs as $pick_and_pack_cost) {
                                        ?>
                                        <div class="row">
                                            <div class="column">
                                                <input type="number" step="any" min="0.00" value="<?= $pick_and_pack_cost['weight-max'] ?>" name="weight-max" /> Kg
                                            </div>

                                            <div class="column">
                                                <input type="number" step="any" min="0.00" value="<?= $pick_and_pack_cost['weight-costs'] ?>" name="weight-costs" /> EUR
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <div class="row">
                                        <button name="grandeljayups_add" type="button"><?= constant(Constants::MODULE_SHIPPING_NAME . '_BUTTON_ADD') ?></button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="buttons">
                <button name="grandeljayups_apply" value="default" type="button"><?= constant(Constants::MODULE_SHIPPING_NAME . '_BUTTON_APPLY') ?></button>
                <button name="grandeljayups_cancel" value="cancel" type="button"><?= constant(Constants::MODULE_SHIPPING_NAME . '_BUTTON_CANCEL') ?></button>
            </div>
        </dialog>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    public static function bulkPriceFactor(string $value, string $option): string
    {
        $page               = \xtc_href_link_admin(\DIR_ADMIN . \FILENAME_MODULES);
        $factor             = $_GET['factor'] ?? 1;
        $reset_parameters   = [
            'set'    => $_GET['set'],
            'module' => $_GET['module'],
            'action' => $_GET['action'],
        ];
        $reset_href         = $page . '?' . \http_build_query($reset_parameters);
        $preview_parameters = \http_build_query(
            \array_merge(
                $reset_parameters,
                ['factor' => $factor]
            )
        );
        $preview_href       = $page . '?' . $preview_parameters;

        $text_preview_title = \constant(Constants::MODULE_SHIPPING_NAME . '_' . Group::BULK_PRICE . '_FACTOR_PREVIEW_TITLE');
        $text_preview_desc  = \constant(Constants::MODULE_SHIPPING_NAME . '_' . Group::BULK_PRICE . '_FACTOR_PREVIEW_DESC');
        $text_reset_title   = \constant(Constants::MODULE_SHIPPING_NAME . '_' . Group::BULK_PRICE . '_FACTOR_RESET_TITLE');

        \ob_start();
        ?>
        <input type="number" name="factor" value="<?= $factor ?>" step="any">

        <a class="button" href="<?= $preview_href ?>" id="factor-preview"><?= $text_preview_title ?></a>
        <a class="button" href="<?= $reset_href ?>"><?= $text_reset_title ?></a>

        <?php if (isset($_GET['factor'])) { ?>
            <p><?= $text_preview_desc ?></p>
        <?php } ?>

        <?php
        $html = \ob_get_clean();

        return $html;
    }
}
