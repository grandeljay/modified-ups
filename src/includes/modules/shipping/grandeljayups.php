<?php

/**
 * UPS
 *
 * @author  Jay Trees <ups@grandels.email>
 * @link    https://github.com/grandeljay/modified-ups
 * @package GrandelJayUPS
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 */

use Grandeljay\Ups\{Constants, Quote};
use Grandeljay\Ups\Configuration\{Group, Field};
use RobinTheHood\ModifiedStdModule\Classes\{StdModule, CaseConverter};

/**
 * Shipping methods musn't contain underscores.
 */
class grandeljayups extends StdModule
{
    public const VERSION = '0.8.0';

    public static array $methods_international = [
        Group::SHIPPING_GROUP_A,
        Group::SHIPPING_GROUP_B,
        Group::SHIPPING_GROUP_C,
        Group::SHIPPING_GROUP_D,
        Group::SHIPPING_GROUP_E,
        Group::SHIPPING_GROUP_F,
    ];

    public static array $methods = [
        Group::SHIPPING_NATIONAL => [
            'STANDARD',
            'SAVER',
            '1200',
            'EXPRESS',
            'PLUS',
        ],
        Group::SHIPPING_GROUP_A  => [
            'STANDARD',
            'SAVER',
            'EXPRESS',
            'PLUS',
        ],
        Group::SHIPPING_GROUP_B  => [
            'STANDARD',
            'SAVER',
            'EXPRESS',
            'PLUS',
        ],
        Group::SHIPPING_GROUP_C  => [
            'STANDARD',
            'SAVER',
            'EXPRESS',
            'PLUS',
        ],
        Group::SHIPPING_GROUP_D  => [
            'STANDARD',
            'SAVER',
            'EXPRESS',
            'PLUS',
        ],
        Group::SHIPPING_GROUP_E  => [
            'EXPEDITED',
            'SAVER',
            'EXPRESS',
            'PLUS',
        ],
        Group::SHIPPING_GROUP_F  => [
            'EXPEDITED',
            'SAVER',
            'EXPRESS',
            'PLUS',
        ],
    ];

    public static function setFunction(string $value, string $option): string
    {
        /** Set method to use */
        $key_without_module_name = substr(
            $option,
            strlen(Constants::MODULE_SHIPPING_NAME) + 1,
        );
        $method_name             = CaseConverter::screamingToCamel($key_without_module_name);
        /** */

        /** Determine if field is part of a group */
        $namespace_configuration = '\\Grandeljay\\Ups\\Configuration';

        $key_start = substr($key_without_module_name, -5);
        $key_end   = substr($key_without_module_name, -3);
        $is_group  = 'START' === $key_start || 'END' === $key_end;

        if ($is_group) {
            $namespace_configuration .= '\\Group';
        } else {
            $namespace_configuration .= '\\Field';
        }
        /** */

        /** Exceptions */
        if (!method_exists($namespace_configuration, $method_name)) {
            /** Numbers */
            if (is_numeric($value)) {
                $method_name = 'inputNumber';
            }

            /** Methods */
            $option_short = substr($option, strlen(Constants::MODULE_SHIPPING_NAME) + 1);
            $methods      = [
                Group::SHIPPING_METHODS . '_STANDARD',
                Group::SHIPPING_METHODS . '_SAVER',
                Group::SHIPPING_METHODS . '_1200',
                Group::SHIPPING_METHODS . '_EXPRESS',
                Group::SHIPPING_METHODS . '_PLUS',
                Group::SHIPPING_METHODS . '_EXPEDITED',
            ];

            if (in_array($option_short, $methods, true)) {
                $method_name = 'shippingMethods';
            }

            /** Shipping methods */
            $key_group = substr($key_without_module_name, 0, -6);

            if (in_array($key_group, self::$methods[Group::SHIPPING_NATIONAL], true)) {
                $method_name = 'shippingMethodCosts';
            }

            if ($is_group) {
                $key = preg_replace('/SHIPPING_(NATIONAL_|GROUP_[A|B|C|D|E|F]_)/', 'SHIPPING_', $key_without_module_name);

                $method_name = CaseConverter::screamingToCamel($key);
            }
            /** */
        }

        if (1 === preg_match('/^shipping(National|Group[A|B|C|D|E|F])[a-zA-Z0-9]+Costs$/', $method_name)) {
            $method_name = 'shippingMethodCosts';
        }
        /** */

        /** Run method */
        if (method_exists($namespace_configuration, $method_name)) {
            return call_user_func(
                $namespace_configuration . '::' . $method_name,
                $value,
                $option
            );
        }

        return '<div>' . sprintf(
            'Method %s does not exist.',
            '<code>' . $namespace_configuration . '::' . $method_name . '</code>'
        ) . '</div>';
        /** */
    }

