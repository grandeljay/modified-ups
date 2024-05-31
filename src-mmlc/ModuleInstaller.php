<?php

namespace Grandeljay\Ups;

use Grandeljay\Ups\Configuration\Group;
use Grandeljay\Ups\Configuration\Field;

class ModuleInstaller
{
    public function __construct(private object $parent)
    {
    }

    public function install(): void
    {
        /**
         * Required for modified compatibility
         */
        $this->parent->addConfiguration('ALLOWED', '', 6, 1);
        $this->parent->addConfiguration('SORT_ORDER', 0, 6, 1);
        $this->parent->addConfigurationSelect('DEBUG_ENABLE', 'true', 6, 1);

        $this->installWeight();
        $this->installMethods();
        $this->installShippingNational();
        $this->installShippingGroups();
        $this->installSurcharges();
    }

    private function installWeight(): void
    {
        $prefix       = Group::SHIPPING_WEIGHT . '_';
        $title        = $this->parent->getConfig(Group::SHIPPING_WEIGHT . '_START_TITLE');
        $maximum      = 45;
        $ideal        = 15;
        $set_function = \grandeljayups::class . '::setFunction';

        $this->parent->addConfiguration($prefix . 'START', $title, 6, 1, $set_function);
        $this->parent->addConfiguration($prefix . 'MAX', $maximum, 6, 1, $set_function);
        $this->parent->addConfiguration($prefix . 'IDEAL', $ideal, 6, 1, $set_function);
        $this->parent->addConfiguration($prefix . 'END', '', 6, 1, $set_function);
    }

    private function installMethods(): void
    {
        $prefix              = Group::SHIPPING_METHODS . '_';
        $title               = $this->parent->getConfig(Group::SHIPPING_METHODS . '_START_TITLE');
        $method_standard     = 'true';
        $method_saver        = 'true';
        $method_1200         = 'true';
        $method_express      = 'true';
        $method_plus         = 'false';
        $method_expedited    = 'true';
        $set_function_group  = \grandeljayups::class . '::setFunction';
        $set_function_select = 'xtc_cfg_select_option([\'true\', \'false\'], ';

        $this->parent->addConfiguration($prefix . 'START', $title, 6, 1, $set_function_group);
        $this->parent->addConfiguration($prefix . 'STANDARD', $method_standard, 6, 1, $set_function_select);
        $this->parent->addConfiguration($prefix . 'SAVER', $method_saver, 6, 1, $set_function_select);
        $this->parent->addConfiguration($prefix . '1200', $method_1200, 6, 1, $set_function_select);
        $this->parent->addConfiguration($prefix . 'EXPRESS', $method_express, 6, 1, $set_function_select);
        $this->parent->addConfiguration($prefix . 'PLUS', $method_plus, 6, 1, $set_function_select);
        $this->parent->addConfiguration($prefix . 'EXPEDITED', $method_expedited, 6, 1, $set_function_select);
        $this->parent->addConfiguration($prefix . 'END', '', 6, 1, $set_function_group);
    }

    private function installShippingNational(): void
    {
        $prefix       = Group::SHIPPING_NATIONAL . '_';
        $title        = $this->parent->getConfig($prefix . 'START_TITLE');
        $country      = \STORE_COUNTRY;
        $costs        = Field::getShippingNationalMethodCosts();
        $set_function = \grandeljayups::class . '::setFunction(';

        $this->parent->addConfiguration($prefix . 'START', $title, 6, 1, $set_function);
        $this->parent->addConfiguration($prefix . 'COUNTRY', $country, 6, 1, $set_function);

        foreach (\grandeljayups::$methods[Group::SHIPPING_NATIONAL] as $method_name) {
            $method_group = $prefix . $method_name;
            $method_title = $this->parent->getConfig('SHIPPING_METHOD_' . $method_name);
            $method_costs = $costs[$method_group]['costs'];
            $method_kg    = $costs[$method_group]['kg'];
            $method_min   = $costs[$method_group]['min'];

            $this->parent->addConfiguration($method_group . '_START', $method_title, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_COSTS', $method_costs, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_KG', $method_kg, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_MIN', $method_min, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_END', '', 6, 1, $set_function);
        }

        $this->parent->addConfiguration($prefix . 'END', '', 6, 1, $set_function);
    }

