<?php

namespace Grandeljay\Ups;

use Grandeljay\Ups\Configuration\Group;
use RobinTheHood\ModifiedStdModule\Classes\{Configuration, CaseConverter};

class Quote
{
    private Configuration $config;
    private Country $country;
    private float $total_weight = 0;
    private array $boxes        = array();
    private array $methods      = array();

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
            return false;
        }

        $shipping_weight_max = $this->getConfig(Group::SHIPPING_WEIGHT . '_MAX');

        foreach ($order->products as $product) {
            if ($product['weight'] >= $shipping_weight_max) {
                return true;
            }
        }

        return false;
    }

    public function getEmpty(): array
    {
        $emptyQuote = array(
            'module'  => \grandeljayups::class,
            'methods' => array(),
        );

        return $emptyQuote;
    }


    public function getBoxes(): array
    {
        global $order;

        $boxes                 = array();
        $shipping_weight_ideal = $this->getConfig(Group::SHIPPING_WEIGHT . '_IDEAL');

        if (null === $order) {
            return $boxes;
        }

        foreach ($order->products as $product) {
            for ($i = 1; $i <= $product['quantity']; $i++) {
                $product_weight      = (float) $product['weight'];
                $this->total_weight += $product_weight;

                /** Find a box empty enough to fit product */
                foreach ($boxes as &$box) {
                    $box_weight          = $box->getWeight();
                    $box_can_fit_product = $box_weight + $product_weight < $shipping_weight_ideal;

                    if ($box_can_fit_product) {
                        $box->addProduct($product);

                        continue 2;
                    }
                }

                /** Break the reference binding so $box can be assigned a new value */
                unset($box);

                /** Add product to a new box */
                $box = new Parcel();
                $box->addProduct($product);

                /** Add box to list */
                $boxes[] = $box;
            }
        }

        return $boxes;
    }

    private function getShippingMethods(): array
    {
        $methods = array();

        /** National */
        $shipping_is_national = intval(\STORE_COUNTRY) === $this->country->getCountryID();

        if ($shipping_is_national) {
            $shipping_national_methods = array(
                'STANDARD',
                'SAVER',
                '1200',
                'EXPRESS',
                'PLUS',
            );

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
        $shipping_groups_methods = array(
            Group::SHIPPING_GROUP_A => array(
                'STANDARD',
                'SAVER',
                'EXPRESS',
                'PLUS',
            ),
            Group::SHIPPING_GROUP_B => array(
                'STANDARD',
                'SAVER',
                'EXPRESS',
                'PLUS',
            ),
            Group::SHIPPING_GROUP_C => array(
                'STANDARD',
                'SAVER',
                'EXPRESS',
                'PLUS',
            ),
            Group::SHIPPING_GROUP_D => array(
                'STANDARD',
                'SAVER',
                'EXPRESS',
                'PLUS',
            ),
            Group::SHIPPING_GROUP_E => array(
                'EXPEDITED',
                'SAVER',
                'EXPRESS',
                'PLUS',
            ),
            Group::SHIPPING_GROUP_F => array(
                'EXPEDITED',
                'SAVER',
                'EXPRESS',
                'PLUS',
            ),
        );

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
        $method_paket_national = array(
            'id'    => CaseConverter::screamingToLisp($method),
            'title' => sprintf(
                'UPS (%s)' . '<!-- BREAK -->' . '<strong>UPS %s</strong><br>%s',
                $this->getNameBoxWeight(),
                $this->getConfig('SHIPPING_METHOD_' . $method),
                $this->getConfig(Group::SHIPPING_NATIONAL . '_START_TITLE')
            ),
            'cost'  => 0,
            'debug' => array(
                'calculations' => array(),
            ),
        );

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
                $costs_before = $method_paket_national['cost'];

                $method_paket_national['cost']                   += $weight_cost;
                $method_paket_national['debug']['calculations'][] = sprintf(
                    'Costs (%01.2f €) + National shipping (%s) (%01.2f €) for shipment (%01.2f kg) = %01.2f €',
                    $costs_before,
                    $method,
                    $weight_cost,
                    $this->total_weight,
                    $method_paket_national['cost']
                );

                break;
            }
        }

        /** Costs per kg */
        if ($this->total_weight > $box_maximum_weight) {
            $box_weight_excess = $this->total_weight - $box_maximum_weight;
            $weight_cost_kg    = $this->getConfig(Group::SHIPPING_NATIONAL . '_' . $method . '_KG');
            $weight_cost       = max(
                $weight_cost_min,
                $box_maximum_costs + ceil($box_weight_excess) * $weight_cost_kg
            );
            $costs_before      = $method_paket_national['cost'];

            $method_paket_national['cost']                   += $weight_cost;
            $method_paket_national['debug']['calculations'][] = sprintf(
                'Costs (%01.2f €) + National shipping (%s) (%01.2f €) for shipment (%01.2f kg) = %01.2f €',
                $costs_before,
                $method,
                $weight_cost,
                $this->total_weight,
                $method_paket_national['cost']
            );
        }

        return $method_paket_national;
    }

    private function getShippingMethodGroups(string $group, string $method): array
    {
        $method_group = array(
            'id'    => CaseConverter::screamingToLisp($method),
            'title' => sprintf(
                'UPS %1$s (%2$s)' . '<!-- BREAK -->' . '<strong>UPS %1$s</strong><br>%3$s',
                $this->getConfig('SHIPPING_METHOD_' . $method),
                $this->getNameBoxWeight(),
                $this->getConfig($group . '_START_TITLE')
            ),
            'cost'  => 0,
            'debug' => array(
                'calculations' => array(),
            ),
        );

        if ('STANDARD' === $method) {
            $method_group['title'] = sprintf(
                'UPS (%2$s)' . '<!-- BREAK -->' . '<strong>UPS %1$s</strong><br>%3$s',
                $this->getConfig('SHIPPING_METHOD_' . $method),
                $this->getNameBoxWeight(),
                $this->getConfig($group . '_START_TITLE')
            );
        }

        $shipping_costs_group = json_decode($this->getConfig($group . '_' . $method . '_COSTS'), true);

        asort($shipping_costs_group);

        $shipping_costs_min_group = $this->getConfig($group . '_' . $method . '_MIN');

        foreach ($this->boxes as $box_index => $box) {
            $box_weight         = $box->getWeight();
            $box_maximum_weight = 0;
            $box_maximum_costs  = 0;

            /** Costs per table */
            foreach ($shipping_costs_group as $shipping_group_cost) {
                $weight_max         = (float) $shipping_group_cost['weight-max'];
                $weight_cost        = max(
                    (float) $shipping_group_cost['weight-costs'],
                    $shipping_costs_min_group
                );
                $box_maximum_weight = $weight_max;
                $box_maximum_costs  = $weight_cost;

                if ($box_weight <= $weight_max) {
                    $costs_before = $method_group['cost'];

                    $method_group['cost']                   += $weight_cost;
                    $method_group['debug']['calculations'][] = sprintf(
                        'Costs (%01.2f €) + National shipping (%s) (%01.2f €) for box %d / %d (%01.2f kg) = %01.2f €',
                        $costs_before,
                        $method,
                        $weight_cost,
                        $box_index + 1,
                        count($this->boxes),
                        $box_weight,
                        $method_group['cost']
                    );

                    break;
                }
            }

            /** Costs per kg */
            if ($box_weight > $box_maximum_weight) {
                $box_weight_excess = $box_weight - $box_maximum_weight;
                $weight_cost_kg    = $this->getConfig($group . '_' . $method . '_KG');
                $weight_cost       = max(
                    $shipping_costs_min_group,
                    $box_maximum_costs + ceil($box_weight_excess) * $weight_cost_kg
                );
                $costs_before      = $method_group['cost'];

                $method_group['cost']                   += $weight_cost;
                $method_group['debug']['calculations'][] = sprintf(
                    'Costs (%01.2f €) + %s (%s) (%01.2f €) for box %d / %d (%01.2f kg) = %01.2f €',
                    $costs_before,
                    $group,
                    $method,
                    $weight_cost,
                    $box_index + 1,
                    count($this->boxes),
                    $box_weight,
                    $method_group['cost']
                );
            }
        }

        return $method_group;
    }

    private function setSurcharges(): void
    {
        /**
         * Surcharges
         */
        $surcharges_config = json_decode($this->getConfig('SURCHARGES_SURCHARGES'), true);
        $surcharges        = array();
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

                                $method_cost                       = $method['cost'];
                                $method['cost']                   += $surcharge_amount;
                                $method['debug']['calculations'][] = sprintf(
                                    'Costs (%01.2f €) + %s (%01.2f €) for box %d / %d (%01.2f kg) = %01.2f €',
                                    $method_cost,
                                    $surcharge['name'],
                                    $surcharge['surcharge'],
                                    $box_index + 1,
                                    count($this->boxes),
                                    $box_weight,
                                    $method['cost']
                                );
                            }
                        } else {
                            if ($this->total_weight >= $for_weight) {
                                $method_cost                       = $method['cost'];
                                $method['cost']                   += $surcharge_amount;
                                $method['debug']['calculations'][] = sprintf(
                                    'Costs (%01.2f €) + %s (%01.2f €) for order (%01.2f kg) = %01.2f €',
                                    $method_cost,
                                    $surcharge['name'],
                                    $surcharge['surcharge'],
                                    $this->total_weight,
                                    $method['cost']
                                );
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

                                $method['cost']                   += $surcharge_amount;
                                $method['debug']['calculations'][] = sprintf(
                                    'Costs before surcharges (%01.2f €) * (%s: %01.2f %% (%01.2f €)) for box %d / %d (%01.2f kg) = %01.2f €',
                                    $cost_before_surcharges,
                                    $surcharge['name'],
                                    $surcharge['surcharge'],
                                    $surcharge_amount,
                                    $box_index + 1,
                                    count($this->boxes),
                                    $box_weight,
                                    $method['cost']
                                );
                            }
                        } else {
                            if ($this->total_weight >= $for_weight) {
                                $method_cost                       = $method['cost'];
                                $method['cost']                   += $surcharge_amount;
                                $method['debug']['calculations'][] = sprintf(
                                    'Costs before surcharges (%01.2f €) * (%s: %01.2f %% (%01.2f €)) for order (%01.2f kg) = %01.2f €',
                                    $method_cost,
                                    $surcharge['name'],
                                    $surcharge['surcharge'],
                                    $surcharge_amount,
                                    $this->total_weight,
                                    $method['cost']
                                );
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
                        $cost_before = $method['cost'];

                        $method['cost']                   += $weight_cost;
                        $method['debug']['calculations'][] = sprintf(
                            'Costs (%01.2f €) + Pick and Pack (%01.2f €) for box %d / %d (%01.2f kg) = %01.2f €',
                            $cost_before,
                            $weight_cost,
                            $box_index + 1,
                            count($this->boxes),
                            $box_weight,
                            $method['cost']
                        );

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

                $method['debug']['calculations'][] = sprintf(
                    'Costs (%01.2f €) round up to %01.2f = %01.2f',
                    $method_cost,
                    $surcharges_round_up_to,
                    $round_up_to
                );

                $method['cost'] = $round_up_to;
            }
        }
        /** */
    }

    private function getNameBoxWeight(): string
    {
        /**
         * Description
         *//*
        foreach ($this->methods as &$method) {
            $method['title'] .=
        }*/

        /**
         * Output debug
         */
        $debug_is_enabled = $this->getConfig('DEBUG_ENABLE');
        $user_is_admin    = isset($_SESSION['customers_status']['customers_status_id']) && 0 === (int) $_SESSION['customers_status']['customers_status_id'];

        if ('true' === $debug_is_enabled && $user_is_admin) {
            foreach ($this->methods as &$method) {
                ob_start();
                ?>
                <br /><br />

                <h3>Debug mode</h3>

                <?php foreach ($method['debug']['calculations'] as $calculation) { ?>
                    <p><?= $calculation ?></p>
                <?php } ?>
                <?php
                $method['title'] .= ob_get_clean();
            }
        }

        /**
         * Box weights
         */
        $boxes_weight = array();

        foreach ($this->boxes as $box) {
            $key = $box->getWeight() . ' kg';

            if (isset($boxes_weight[$key])) {
                $boxes_weight[$key]++;
            } else {
                $boxes_weight[$key] = 1;
            }
        }

        $boxes_weight_text = array();

        foreach ($boxes_weight as $weight_text => $quantity) {
            preg_match('/[\d+\.]+/', $weight_text, $weight_matches);

            $weight = round($weight_matches[0], 2) . ' kg';

            $boxes_weight_text[] = sprintf(
                '%dx %s',
                $quantity,
                $weight
            );
        }

        if ('true' !== $debug_is_enabled || !$user_is_admin) {
            $boxes_weight_text = array(
                sprintf(
                    '%s kg',
                    round($this->total_weight, 2)
                ),
            );
        }

        return implode(', ', $boxes_weight_text);
    }

    public function getQuote(): array
    {
        $quote = array(
            'id'      => 'grandeljayups',
            'module'  => sprintf(
                'UPS (%s)',
                $this->getNameBoxWeight()
            ),
            'methods' => $this->methods,
        );

        return $quote;
    }
}