    /**
     * Used by modified to determine the cheapest shipping method. Should
     * contain the return value of the `quote` method.
     *
     * @var array
     */
    public array $quotes = [];

    /**
     * Used to calculate the tax.
     *
     * @var int
     */
    public int $tax_class = 1;

    public function __construct()
    {
        parent::__construct(Constants::MODULE_SHIPPING_NAME);

        $this->checkForUpdate(true);

        /**
         * Sort Order
         */
        $this->addkey('SORT_ORDER');

        /**
         * Debug
         */
        $this->addKey('DEBUG_ENABLE');
        /** */

        /**
         * Weight
         */
        $this->addKey(Group::SHIPPING_WEIGHT . '_START');

        $this->addKey(Group::SHIPPING_WEIGHT . '_MAX');
        $this->addKey(Group::SHIPPING_WEIGHT . '_IDEAL');

        $this->addKey(Group::SHIPPING_WEIGHT . '_END');
        /** */

        /**
         * Methods
         */
        $this->addKey(Group::SHIPPING_METHODS . '_START');

        $this->addKey(Group::SHIPPING_METHODS . '_STANDARD');
        $this->addKey(Group::SHIPPING_METHODS . '_SAVER');
        $this->addKey(Group::SHIPPING_METHODS . '_1200');
        $this->addKey(Group::SHIPPING_METHODS . '_EXPRESS');
        $this->addKey(Group::SHIPPING_METHODS . '_PLUS');
        $this->addKey(Group::SHIPPING_METHODS . '_EXPEDITED');

        $this->addKey(Group::SHIPPING_METHODS . '_END');
        /** */

        /**
         * National
         */
        $this->addKey(Group::SHIPPING_NATIONAL . '_START');

        $this->addKey(Group::SHIPPING_NATIONAL . '_COUNTRY');

        foreach (self::$methods[Group::SHIPPING_NATIONAL] as $method) {
            $method = Group::SHIPPING_NATIONAL . '_' . $method;

            $this->addKey($method . '_START');

            $this->addKey($method . '_COSTS');
            $this->addKey($method . '_KG');
            $this->addKey($method . '_MIN');

            $this->addKey($method . '_END');
        }

        $this->addKey(Group::SHIPPING_NATIONAL . '_END');
        /** */

        /**
         * Groups
         */
        foreach (self::$methods_international as $group) {
            $this->addKey($group . '_START');

            if (Group::SHIPPING_GROUP_F !== $group) {
                $this->addKey($group . '_COUNTRIES');
            }

            foreach (self::$methods[$group] as $method_name) {
                $method_group = $group . '_' . $method_name;

                $this->addKey($method_group . '_START');

                $this->addKey($method_group . '_COSTS');
                $this->addKey($method_group . '_KG');
                $this->addKey($method_group . '_MIN');

                $this->addKey($method_group . '_END');
            }

            $this->addKey($group . '_END');
        }
        /** */

        /**
         * Surcharges
         */
        $this->addKey(Group::SURCHARGES . '_START');

        $this->addKey(Group::SURCHARGES . '_SURCHARGES');

        $this->addKey(Group::SURCHARGES . '_PICK_AND_PACK');

        $this->addKey(Group::SURCHARGES . '_ROUND_UP');
        $this->addKey(Group::SURCHARGES . '_ROUND_UP_TO');

        $this->addKey(Group::SURCHARGES . '_END');
        /** */
    }