    private function installShippingGroups(): void
    {
        $this->installShippingGroupA();
        $this->installShippingGroupB();
        $this->installShippingGroupC();
        $this->installShippingGroupD();
        $this->installShippingGroupE();
        $this->installShippingGroupF();
    }

    private function installShippingGroupA(): void
    {
        $prefix       = Group::SHIPPING_GROUP_A . '_';
        $title        = $this->parent->getConfig($prefix . 'START_TITLE');
        $countries    = 'AT, BE, CZ, HU, LU, NL, PL';
        $costs        = Field::getShippingCountryGroupACosts();
        $set_function = \grandeljayups::class . '::setFunction(';

        $this->parent->addConfiguration($prefix . 'START', $title, 6, 1, $set_function);
        $this->parent->addConfiguration($prefix . 'COUNTRIES', $countries, 6, 1);

        foreach (\grandeljayups::$methods[Group::SHIPPING_GROUP_A] as $method_name) {
            $method_group = $prefix . $method_name;
            $method_title = $this->parent->getConfig('SHIPPING_METHOD_' . $method_name);
            $method_costs = $costs[$method_group]['costs'];
            $method_kg    = $costs[$method_group]['kg'];
            $method_min   = $costs[$method_group]['min'];

            $this->parent->addConfiguration($method_group . '_START', $method_title, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_COSTS', $method_costs, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_KG', $method_kg, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_MIN', $method_min, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_END', '', 6, 1, $set_function);
        }

        $this->parent->addConfiguration($prefix . 'END', '', 6, 1, $set_function);
    }

    private function installShippingGroupB(): void
    {
        $prefix       = Group::SHIPPING_GROUP_B . '_';
        $title        = $this->parent->getConfig($prefix . 'START_TITLE');
        $countries    = 'DK, ES, FR, HR, IT, RO, SI, PT';
        $costs        = Field::getShippingCountryGroupBCosts();
        $set_function = \grandeljayups::class . '::setFunction(';

        $this->parent->addConfiguration($prefix . 'START', $title, 6, 1, $set_function);
        $this->parent->addConfiguration($prefix . 'COUNTRIES', $countries, 6, 1);

        foreach (\grandeljayups::$methods[Group::SHIPPING_GROUP_B] as $method_name) {
            $method_group = $prefix . $method_name;
            $method_title = $this->parent->getConfig('SHIPPING_METHOD_' . $method_name);
            $method_costs = $costs[$method_group]['costs'];
            $method_kg    = $costs[$method_group]['kg'];
            $method_min   = $costs[$method_group]['min'];

            $this->parent->addConfiguration($method_group . '_START', $method_title, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_COSTS', $method_costs, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_KG', $method_kg, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_MIN', $method_min, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_END', '', 6, 1, $set_function);
        }

        $this->parent->addConfiguration(Group::SHIPPING_GROUP_B . '_END', '', 6, 1, $set_function);
    }

    private function installShippingGroupC(): void
    {
        $prefix       = Group::SHIPPING_GROUP_C . '_';
        $title        = $this->parent->getConfig($prefix . 'START_TITLE');
        $countries    = 'BG, CY, EE, FI, GR, IC, IE, LT, LV, MT, SE, SK';
        $costs        = Field::getShippingCountryGroupCCosts();
        $set_function = \grandeljayups::class . '::setFunction(';

        $this->parent->addConfiguration(Group::SHIPPING_GROUP_C . '_START', $title, 6, 1, $set_function);
        $this->parent->addConfiguration(Group::SHIPPING_GROUP_C . '_COUNTRIES', $countries, 6, 1);

        foreach (\grandeljayups::$methods[Group::SHIPPING_GROUP_C] as $method_name) {
            $method_group = $prefix . $method_name;
            $method_title = $this->parent->getConfig('SHIPPING_METHOD_' . $method_name);
            $method_costs = $costs[$method_group]['costs'];
            $method_kg    = $costs[$method_group]['kg'];
            $method_min   = $costs[$method_group]['min'];

            $this->parent->addConfiguration($method_group . '_START', $method_title, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_COSTS', $method_costs, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_KG', $method_kg, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_MIN', $method_min, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_END', '', 6, 1, $set_function);
        }

        $this->parent->addConfiguration(Group::SHIPPING_GROUP_C . '_END', '', 6, 1, $set_function);
    }

