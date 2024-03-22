<?php

namespace Grandeljay\Ups;

use Grandeljay\Ups\Configuration\Group;
use RobinTheHood\ModifiedStdModule\Classes\{Configuration, CaseConverter};

class Quote
{
    private Configuration $config;
    private Country $country;
    private float $total_weight = 0;
    private array $boxes        = [];
    private array $methods      = [];

    public function __construct(string $module)
    {
        global $order;

        if (!isset($order)) {
            return;
        }

        $this->config  = new Configuration($module);
        $this->country = new Country($order->delivery['country']);
        $this->boxes   = $this->getBoxes();
        $this->methods = $this->getShippingMethods();

        $this->setSurcharges();
    }

    private function getConfig(string $screaming_key): mixed
    {
        $camelKey = CaseConverter::screamingToCamel($screaming_key);
        $value    = isset($this->config->$camelKey) ? $this->config->$camelKey : $camelKey;

        return $value;
    }

    public function exceedsMaximumWeight(): bool
    {
        global $order;

        if (null === $order) {
            return true;
        }

        $shipping_weight_max = $this->getConfig(Group::SHIPPING_WEIGHT . '_MAX');

        foreach ($order->products as $product) {
            if ($product['weight'] >= $shipping_weight_max) {
                return true;
            }
        }

        return false;
    }

    public function getBoxes(): array
    {
        global $order;

        $boxes                 = [];
        $shipping_weight_ideal = $this->getConfig(Group::SHIPPING_WEIGHT . '_IDEAL');

        if (null === $order) {
            return $boxes;
        }

        foreach ($order->products as $product) {
            for ($i = 1; $i <= $product['quantity']; $i++) {
                $product_weight = (float) $product['weight'];

                /** Find a box empty enough to fit product */
                foreach ($boxes as $box) {
                    $box_weight          = $box->getWeight();
                    $box_can_fit_product = $box_weight + $product_weight < $shipping_weight_ideal;

                    if ($box_can_fit_product) {
                        $box->addProduct($product);

                        continue 2;
                    }
                }

                /** Add product to a new box */
                $box = new Parcel();
                $box->addProduct($product);

                /** Add box to list */
                $boxes[] = $box;
            }
        }

        $this->total_weight = 0;

        foreach ($boxes as $box) {
            $this->total_weight += $box->getWeight();
        }

        return $boxes;
    }

    private function getShippingMethods(): array
    {
        $methods = [];

        /** National */
        $shipping_is_national = intval(\STORE_COUNTRY) === $this->country->getCountryID();

        if ($shipping_is_national) {
            $shipping_national_methods = [
                'STANDARD',
                'SAVER',
                '1200',
                'EXPRESS',
                'PLUS',
            ];

            foreach ($shipping_national_methods as $method) {
                $method_is_enabled = 'true' === $this->getConfig(Group::SHIPPING_METHODS . '_' . $method);

                if ($method_is_enabled) {
                    $methods[] = $this->getShippingMethodNational($method);
                }
            }

            return $methods;
        }
        /** */

        /** Groups */
        $shipping_groups_methods = [
            Group::SHIPPING_GROUP_A => [
                'STANDARD',
                'SAVER',
                'EXPRESS',
                'PLUS',
            ],
            Group::SHIPPING_GROUP_B => [
                'STANDARD',
                'SAVER',
                'EXPRESS',
                'PLUS',
            ],
            Group::SHIPPING_GROUP_C => [
                'STANDARD',
                'SAVER',
                'EXPRESS',
                'PLUS',
            ],
            Group::SHIPPING_GROUP_D => [
                'STANDARD',
                'SAVER',
                'EXPRESS',
                'PLUS',
            ],
            Group::SHIPPING_GROUP_E => [
                'EXPEDITED',
                'SAVER',
                'EXPRESS',
                'PLUS',
            ],
            Group::SHIPPING_GROUP_F => [
                'EXPEDITED',
                'SAVER',
                'EXPRESS',
                'PLUS',
            ],
        ];

        foreach ($shipping_groups_methods as $group => $shipping_methods) {
            $countries_field = $this->getConfig($group . '_COUNTRIES');
            $countries       = array_map(
                function ($country) {
                    return trim($country);
                },
                explode(',', $countries_field)
            );

            if (in_array($_SESSION['delivery_zone'], $countries, true)) {
                foreach ($shipping_methods as $method) {
                    $method_is_enabled = 'true' === $this->getConfig(Group::SHIPPING_METHODS . '_' . $method);

                    if ($method_is_enabled) {
                        $methods[] = $this->getShippingMethodGroups($group, $method);
                    }
                }
            }
        }
        /** */

        /** Default */
        if (0 === count($methods)) {
            foreach ($shipping_groups_methods[Group::SHIPPING_GROUP_F] as $method) {
                $method_is_enabled = 'true' === $this->getConfig(Group::SHIPPING_METHODS . '_' . $method);

                if ($method_is_enabled) {
                    $methods[] = $this->getShippingMethodGroups(Group::SHIPPING_GROUP_F, $method);
                }
            }
        }
        /** */

        return $methods;
    }

