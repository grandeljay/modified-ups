<?php

namespace Grandeljay\Ups;

use Grandeljay\Ups\Configuration\Configuration;
use Grandeljay\Ups\Configuration\Group;

/**
 * A modified-shop shipping method for UPS.
 */
class Method
{
    protected array $boxes             = [];
    protected float $weight            = 0;
    protected string $weight_formatted = '';

    public static function isEnabled(string $method_name): bool
    {
        $method_is_enabled = 'true' === Configuration::get(Group::SHIPPING_METHODS . '_' . $method_name);

        return $method_is_enabled;
    }

    public function __construct()
    {
        $shipping_weight_ideal   = Configuration::get(Group::SHIPPING_WEIGHT . '_IDEAL');
        $shipping_weight_maximum = Configuration::get(Group::SHIPPING_WEIGHT . '_MAX');

        $order_packer = new \Grandeljay\ShippingModuleHelper\OrderPacker();
        $order_packer->setIdealWeight($shipping_weight_ideal);
        $order_packer->setMaximumWeight($shipping_weight_maximum);
        $order_packer->packOrder();

        $this->boxes            = $order_packer->getBoxes();
        $this->weight           = $order_packer->getWeight();
        $this->weight_formatted = $order_packer->getWeightFormatted();
    }

    public function isNational(): bool
    {
        global $order;

        $is_national = \STORE_COUNTRY === $order->delivery['country']['id'];

        return $is_national;
    }

    public function isInternational(): bool
    {
        global $order;

        $is_international = \STORE_COUNTRY !== $order->delivery['country']['id'];

        return $is_international;
    }