    private function installShippingGroupD(): void
    {
        $prefix       = Group::SHIPPING_GROUP_D . '_';
        $title        = $this->parent->getConfig($prefix . 'START_TITLE');
        $countries    = 'AD, CH, GB, GG, JE, NO, SM';
        $costs        = Field::getShippingCountryGroupDCosts();
        $set_function = \grandeljayups::class . '::setFunction(';

        $this->parent->addConfiguration($prefix . 'START', $title, 6, 1, $set_function);
        $this->parent->addConfiguration($prefix . 'COUNTRIES', $countries, 6, 1);

        foreach (\grandeljayups::$methods[Group::SHIPPING_GROUP_D] as $method_name) {
            $method_group = $prefix . $method_name;
            $method_title = $this->parent->getConfig('SHIPPING_METHOD_' . $method_name);
            $method_costs = $costs[$method_group]['costs'];
            $method_kg    = $costs[$method_group]['kg'];
            $method_min   = $costs[$method_group]['min'];

            $this->parent->addConfiguration($method_group . '_START', $method_title, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_COSTS', $method_costs, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_KG', $method_kg, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_MIN', $method_min, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_END', '', 6, 1, $set_function);
        }

        $this->parent->addConfiguration($prefix . 'END', '', 6, 1, \grandeljayups::class . '::setFunction(');
    }

    private function installShippingGroupE(): void
    {
        $prefix       = Group::SHIPPING_GROUP_E . '_';
        $title        = $this->parent->getConfig($prefix . 'START_TITLE');
        $countries    = 'AE, BA, CA, CN, HK, IN, JP, KV, MD, ME, MK, MX, RS, SA, SG, TR, TW, UA, US, VN';
        $costs        = Field::getShippingCountryGroupECosts();
        $set_function = \grandeljayups::class . '::setFunction(';

        $this->parent->addConfiguration($prefix . 'START', $title, 6, 1, $set_function);
        $this->parent->addConfiguration($prefix . 'COUNTRIES', $countries, 6, 1);

        foreach (\grandeljayups::$methods[Group::SHIPPING_GROUP_E] as $method_name) {
            $method_group = $prefix . $method_name;
            $method_title = $this->parent->getConfig('SHIPPING_METHOD_' . $method_name);
            $method_costs = $costs[$method_group]['costs'];
            $method_kg    = $costs[$method_group]['kg'];
            $method_min   = $costs[$method_group]['min'];

            $this->parent->addConfiguration($method_group . '_START', $method_title, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_COSTS', $method_costs, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_KG', $method_kg, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_MIN', $method_min, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_END', '', 6, 1, $set_function);
        }

        $this->parent->addConfiguration($prefix . 'END', '', 6, 1, $set_function);
    }

