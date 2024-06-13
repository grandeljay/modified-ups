<?php

namespace Grandeljay\Ups;

use Grandeljay\Ups\Configuration\Configuration;
use Grandeljay\Ups\Configuration\Group;
use RobinTheHood\ModifiedStdModule\Classes\CaseConverter;

class National extends Method
{
    public static function getMethodNames(): array
    {
        return [
            'STANDARD',
            'SAVER',
            '1200',
            'EXPRESS',
            'PLUS',
        ];
    }

    public function __construct()
    {
        parent::__construct();
    }

    private function getCostsAndCalculations(string $method_name): array
    {
        $calculations = [];

        $total_weight = $this->weight;

        $costs       = 0;
        $costs_json  = Configuration::get(Group::SHIPPING_NATIONAL . '_' . $method_name . '_COSTS');
        $costs_table = json_decode($costs_json, true);

        asort($costs_table);

        $costs_min = Configuration::get(Group::SHIPPING_NATIONAL . '_' . $method_name . '_MIN');

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
                        'National shipping (%s) for shipment (%01.2f kg)',
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
            $weight_cost_kg    = Configuration::get(Group::SHIPPING_NATIONAL . '_' . $method_name . '_KG');
            $weight_cost       = max(
                $costs_min,
                $box_maximum_costs + ceil($box_weight_excess) * $weight_cost_kg
            );
            $costs            += $weight_cost;
            $calculations[]    = [
                'item'  => sprintf(
                    'National shipping (%s) for shipment (%01.2f kg)',
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

        foreach (self::getMethodNames() as $method_name) {
            if (!Method::isEnabled($method_name)) {
                continue;
            }

            $method_id                 = CaseConverter::screamingToLisp($method_name);
            $method_type               = Configuration::get('SHIPPING_METHOD_' . $method_name);
            $method_type_description   = Configuration::get(Group::SHIPPING_NATIONAL . '_START_TITLE');
            $method_format             = 'UPS %1$s (%2$s)' . '<!-- BREAK -->' . '<strong>UPS %1$s</strong><br>%3$s';
            $method_weight             = $this->weight_formatted;
            $method_costs_calculations = $this->getCostsAndCalculations($method_name);
            $method_title              = sprintf(
                $method_format,
                $method_type,
                $method_weight,
                $method_type_description
            );

            $method    = [
                'id'           => $method_id,
                'title'        => $method_title,
                'cost'         => $method_costs_calculations['costs'],
                'calculations' => $method_costs_calculations['calculations'],
            ];
            $methods[] = $method;
        }

        $this->setSurcharges($methods);

        return $methods;
    }
}