    protected function setSurcharges(array &$methods): void
    {
        $boxes        = $this->boxes;
        $total_weight = $this->weight;

        /**
         * Surcharges
         */
        $surcharges_config = json_decode(Configuration::Get('SURCHARGES_SURCHARGES'), true);
        $surcharges        = [];
        $surcharges_update = false;

        foreach ($methods as &$method) {
            $cost_before_surcharges = $method['cost'];

            foreach ($surcharges_config as $surcharge_index => $surcharge) {
                /**
                 * For Weight
                 */
                $for_weight      = $surcharge['for-weight'] ?? 0;
                $key_per_package = sprintf('configuration[per-package-%d]', $surcharge_index);

                if ('true' !== $surcharge[$key_per_package]) {
                    if ($total_weight < $for_weight) {
                        continue;
                    }
                }

                /**
                 * For Method
                 */
                $for_method = $surcharge['for-method'] ?? 'all' ;

                if (
                       $method['id'] !== $for_method
                    && 'all'         !== $for_method
                    && 'all-others'  !== $for_method
                ) {
                    continue;
                }

                /** All others */
                if ('standard' === $method['id'] && 'all-others' === $for_method) {
                    continue;
                }

                /**
                 * Duration
                 */
                if (!empty($surcharge['duration-start']) && !empty($surcharge['duration-end'])) {
                    /** Date now */
                    $date_now = new \DateTime();

                    /** Duration start */
                    $duration_start           = new \DateTime($surcharge['duration-start']);
                    $duration_start_is_active = $date_now >= $duration_start;

                    /** Duration end */
                    $duration_end           = new \DateTime($surcharge['duration-end']);
                    $duration_end_is_active = $date_now <= $duration_end;

                    /** Automatically update duration years */
                    if ($date_now > $duration_start && $date_now > $duration_end) {
                        $new_duration_start = $duration_start->modify('+1 year');
                        $new_duration_end   = $duration_end->modify('+1 year');

                        $surcharge['duration-start'] = $new_duration_start->format('Y-m-d');
                        $surcharge['duration-end']   = $new_duration_end->format('Y-m-d');

                        $surcharges_update = true;
                    }

                    /** Duration now */
                    $duration_is_now = $duration_start_is_active && $duration_end_is_active;

                    if (!$duration_is_now) {
                        continue;
                    }
                }

                $key_per_package = sprintf('configuration[per-package-%d]', $surcharge_index);

                switch ($surcharge['type']) {
                    case 'fixed':
                        $surcharge_amount = (float) $surcharge['surcharge'];

                        if ('true' === $surcharge[$key_per_package]) {
                            foreach ($boxes as $box_index => $box) {
                                $box_weight = $box->getWeightWithAttributes();

                                if ($box_weight < $for_weight) {
                                    continue;
                                }

                                $method_cost              = $method['cost'];
                                $method['cost']          += $surcharge_amount;
                                $method['calculations'][] = [
                                    'item'  => sprintf(
                                        '%s for box %d / %d (%01.2f kg)',
                                        $surcharge['name'],
                                        $box_index + 1,
                                        count($boxes),
                                        $box_weight
                                    ),
                                    'costs' => $surcharge_amount,
                                ];
                            }
                        } else {
                            if ($total_weight >= $for_weight) {
                                $method_cost              = $method['cost'];
                                $method['cost']          += $surcharge_amount;
                                $method['calculations'][] = [
                                    'item'  => sprintf(
                                        '%s for order (%01.2f kg)',
                                        $surcharge['name'],
                                        $total_weight
                                    ),
                                    'costs' => $surcharge_amount,
                                ];
                            }
                        }
                        break;

                    case 'percent':
                        $surcharge_amount = $cost_before_surcharges * ($surcharge['surcharge'] / 100);

                        if ('true' === $surcharge[$key_per_package]) {
                            foreach ($boxes as $box_index => $box) {
                                $box_weight = $box->getWeightWithAttributes();

                                if ($box_weight < $for_weight) {
                                    continue;
                                }

                                $method['cost']          += $surcharge_amount;
                                $method['calculations'][] = [
                                    'item'  => sprintf(
                                        '%s: %01.2f %% for box %d / %d (%01.2f kg)',
                                        $surcharge['name'],
                                        $surcharge['surcharge'],
                                        $box_index + 1,
                                        count($boxes),
                                        $box_weight
                                    ),
                                    'costs' => $surcharge_amount,
                                ];
                            }
                        } else {
                            if ($total_weight >= $for_weight) {
                                $method_cost              = $method['cost'];
                                $method['cost']          += $surcharge_amount;
                                $method['calculations'][] = [
                                    'item'  => sprintf(
                                        '%s: %01.2f %% for order (%01.2f kg)',
                                        $surcharge['name'],
                                        $surcharge['surcharge'],
                                        $total_weight
                                    ),
                                    'costs' => $surcharge_amount,
                                ];
                            }
                        }
                        break;
                }
            }

            $surcharges[] = $surcharge;
        }

        /** Update surcharges option */
        if ($surcharges_update) {
            \xtc_db_query(
                sprintf(
                    'UPDATE `%s`
                        SET `configuration_value` = "%s"
                      WHERE `configuration_key`   = "%s"',
                    \TABLE_CONFIGURATION,
                    \addslashes(json_encode($surcharges)),
                    'MODULE_SHIPPING_GRANDELJAYUPS_SURCHARGES'
                )
            );
        }

        /** Pick and Pack */
        foreach ($methods as &$method) {
            $pick_and_pack_costs = json_decode(Configuration::Get(Group::SURCHARGES . '_PICK_AND_PACK', '[]'), true);

            asort($pick_and_pack_costs);

            foreach ($boxes as $box_index => $box) {
                foreach ($pick_and_pack_costs as $pick_and_pack_cost) {
                    $weight_max  = floatval($pick_and_pack_cost['weight-max']);
                    $weight_cost = floatval($pick_and_pack_cost['weight-costs']);
                    $box_weight  = $box->getWeightWithAttributes();

                    if ($box_weight <= $weight_max) {
                        $method['cost']          += $weight_cost;
                        $method['calculations'][] = [
                            'item'  => sprintf(
                                'Pick and Pack for box %d / %d (%01.2f kg)',
                                $box_index + 1,
                                count($boxes),
                                $box_weight
                            ),
                            'costs' => $weight_cost,
                        ];

                        break;
                    }
                }
            }
        }

        /** Round up */
        $surcharges_round_up    = Configuration::Get(Group::SURCHARGES . '_ROUND_UP');
        $surcharges_round_up_to = Configuration::Get(Group::SURCHARGES . '_ROUND_UP_TO');

        if ('true' === $surcharges_round_up && is_numeric($surcharges_round_up_to)) {
            $surcharges_round_up_to = (float) $surcharges_round_up_to;

            foreach ($methods as &$method) {
                $method_cost     = $method['cost'];
                $number_whole    = floor($method_cost);
                $number_decimals = round($method_cost - $number_whole, 2);

                $round_up_to = $method_cost;

                if ($number_decimals > $surcharges_round_up_to) {
                    $round_up_to = ceil($method_cost) + $surcharges_round_up_to;
                }

                if ($number_decimals < $surcharges_round_up_to) {
                    $round_up_to = $number_whole + $surcharges_round_up_to;
                }

                $method['calculations'][] = [
                    'item'  => sprintf(
                        'Round up to %01.2f',
                        $surcharges_round_up_to
                    ),
                    'costs' => $round_up_to - $method_cost,
                ];

                $method['cost'] = $round_up_to;
            }
        }
        /** */
    }
}
