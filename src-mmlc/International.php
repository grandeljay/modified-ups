<?php

namespace Grandeljay\Ups;

use Grandeljay\Ups\Configuration\Configuration;
use Grandeljay\Ups\Configuration\Group;
use RobinTheHood\ModifiedStdModule\Classes\CaseConverter;

class International extends Method
{
    private static function getShippingGroups(): array
    {
        return [
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
    }

    private static function getShippingGroupName(): string
    {
        $method_groups = self::getShippingGroups();

        foreach ($method_groups as $method_group => $method_names) {
            $countries_field = Configuration::get($method_group . '_COUNTRIES');
            $countries       = array_map('trim', explode(',', $countries_field));

            if (in_array($_SESSION['delivery_zone'], $countries, true)) {
                return $method_group;
            }
        }

        return Group::SHIPPING_GROUP_F;
    }

    public function __construct()
    {
        parent::__construct();
    }

    private function getCostsAndCalculations(string $method_group, string $method_name): array
    {
        $calculations = [];

        $total_weight = $this->weight;

        $costs       = 0;
        $costs_json  = Configuration::get($method_group . '_' . $method_name . '_COSTS');
        $costs_table = json_decode($costs_json, true);

        asort($costs_table);

        $costs_min = Configuration::get($method_group . '_' . $method_name . '_MIN');

        $box_maximum_weight = 0;
        $box_maximum_costs  = 0;

        /** Costs per table */
        foreach ($costs_table as $costs_entry) {
            $weight_max         = (float) $costs_entry['weight-max'];
            $weight_cost        = max(
                (float) $costs_entry['weight-costs'],
                $costs_min
            );
            $box_maximum_weight = $weight_max;
            $box_maximum_costs  = $weight_cost;

            if ($total_weight <= $weight_max) {
                $costs         += $weight_cost;
                $calculations[] = [
                    'item'  => sprintf(
                        'International shipping (%s) for shipment (%01.2f kg)',
                        $method_name,
                        $total_weight
                    ),
                    'costs' => $weight_cost,
                ];

                break;
            }
        }

        /** Costs per kg */
        if ($total_weight > $box_maximum_weight) {
            $box_weight_excess = $total_weight - $box_maximum_weight;
            $weight_cost_kg    = Configuration::get($method_group . '_' . $method_name . '_KG');
            $weight_cost       = max(
                $costs_min,
                $box_maximum_costs + ceil($box_weight_excess) * $weight_cost_kg
            );
            $costs            += $weight_cost;
            $calculations[]    = [
                'item'  => sprintf(
                    'International shipping (%s) for shipment (%01.2f kg)',
                    $method_name,
                    $total_weight
                ),
                'costs' => $weight_cost,
            ];
        }

        return [
            'costs'        => $costs,
            'calculations' => $calculations,
        ];
    }

    public function getMethods(): array
    {
        $methods = [];

        if ($this->exceedsMaximumWeight()) {
            return [];
        }

        $method_groups      = self::getShippingGroups();
        $method_group_name  = self::getShippingGroupName();
        $method_group_names = $method_groups[$method_group_name];

        foreach ($method_group_names as $method_name) {
            if (!Method::isEnabled($method_name)) {
                continue;
            }

            if (Method::isExcluded('INTERNATIONAL_' . $method_name)) {
                continue;
            }

            $method_id                 = CaseConverter::screamingToLisp($method_name);
            $method_type               = Configuration::get('SHIPPING_METHOD_' . $method_name);
            $method_type_description   = Configuration::get($method_group_name . '_START_TITLE');
            $method_costs_calculations = $this->getCostsAndCalculations($method_group_name, $method_name);

            $method    = [
                'id'               => $method_id,
                'title'            => $method_type,
                'description'      => $method_type_description,
                'cost'             => $method_costs_calculations['costs'],
                'calculations'     => $method_costs_calculations['calculations'],
                'weight_formatted' => $this->weight_formatted,
            ];
            $methods[] = $method;
        }

        $this->setSurcharges($methods);

        return $methods;
    }
}