    public function install()
    {
        parent::install();

        /**
         * Required for modified compatibility
         */
        $this->addConfiguration('ALLOWED', '', 6, 1);
        /** */

        /**
         * Sort Order
         */
        $this->addConfiguration('SORT_ORDER', 0, 6, 1);

        /**
         * Debug
         */
        $this->addConfigurationSelect('DEBUG_ENABLE', 'true', 6, 1);
        /** */

        /**
         * Weight
         */
        $this->addConfiguration(Group::SHIPPING_WEIGHT . '_START', $this->getConfig(Group::SHIPPING_WEIGHT . '_START_TITLE'), 6, 1, self::class . '::setFunction(');

        $this->addConfiguration(Group::SHIPPING_WEIGHT . '_MAX', 45, 6, 1, self::class . '::setFunction(');
        $this->addConfiguration(Group::SHIPPING_WEIGHT . '_IDEAL', 15, 6, 1, self::class . '::setFunction(');

        $this->addConfiguration(Group::SHIPPING_WEIGHT . '_END', '', 6, 1, self::class . '::setFunction(');
        /** */

        /**
         * Methods
         */
        $this->addConfiguration(Group::SHIPPING_METHODS . '_START', $this->getConfig(Group::SHIPPING_METHODS . '_START_TITLE'), 6, 1, self::class . '::setFunction(');

        $this->addConfiguration(Group::SHIPPING_METHODS . '_STANDARD', 'true', 6, 1, 'xtc_cfg_select_option(array(\'true\', \'false\'), ');
        $this->addConfiguration(Group::SHIPPING_METHODS . '_SAVER', 'true', 6, 1, 'xtc_cfg_select_option(array(\'true\', \'false\'), ');
        $this->addConfiguration(Group::SHIPPING_METHODS . '_1200', 'true', 6, 1, 'xtc_cfg_select_option(array(\'true\', \'false\'), ');
        $this->addConfiguration(Group::SHIPPING_METHODS . '_EXPRESS', 'true', 6, 1, 'xtc_cfg_select_option(array(\'true\', \'false\'), ');
        $this->addConfiguration(Group::SHIPPING_METHODS . '_PLUS', 'false', 6, 1, 'xtc_cfg_select_option(array(\'true\', \'false\'), ');
        $this->addConfiguration(Group::SHIPPING_METHODS . '_EXPEDITED', 'true', 6, 1, 'xtc_cfg_select_option(array(\'true\', \'false\'), ');

        $this->addConfiguration(Group::SHIPPING_METHODS . '_END', '', 6, 1, self::class . '::setFunction(');
        /** */

        /**
         * National
         */
        $national_title   = $this->getConfig(Group::SHIPPING_NATIONAL . '_START_TITLE');
        $national_country = \STORE_COUNTRY;
        $national_costs   = Field::getShippingNationalMethodCosts();

        $this->addConfiguration(Group::SHIPPING_NATIONAL . '_START', $national_title, 6, 1, self::class . '::setFunction(');

        $this->addConfiguration(Group::SHIPPING_NATIONAL . '_COUNTRY', $national_country, 6, 1, self::class . '::setFunction(');

        foreach (self::$methods[Group::SHIPPING_NATIONAL] as $method_name) {
            $method_group = Group::SHIPPING_NATIONAL . '_' . $method_name;

            $national_method_title = $this->getConfig('SHIPPING_METHOD_' . $method_name);
            $national_method_costs = $national_costs[$method_group]['costs'];
            $national_method_kg    = $national_costs[$method_group]['kg'];
            $national_method_min   = $national_costs[$method_group]['min'];

            $this->addConfiguration($method_group . '_START', $national_method_title, 6, 1, self::class . '::setFunction(');

                $this->addConfiguration($method_group . '_COSTS', $national_method_costs, 6, 1, self::class . '::setFunction(');
                $this->addConfiguration($method_group . '_KG', $national_method_kg, 6, 1, self::class . '::setFunction(');
                $this->addConfiguration($method_group . '_MIN', $national_method_min, 6, 1, self::class . '::setFunction(');

            $this->addConfiguration($method_group . '_END', '', 6, 1, self::class . '::setFunction(');
        }

        $this->addConfiguration(Group::SHIPPING_NATIONAL . '_END', '', 6, 1, self::class . '::setFunction(');
        /** */

        /**
         * Group A
         */
        $group_a_title     = $this->getConfig(Group::SHIPPING_GROUP_A . '_START_TITLE');
        $group_a_countries = 'AT, BE, CZ, HU, LU, NL, PL';
        $group_a_costs     = Field::getShippingCountryGroupACosts();

        $this->addConfiguration(Group::SHIPPING_GROUP_A . '_START', $group_a_title, 6, 1, self::class . '::setFunction(');

        $this->addConfiguration(Group::SHIPPING_GROUP_A . '_COUNTRIES', $group_a_countries, 6, 1);

        foreach (self::$methods[Group::SHIPPING_GROUP_A] as $method_name) {
            $method_group = Group::SHIPPING_GROUP_A . '_' . $method_name;

            $group_a_method_title = $this->getConfig('SHIPPING_METHOD_' . $method_name);
            $group_a_method_costs = $group_a_costs[$method_group]['costs'];
            $group_a_method_kg    = $group_a_costs[$method_group]['kg'];
            $group_a_method_min   = $group_a_costs[$method_group]['min'];

            $this->addConfiguration($method_group . '_START', $group_a_method_title, 6, 1, self::class . '::setFunction(');

                $this->addConfiguration($method_group . '_COSTS', $group_a_method_costs, 6, 1, self::class . '::setFunction(');
                $this->addConfiguration($method_group . '_KG', $group_a_method_kg, 6, 1, self::class . '::setFunction(');
                $this->addConfiguration($method_group . '_MIN', $group_a_method_min, 6, 1, self::class . '::setFunction(');

            $this->addConfiguration($method_group . '_END', '', 6, 1, self::class . '::setFunction(');
        }

        $this->addConfiguration(Group::SHIPPING_GROUP_A . '_END', '', 6, 1, self::class . '::setFunction(');
        /** */

        /**
         * Group B
         */
        $group_b_title     = $this->getConfig(Group::SHIPPING_GROUP_B . '_START_TITLE');
        $group_b_countries = 'DK, ES, FR, HR, IT, RO, SI, PT';
        $group_b_costs     = Field::getShippingCountryGroupBCosts();

        $this->addConfiguration(Group::SHIPPING_GROUP_B . '_START', $group_b_title, 6, 1, self::class . '::setFunction(');

        $this->addConfiguration(Group::SHIPPING_GROUP_B . '_COUNTRIES', $group_b_countries, 6, 1);

        foreach (self::$methods[Group::SHIPPING_GROUP_B] as $method_name) {
            $method_group = Group::SHIPPING_GROUP_B . '_' . $method_name;

            $group_b_method_title = $this->getConfig('SHIPPING_METHOD_' . $method_name);
            $group_b_method_costs = $group_b_costs[$method_group]['costs'];
            $group_b_method_kg    = $group_b_costs[$method_group]['kg'];
            $group_b_method_min   = $group_b_costs[$method_group]['min'];

            $this->addConfiguration($method_group . '_START', $group_b_method_title, 6, 1, self::class . '::setFunction(');

                $this->addConfiguration($method_group . '_COSTS', $group_b_method_costs, 6, 1, self::class . '::setFunction(');
                $this->addConfiguration($method_group . '_KG', $group_b_method_kg, 6, 1, self::class . '::setFunction(');
                $this->addConfiguration($method_group . '_MIN', $group_b_method_min, 6, 1, self::class . '::setFunction(');

            $this->addConfiguration($method_group . '_END', '', 6, 1, self::class . '::setFunction(');
        }

        $this->addConfiguration(Group::SHIPPING_GROUP_B . '_END', '', 6, 1, self::class . '::setFunction(');
        /** */

        /**
         * Group C
         */
        $group_c_title     = $this->getConfig(Group::SHIPPING_GROUP_C . '_START_TITLE');
        $group_c_countries = 'BG, CY, EE, FI, GR, IC, IE, LT, LV, MT, SE, SK';
        $group_c_costs     = Field::getShippingCountryGroupCCosts();

        $this->addConfiguration(Group::SHIPPING_GROUP_C . '_START', $group_c_title, 6, 1, self::class . '::setFunction(');

        $this->addConfiguration(Group::SHIPPING_GROUP_C . '_COUNTRIES', $group_c_countries, 6, 1);

        foreach (self::$methods[Group::SHIPPING_GROUP_C] as $method_name) {
            $method_group = Group::SHIPPING_GROUP_C . '_' . $method_name;

            $group_c_method_title = $this->getConfig('SHIPPING_METHOD_' . $method_name);
            $group_c_method_costs = $group_c_costs[$method_group]['costs'];
            $group_c_method_kg    = $group_c_costs[$method_group]['kg'];
            $group_c_method_min   = $group_c_costs[$method_group]['min'];

            $this->addConfiguration($method_group . '_START', $group_c_method_title, 6, 1, self::class . '::setFunction(');

                $this->addConfiguration($method_group . '_COSTS', $group_c_method_costs, 6, 1, self::class . '::setFunction(');
                $this->addConfiguration($method_group . '_KG', $group_c_method_kg, 6, 1, self::class . '::setFunction(');
                $this->addConfiguration($method_group . '_MIN', $group_c_method_min, 6, 1, self::class . '::setFunction(');

            $this->addConfiguration($method_group . '_END', '', 6, 1, self::class . '::setFunction(');
        }

        $this->addConfiguration(Group::SHIPPING_GROUP_C . '_END', '', 6, 1, self::class . '::setFunction(');
        /** */

        /**
         * Group D
         */
        $group_d_title     = $this->getConfig(Group::SHIPPING_GROUP_D . '_START_TITLE');
        $group_d_countries = 'AD, CH, GB, GG, JE, NO, SM';
        $group_d_costs     = Field::getShippingCountryGroupDCosts();

        $this->addConfiguration(Group::SHIPPING_GROUP_D . '_START', $group_d_title, 6, 1, self::class . '::setFunction(');

        $this->addConfiguration(Group::SHIPPING_GROUP_D . '_COUNTRIES', $group_d_countries, 6, 1);

        foreach (self::$methods[Group::SHIPPING_GROUP_D] as $method_name) {
            $method_group = Group::SHIPPING_GROUP_D . '_' . $method_name;

            $group_d_method_title = $this->getConfig('SHIPPING_METHOD_' . $method_name);
            $group_d_method_costs = $group_d_costs[$method_group]['costs'];
            $group_d_method_kg    = $group_d_costs[$method_group]['kg'];
            $group_d_method_min   = $group_d_costs[$method_group]['min'];

            $this->addConfiguration($method_group . '_START', $group_d_method_title, 6, 1, self::class . '::setFunction(');

                $this->addConfiguration($method_group . '_COSTS', $group_d_method_costs, 6, 1, self::class . '::setFunction(');
                $this->addConfiguration($method_group . '_KG', $group_d_method_kg, 6, 1, self::class . '::setFunction(');
                $this->addConfiguration($method_group . '_MIN', $group_d_method_min, 6, 1, self::class . '::setFunction(');

            $this->addConfiguration($method_group . '_END', '', 6, 1, self::class . '::setFunction(');
        }

        $this->addConfiguration(Group::SHIPPING_GROUP_D . '_END', '', 6, 1, self::class . '::setFunction(');
        /** */

        /**
         * Group E
         */
        $group_e_title     = $this->getConfig(Group::SHIPPING_GROUP_E . '_START_TITLE');
        $group_e_countries = 'AE, BA, CA, CN, HK, IN, JP, KV, MD, ME, MK, MX, RS, SA, SG, TR, TW, UA, US, VN';
        $group_e_costs     = Field::getShippingCountryGroupECosts();

        $this->addConfiguration(Group::SHIPPING_GROUP_E . '_START', $group_e_title, 6, 1, self::class . '::setFunction(');

        $this->addConfiguration(Group::SHIPPING_GROUP_E . '_COUNTRIES', $group_e_countries, 6, 1);

        foreach (self::$methods[Group::SHIPPING_GROUP_E] as $method_name) {
            $method_group = Group::SHIPPING_GROUP_E . '_' . $method_name;

            $group_e_method_title = $this->getConfig('SHIPPING_METHOD_' . $method_name);
            $group_e_method_costs = $group_e_costs[$method_group]['costs'];
            $group_e_method_kg    = $group_e_costs[$method_group]['kg'];
            $group_e_method_min   = $group_e_costs[$method_group]['min'];

            $this->addConfiguration($method_group . '_START', $group_e_method_title, 6, 1, self::class . '::setFunction(');

                $this->addConfiguration($method_group . '_COSTS', $group_e_method_costs, 6, 1, self::class . '::setFunction(');
                $this->addConfiguration($method_group . '_KG', $group_e_method_kg, 6, 1, self::class . '::setFunction(');
                $this->addConfiguration($method_group . '_MIN', $group_e_method_min, 6, 1, self::class . '::setFunction(');

            $this->addConfiguration($method_group . '_END', '', 6, 1, self::class . '::setFunction(');
        }

        $this->addConfiguration(Group::SHIPPING_GROUP_E . '_END', '', 6, 1, self::class . '::setFunction(');
        /** */

        /**
         * Group F
         */
        $group_f_title = $this->getConfig(Group::SHIPPING_GROUP_F . '_START_TITLE');
        $group_f_costs = Field::getShippingCountryGroupFCosts();

        $this->addConfiguration(Group::SHIPPING_GROUP_F . '_START', $group_f_title, 6, 1, self::class . '::setFunction(');

        $this->addConfiguration(Group::SHIPPING_GROUP_F . '_COUNTRIES', '', 6, 1);

        foreach (self::$methods[Group::SHIPPING_GROUP_F] as $method_name) {
            $method_group = Group::SHIPPING_GROUP_F . '_' . $method_name;

            $group_f_method_title = $this->getConfig('SHIPPING_METHOD_' . $method_name);
            $group_f_method_costs = $group_f_costs[$method_group]['costs'];
            $group_f_method_kg    = $group_f_costs[$method_group]['kg'];
            $group_f_method_min   = $group_f_costs[$method_group]['min'];

            $this->addConfiguration($method_group . '_START', $group_f_method_title, 6, 1, self::class . '::setFunction(');

                $this->addConfiguration($method_group . '_COSTS', $group_f_method_costs, 6, 1, self::class . '::setFunction(');
                $this->addConfiguration($method_group . '_KG', $group_f_method_kg, 6, 1, self::class . '::setFunction(');
                $this->addConfiguration($method_group . '_MIN', $group_f_method_min, 6, 1, self::class . '::setFunction(');

            $this->addConfiguration($method_group . '_END', '', 6, 1, self::class . '::setFunction(');
        }

        $this->addConfiguration(Group::SHIPPING_GROUP_F . '_END', '', 6, 1, self::class . '::setFunction(');
        /** */

        /**
         * Surcharges
         */
        $this->addConfiguration(Group::SURCHARGES . '_START', $this->getConfig(Group::SURCHARGES . '_START_TITLE'), 6, 1, self::class . '::setFunction(');

        $surcharges = json_encode(
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

        $this->addConfiguration(Group::SURCHARGES . '_SURCHARGES', $surcharges, 6, 1, self::class . '::setFunction(');

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

        $this->addConfiguration(Group::SURCHARGES . '_PICK_AND_PACK', $pick_and_pack, 6, 1, self::class . '::setFunction(');

        $this->addConfigurationSelect(Group::SURCHARGES . '_ROUND_UP', 'true', 6, 1);
        $this->addConfiguration(Group::SURCHARGES . '_ROUND_UP_TO', 0.90, 6, 1, self::class . '::setFunction(');

        $this->addConfiguration(Group::SURCHARGES . '_END', '', 6, 1, self::class . '::setFunction(');
        /** */
    }

    protected function updateSteps()
    {
        if (-1 === version_compare($this->getVersion(), self::VERSION)) {
            $this->setVersion(self::VERSION);

            return self::UPDATE_SUCCESS;
        }

        return self::UPDATE_NOTHING;
    }

    public function remove()
    {
        parent::remove();

        /**
         * Required for modified compatibility
         */
        $this->removeConfiguration('ALLOWED');
        /** */

        /**
         * Sort Order
         */
        $this->removeConfiguration('SORT_ORDER');

        /**
         * Debug
         */
        $this->removeConfiguration('DEBUG_ENABLE');
        /** */

        /**
         * Weight
         */
        $this->removeConfiguration(Group::SHIPPING_WEIGHT . '_START');
            $this->removeConfiguration(Group::SHIPPING_WEIGHT . '_MAX');
            $this->removeConfiguration(Group::SHIPPING_WEIGHT . '_IDEAL');
        $this->removeConfiguration(Group::SHIPPING_WEIGHT . '_END');
        /** */

        /**
         * Methods
         */
        $this->removeConfiguration(Group::SHIPPING_METHODS . '_START');

        $this->removeConfiguration(Group::SHIPPING_METHODS . '_STANDARD');
        $this->removeConfiguration(Group::SHIPPING_METHODS . '_SAVER');
        $this->removeConfiguration(Group::SHIPPING_METHODS . '_1200');
        $this->removeConfiguration(Group::SHIPPING_METHODS . '_EXPRESS');
        $this->removeConfiguration(Group::SHIPPING_METHODS . '_PLUS');
        $this->removeConfiguration(Group::SHIPPING_METHODS . '_EXPEDITED');

        $this->removeConfiguration(Group::SHIPPING_METHODS . '_END');
        /** */

        /**
         * National
         */
        $this->removeConfiguration(Group::SHIPPING_NATIONAL . '_START');

        $this->removeConfiguration(Group::SHIPPING_NATIONAL . '_COUNTRY');

        foreach (self::$methods[Group::SHIPPING_NATIONAL] as $method) {
            $method = Group::SHIPPING_NATIONAL . '_' . $method;

            $this->removeConfiguration($method . '_START');

                $this->removeConfiguration($method . '_COSTS');
                $this->removeConfiguration($method . '_KG');
                $this->removeConfiguration($method . '_MIN');

            $this->removeConfiguration($method . '_END');
        }

        $this->removeConfiguration(Group::SHIPPING_NATIONAL . '_END');
        /** */

        /**
         * Groups
         */
        foreach (self::$methods_international as $group) {
            $this->removeConfiguration($group . '_START');

            if (Group::SHIPPING_GROUP_F !== $group) {
                $this->addKey($group . '_COUNTRIES');
            }

            foreach (self::$methods[$group] as $method_name) {
                $method_group = $group . '_' . $method_name;

                $this->removeConfiguration($method_group . '_START');

                    $this->removeConfiguration($method_group . '_COSTS');
                    $this->removeConfiguration($method_group . '_KG');
                    $this->removeConfiguration($method_group . '_MIN');

                $this->removeConfiguration($method_group . '_END');
            }

            $this->removeConfiguration($group . '_END');
        }
        /** */

        /**
         * Surcharges
         */
        $this->removeConfiguration(Group::SURCHARGES . '_START');

            $this->removeConfiguration(Group::SURCHARGES . '_SURCHARGES');

            $this->removeConfiguration(Group::SURCHARGES . '_PICK_AND_PACK');

            $this->removeConfiguration(Group::SURCHARGES . '_ROUND_UP');
            $this->removeConfiguration(Group::SURCHARGES . '_ROUND_UP_TO');

        $this->deleteConfiguration(Group::SURCHARGES . '_END');
        /** */
    }

    /**
     * Used by modified to show shipping costs. Will be ignored if the value is
     * not an array.
     *
     * @param string $method_id The selected method during checkout. Only return
     *                          this method when it is set.
     *
     * @return array|null
     */
    public function quote(string $method_id = ''): ?array
    {
        $quote  = new Quote(Constants::MODULE_SHIPPING_NAME);
        $quotes = $quote->getQuote($method_id);

        if (is_array($quotes) && !$quote->exceedsMaximumWeight()) {
            $this->quotes = $quotes;
        }

        return $quotes;
    }
}