    private function getShippingMethodNational(string $method): array
    {
        $method_paket_national = [
            'id'           => CaseConverter::screamingToLisp($method),
            'title'        => sprintf(
                'UPS %1$s (%2$s)' . '<!-- BREAK -->' . '<strong>UPS %1$s</strong><br>%3$s',
                $this->getConfig('SHIPPING_METHOD_' . $method),
                $this->getNameBoxWeight(),
                $this->getConfig(Group::SHIPPING_NATIONAL . '_START_TITLE')
            ),
            'cost'         => 0,
            'calculations' => [],
        ];

        $shipping_national_costs = json_decode($this->getConfig(Group::SHIPPING_NATIONAL . '_' . $method . '_COSTS'), true);

        asort($shipping_national_costs);

        $weight_cost_min = $this->getConfig(Group::SHIPPING_NATIONAL . '_' . $method . '_MIN');

        $box_maximum_weight = 0;
        $box_maximum_costs  = 0;

        /** Costs per table */
        foreach ($shipping_national_costs as $shipping_national_cost) {
            $weight_max         = (float) $shipping_national_cost['weight-max'];
            $weight_cost        = max(
                (float) $shipping_national_cost['weight-costs'],
                $weight_cost_min
            );
            $box_maximum_weight = $weight_max;
            $box_maximum_costs  = $weight_cost;

            if ($this->total_weight <= $weight_max) {
                $method_paket_national['cost']          += $weight_cost;
                $method_paket_national['calculations'][] = [
                    'item'  => sprintf(
                        'National shipping (%s) for shipment (%01.2f kg)',
                        $method,
                        $this->total_weight
                    ),
                    'costs' => $weight_cost,
                ];

                break;
            }
        }

        /** Costs per kg */
        if ($this->total_weight > $box_maximum_weight) {
            $box_weight_excess                       = $this->total_weight - $box_maximum_weight;
            $weight_cost_kg                          = $this->getConfig(Group::SHIPPING_NATIONAL . '_' . $method . '_KG');
            $weight_cost                             = max(
                $weight_cost_min,
                $box_maximum_costs + ceil($box_weight_excess) * $weight_cost_kg
            );
            $method_paket_national['cost']          += $weight_cost;
            $method_paket_national['calculations'][] = [
                'item'  => sprintf(
                    'National shipping (%s) for shipment (%01.2f kg)',
                    $method,
                    $this->total_weight
                ),
                'costs' => $weight_cost,
            ];
        }

        return $method_paket_national;
    }