    private function installShippingGroupF(): void
    {
        $prefix       = Group::SHIPPING_GROUP_F . '_';
        $title        = $this->parent->getConfig($prefix . 'START_TITLE');
        $costs        = Field::getShippingCountryGroupFCosts();
        $set_function = \grandeljayups::class . '::setFunction(';

        $this->parent->addConfiguration($prefix . 'START', $title, 6, 1, $set_function);
        $this->parent->addConfiguration($prefix . 'COUNTRIES', '', 6, 1);

        foreach (\grandeljayups::$methods[Group::SHIPPING_GROUP_F] as $method_name) {
            $method_group = $prefix . $method_name;
            $method_title = $this->parent->getConfig('SHIPPING_METHOD_' . $method_name);
            $method_costs = $costs[$method_group]['costs'];
            $method_kg    = $costs[$method_group]['kg'];
            $method_min   = $costs[$method_group]['min'];

            $this->parent->addConfiguration($method_group . '_START', $method_title, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_COSTS', $method_costs, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_KG', $method_kg, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_MIN', $method_min, 6, 1, $set_function);
            $this->parent->addConfiguration($method_group . '_END', '', 6, 1, $set_function);
        }

        $this->parent->addConfiguration($prefix . 'END', '', 6, 1, $set_function);
    }

    private function installSurcharges(): void
    {
        $prefix       = Group::SURCHARGES . '_';
        $title        = $this->parent->getConfig($prefix . 'START_TITLE');
        $set_function = \grandeljayups::class . '::setFunction(';

        $surcharges    = json_encode(
            [
                [
                    'name'                         => 'Treibstoffzuschlag',
                    'surcharge'                    => 18.25,
                    'type'                         => 'percent',
                    'configuration[per-package-0]' => 'false',
                    'for-method'                   => 'standard',
                ],
                [
                    'name'                         => 'Treibstoffzuschlag',
                    'surcharge'                    => 29.25,
                    'type'                         => 'percent',
                    'configuration[per-package-1]' => 'false',
                    'for-method'                   => 'all-others',
                ],
                [
                    'name'                         => 'Peak Handhabung',
                    'surcharge'                    => 4.90,
                    'type'                         => 'fixed',
                    'configuration[per-package-2]' => 'true',
                    'for-weight'                   => 32,
                    'duration-start'               => date('Y') . '-10-31',
                    'duration-end'                 => date('Y') + 1 . '-01-15',
                ],
                [
                    'name'                         => 'Peak großes Paket',
                    'surcharge'                    => 53,
                    'type'                         => 'fixed',
                    'configuration[per-package-3]' => 'true',
                    'for-weight'                   => 40,
                    'duration-start'               => date('Y') . '-10-31',
                    'duration-end'                 => date('Y') + 1 . '-01-15',
                ],
                [
                    'name'                         => 'Zuschlag Handhabung',
                    'surcharge'                    => 10,
                    'type'                         => 'fixed',
                    'configuration[per-package-4]' => 'true',
                    'for-weight'                   => 32,
                ],
                [
                    'name'                         => 'Zuschlag großes Paket',
                    'surcharge'                    => 71.5,
                    'type'                         => 'fixed',
                    'configuration[per-package-5]' => 'true',
                    'for-weight'                   => 40,
                ],
            ],
            \JSON_UNESCAPED_UNICODE
        );
        $pick_and_pack = json_encode(
            [
                [
                    'weight-max'   => 1.00,
                    'weight-costs' => 1.30,
                ],
                [
                    'weight-max'   => 5.00,
                    'weight-costs' => 1.60,
                ],
                [
                    'weight-max'   => 10.00,
                    'weight-costs' =>  2.00,
                ],
                [
                    'weight-max'   => 20.00,
                    'weight-costs' =>  2.60,
                ],
                [
                    'weight-max'   => 60.00,
                    'weight-costs' =>  3.00,
                ],
            ],
        );

        $this->parent->addConfiguration($prefix . 'START', $title, 6, 1, $set_function);
        $this->parent->addConfiguration($prefix . 'SURCHARGES', $surcharges, 6, 1, $set_function);
        $this->parent->addConfiguration($prefix . 'PICK_AND_PACK', $pick_and_pack, 6, 1, $set_function);
        $this->parent->addConfigurationSelect($prefix . 'ROUND_UP', 'true', 6, 1);
        $this->parent->addConfiguration($prefix . 'ROUND_UP_TO', 0.90, 6, 1, $set_function);
        $this->parent->addConfiguration($prefix . 'END', '', 6, 1, $set_function);
    }
}