<?php

namespace Grandeljay\Ups\Configuration;

use Grandeljay\Ups\Constants;
use RobinTheHood\ModifiedStdModule\Classes\CaseConverter;

class Group
{
    public static function start(string $value, string $option): string
    {
        $key_without_module_name = substr($option, strlen(Constants::MODULE_SHIPPING_NAME) + 1);
        $key_lisp                = CaseConverter::screamingToLisp($key_without_module_name);
        $classes                 = [
            $key_lisp,
        ];

        if (isset($_GET['factor']) && ('shipping-group-' === \substr($key_lisp, 0, 15) || 'bulk-price-start' === $key_lisp)) {
            $classes[] = 'factor-active';
        }

        $class = \implode(' ', $classes);

        ob_start();
        ?>
        <details class="<?= $class ?>">
            <summary><?= $value ?></summary>
            <div>
        <?php
        return ob_get_clean();
    }

    public static function end(string $value, string $option): string
    {
        ob_start();
        ?>
            </div>
        </details>
        <?php
        return ob_get_clean();
    }

    /**
     * Weight
     */
    public const SHIPPING_WEIGHT = 'SHIPPING_WEIGHT';

    public static function shippingWeightStart(string $value, string $option): string
    {
        $title = \constant($option . '_TITLE');

        return self::start('<h2>' . $title . '</h2>', $option);
    }

    public static function shippingWeightEnd(string $value, string $option): string
    {
        return self::end($value, $option);
    }

    /**
     * Methods
     */
    public const SHIPPING_METHODS = 'SHIPPING_METHODS';

    public static function shippingMethodsStart(string $value, string $option): string
    {
        $title = \constant($option . '_TITLE');

        return self::start('<h2>' . $title . '</h2>', $option);
    }

    public static function shippingMethodsEnd(string $value, string $option): string
    {
        return self::end($value, $option);
    }

    /**
     * Shipping
     */
    public const SHIPPING_NATIONAL = 'SHIPPING_NATIONAL';
    public const SHIPPING_GROUP_A  = 'SHIPPING_GROUP_A';
    public const SHIPPING_GROUP_B  = 'SHIPPING_GROUP_B';
    public const SHIPPING_GROUP_C  = 'SHIPPING_GROUP_C';
    public const SHIPPING_GROUP_D  = 'SHIPPING_GROUP_D';
    public const SHIPPING_GROUP_E  = 'SHIPPING_GROUP_E';
    public const SHIPPING_GROUP_F  = 'SHIPPING_GROUP_F';

    public static function shippingStart(string $value, string $option): string
    {
        $title = \constant($option . '_TITLE');

        return self::start('<h2>' . $title . '</h2>', $option);
    }

    public static function shippingEnd(string $value, string $option): string
    {
        return self::end($value, $option);
    }

    /** Standard */
    public static function shippingStandardStart(string $value, string $option): string
    {
        $title = \constant(Constants::MODULE_SHIPPING_NAME . '_SHIPPING_METHOD_STANDARD');

        return self::start('<h3>' . $title . '</h3>', $option);
    }

    public static function shippingStandardEnd(string $value, string $option): string
    {
        return self::end($value, $option);
    }

    /** Expedited */
    public static function shippingExpeditedStart(string $value, string $option): string
    {
        $title = \constant(Constants::MODULE_SHIPPING_NAME . '_SHIPPING_METHOD_EXPEDITED');

        return self::start('<h3>' . $title . '</h3>', $option);
    }

    public static function shippingExpeditedEnd(string $value, string $option): string
    {
        return self::end($value, $option);
    }

    /** Saver */
    public static function shippingSaverStart(string $value, string $option): string
    {
        $title = \constant(Constants::MODULE_SHIPPING_NAME . '_SHIPPING_METHOD_SAVER');

        return self::start('<h3>' . $title . '</h3>', $option);
    }

    public static function shippingSaverEnd(string $value, string $option): string
    {
        return self::end($value, $option);
    }

    /** 12:00 */
    public static function shipping1200Start(string $value, string $option): string
    {
        $title = \constant(Constants::MODULE_SHIPPING_NAME . '_SHIPPING_METHOD_1200');

        return self::start('<h3>' . $title . '</h3>', $option);
    }

    public static function shipping1200End(string $value, string $option): string
    {
        return self::end($value, $option);
    }

    /** Express */
    public static function shippingExpressStart(string $value, string $option): string
    {
        $title = \constant(Constants::MODULE_SHIPPING_NAME . '_SHIPPING_METHOD_EXPRESS');

        return self::start('<h3>' . $title . '</h3>', $option);
    }

    public static function shippingExpressEnd(string $value, string $option): string
    {
        return self::end($value, $option);
    }

    /** Plus */
    public static function shippingPlusStart(string $value, string $option): string
    {
        $title = \constant(Constants::MODULE_SHIPPING_NAME . '_SHIPPING_METHOD_PLUS');

        return self::start('<h3>' . $title . '</h3>', $option);
    }

    public static function shippingPlusEnd(string $value, string $option): string
    {
        return self::end($value, $option);
    }

    /**
     * Surcharges
     */
    public const SURCHARGES = 'SURCHARGES';

    public static function surchargesStart(string $value, string $option): string
    {
        $title = \constant($option . '_TITLE');

        return self::start('<h2>' . $title . '</h2>', $option);
    }

    public static function surchargesEnd(string $value, string $option): string
    {
        return self::end($value, $option);
    }

    /**
     * Bulk Price Change Preview
     */
    public const BULK_PRICE = 'BULK_PRICE';

    public static function bulkPriceStart(string $value, string $option): string
    {
        $title = \constant($option . '_TITLE');

        return self::start('<h2>' . $title . '</h2>', $option);
    }

    public static function bulkPriceEnd(string $value, string $option): string
    {
        return self::end($value, $option);
    }
}