    private function getShippingMethodGroups(string $group, string $method): array
    {
        $method_group = [
            'id'           => CaseConverter::screamingToLisp($method),
            'title'        => sprintf(
                'UPS %1$s (%2$s)' . '<!-- BREAK -->' . '<strong>UPS %1$s</strong><br>%3$s',
                $this->getConfig('SHIPPING_METHOD_' . $method),
                $this->getNameBoxWeight(),
                $this->getConfig($group . '_START_TITLE')
            ),
            'cost'         => 0,
            'calculations' => [],
        ];

        $shipping_costs_group = json_decode($this->getConfig($group . '_' . $method . '_COSTS'), true);

        asort($shipping_costs_group);

        $shipping_costs_min_group = $this->getConfig($group . '_' . $method . '_MIN');

        /** Costs per table */
        foreach ($shipping_costs_group as $shipping_international_cost) {
            $weight_max         = (float) $shipping_international_cost['weight-max'];
            $weight_cost        = max(
                (float) $shipping_international_cost['weight-costs'],
                $shipping_costs_min_group
            );
            $box_maximum_weight = $weight_max;
            $box_maximum_costs  = $weight_cost;

            if ($this->total_weight <= $weight_max) {
                $method_group['cost']          += $weight_cost;
                $method_group['calculations'][] = [
                    'item'  => sprintf(
                        'International shipping (%s) for shipment (%01.2f kg)',
                        $method,
                        $this->total_weight
                    ),
                    'costs' => $weight_cost,
                ];

                break;
            }
        }

        /** Costs per kg */
        if ($this->total_weight > $box_maximum_weight) {
            $box_weight_excess              = $this->total_weight - $box_maximum_weight;
            $weight_cost_kg                 = $this->getConfig(Group::SHIPPING_NATIONAL . '_' . $method . '_KG');
            $weight_cost                    = max(
                $shipping_costs_min_group,
                $box_maximum_costs + ceil($box_weight_excess) * $weight_cost_kg
            );
            $method_group['cost']          += $weight_cost;
            $method_group['calculations'][] = [
                'item'  => sprintf(
                    'International shipping (%s) for shipment (%01.2f kg)',
                    $method,
                    $this->total_weight
                ),
                'costs' => $weight_cost,
            ];
        }

        return $method_group;
    }

