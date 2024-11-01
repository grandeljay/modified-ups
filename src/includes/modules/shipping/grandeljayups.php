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

use Grandeljay\Ups\{Module, ModuleInstaller, Constants, Quote};
use Grandeljay\Ups\Configuration\Group;
use RobinTheHood\ModifiedStdModule\Classes\{StdModule, CaseConverter};

/**
 * Shipping methods musn't contain underscores.
 */
class grandeljayups extends StdModule
{
    public const VERSION = '0.12.1';

    private Module $module;

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

        /** Methods */
        if (!method_exists($namespace_configuration, $method_name)) {
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

        if (1 === preg_match('/^shipping(National|Group[A|B|C|D|E|F])[a-zA-Z0-9]+Excluded$/', $method_name)) {
            $method_name = 'shippingMethodExcluded';
        }
        /** */

        /** Exceptions */
        if (!method_exists($namespace_configuration, $method_name)) {
            if (is_numeric($value)) {
                $method_name = 'inputNumber';
            }
        }

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
        $this->module = new Module($this);
        $this->module->addKeys();
    }

    public function stdAddKey(string $key): void
    {
        $this->addKey($key);
    }

    public function stdAddConfiguration(
        string $key,
        string $value = '',
        int $group_id = 6,
        int $sort_order = 1,
        string $set_function = '',
        string $use_function = ''
    ): void {
        $this->addConfiguration(
            $key,
            $value,
            $group_id,
            $sort_order,
            $set_function,
            $use_function
        );
    }

    public function stdAddConfigurationSelect(
        string $key,
        string $value = '',
        int $group_id = 6,
        int $sort_order = 1
    ): void {
        $this->addConfiguration($key, $value, $group_id, $sort_order, 'select');
    }

    public function stdRemoveConfiguration(string $key): void
    {
        $this->removeConfiguration($key);
    }

    public function install()
    {
        parent::install();

        $this->module->install();
    }

    protected function updateSteps(): int
    {
        $version_before_update = $this->getVersion();
        $version_after_update  = self::VERSION;

        if (version_compare($version_before_update, '0.9.0', '<=')) {
            $installer = new ModuleInstaller($this);
            $installer->installBulkPriceChangePreview();
        }

        if (version_compare($version_before_update, $version_after_update, '<')) {
            $this->setVersion($version_after_update);

            return self::UPDATE_SUCCESS;
        }

        return self::UPDATE_NOTHING;
    }

    public function remove()
    {
        parent::remove();

        $this->module->uninstall();
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

        if (is_array($quotes)) {
            $this->quotes = $quotes;
        }

        return $quotes;
    }
}