    private function setSurcharges(): void
    {
        /**
         * Surcharges
         */
        $surcharges_config = json_decode($this->getConfig('SURCHARGES_SURCHARGES'), true);
        $surcharges        = [];
        $surcharges_update = false;

        foreach ($this->methods as &$method) {
            $cost_before_surcharges = $method['cost'];

            foreach ($surcharges_config as $surcharge_index => $surcharge) {
                /**
                 * For Weight
                 */
                $for_weight      = $surcharge['for-weight'] ?? 0;
                $key_per_package = sprintf('configuration[per-package-%d]', $surcharge_index);

                if ('true' !== $surcharge[$key_per_package]) {
                    if ($this->total_weight < $for_weight) {
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
                            foreach ($this->boxes as $box_index => $box) {
                                $box_weight = $box->getWeight();

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
                                        count($this->boxes),
                                        $box_weight
                                    ),
                                    'costs' => $surcharge_amount,
                                ];
                            }
                        } else {
                            if ($this->total_weight >= $for_weight) {
                                $method_cost              = $method['cost'];
                                $method['cost']          += $surcharge_amount;
                                $method['calculations'][] = [
                                    'item'  => sprintf(
                                        '%s for order (%01.2f kg)',
                                        $surcharge['name'],
                                        $this->total_weight
                                    ),
                                    'costs' => $surcharge_amount,
                                ];
                            }
                        }
                        break;

                    case 'percent':
                        $surcharge_amount = $cost_before_surcharges * ($surcharge['surcharge'] / 100);

                        if ('true' === $surcharge[$key_per_package]) {
                            foreach ($this->boxes as $box_index => $box) {
                                $box_weight = $box->getWeight();

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
                                        count($this->boxes),
                                        $box_weight
                                    ),
                                    'costs' => $surcharge_amount,
                                ];
                            }
                        } else {
                            if ($this->total_weight >= $for_weight) {
                                $method_cost              = $method['cost'];
                                $method['cost']          += $surcharge_amount;
                                $method['calculations'][] = [
                                    'item'  => sprintf(
                                        '%s: %01.2f %% for order (%01.2f kg)',
                                        $surcharge['name'],
                                        $surcharge['surcharge'],
                                        $this->total_weight
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
        foreach ($this->methods as &$method) {
            $pick_and_pack_costs = json_decode($this->getConfig(Group::SURCHARGES . '_PICK_AND_PACK', '[]'), true);

            asort($pick_and_pack_costs);

            foreach ($this->boxes as $box_index => $box) {
                foreach ($pick_and_pack_costs as $pick_and_pack_cost) {
                    $weight_max  = floatval($pick_and_pack_cost['weight-max']);
                    $weight_cost = floatval($pick_and_pack_cost['weight-costs']);
                    $box_weight  = $box->getWeight();

                    if ($box_weight <= $weight_max) {
                        $method['cost']          += $weight_cost;
                        $method['calculations'][] = [
                            'item'  => sprintf(
                                'Pick and Pack for box %d / %d (%01.2f kg)',
                                $box_index + 1,
                                count($this->boxes),
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
        $surcharges_round_up    = $this->getConfig(Group::SURCHARGES . '_ROUND_UP');
        $surcharges_round_up_to = $this->getConfig(Group::SURCHARGES . '_ROUND_UP_TO');

        if ('true' === $surcharges_round_up && is_numeric($surcharges_round_up_to)) {
            $surcharges_round_up_to = (float) $surcharges_round_up_to;

            foreach ($this->methods as &$method) {
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

    private function getNameBoxWeight(): string
    {
        $boxes_weight = [];

        foreach ($this->boxes as $box) {
            $key = $box->getWeight() . ' kg';

            if (isset($boxes_weight[$key])) {
                $boxes_weight[$key]++;
            } else {
                $boxes_weight[$key] = 1;
            }
        }

        $boxes_weight_text = [];

        foreach ($boxes_weight as $weight_text => $quantity) {
            preg_match('/[\d+\.]+/', $weight_text, $weight_matches);

            $weight = round($weight_matches[0], 2) . ' kg';

            $boxes_weight_text[] = sprintf(
                '%dx %s',
                $quantity,
                $weight
            );
        }

        $debug_is_enabled = $this->getConfig('DEBUG_ENABLE');
        $user_is_admin    = isset($_SESSION['customers_status']['customers_status_id']) && 0 === (int) $_SESSION['customers_status']['customers_status_id'];

        if ('true' !== $debug_is_enabled || !$user_is_admin) {
            $boxes_weight_text = [
                sprintf(
                    '%s kg',
                    round($this->total_weight, 2)
                ),
            ];
        }

        return implode(', ', $boxes_weight_text);
    }

    public function getQuote(string $method_id): ?array
    {
        if (empty($this->methods) || $this->exceedsMaximumWeight()) {
            return null;
        }

        $methods = $this->methods;

        if ('' !== $method_id) {
            $methods = [];

            foreach ($this->methods as $method) {
                if ($method_id === $method['id']) {
                    $methods[] = $method;
                }
            }
        }

        /**
         * Output debug
         */
        $debug_is_enabled = $this->getConfig('DEBUG_ENABLE');
        $user_is_admin    = isset($_SESSION['customers_status']['customers_status_id']) && 0 === (int) $_SESSION['customers_status']['customers_status_id'];

        if ('true' === $debug_is_enabled && $user_is_admin) {
            foreach ($methods as &$method) {
                $total = 0;

                ob_start();
                ?>
                <br><br>

                <h3>Debug mode</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Costs</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($method['calculations'] as $calculation) { ?>
                            <?php $total += $calculation['costs']; ?>

                            <tr>
                                <td><?= $calculation['item'] ?></td>
                                <td><?= \sprintf('%01.2f', $calculation['costs']) ?></td>
                                <td><?= \sprintf('%01.2f', $total) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                $method['title'] .= ob_get_clean();
            }
        }

        $quote = [
            'id'      => 'grandeljayups',
            'module'  => sprintf(
                'UPS (%s)',
                $this->getNameBoxWeight()
            ),
            'methods' => $methods,
        ];

        return $quote;
    }
}
