<?php

namespace Grandeljay\Ups;

use Grandeljay\Ups\Configuration\Group;

class ModuleUninstaller
{
    public function __construct(private object $parent)
    {
    }

    public function uninstall(): void
    {
        $this->parent->stdRemoveConfiguration('ALLOWED');
        $this->parent->stdRemoveConfiguration('SORT_ORDER');
        $this->parent->stdRemoveConfiguration('DEBUG_ENABLE');

        $this->uninstallWeight();
        $this->uninstallMethods();
        $this->uninstallShippingNational();
        $this->uninstallShippingGroups();
        $this->uninstallSurcharges();
        $this->uninstallBulkPriceChangePreview();
    }

    private function uninstallWeight(): void
    {
        $prefix = Group::SHIPPING_WEIGHT . '_';

        $this->parent->stdRemoveConfiguration($prefix . 'START');
        $this->parent->stdRemoveConfiguration($prefix . 'MAX');
        $this->parent->stdRemoveConfiguration($prefix . 'IDEAL');
        $this->parent->stdRemoveConfiguration($prefix . 'END');
    }

    private function uninstallMethods(): void
    {
        $prefix = Group::SHIPPING_METHODS . '_';

        $this->parent->stdRemoveConfiguration($prefix . 'START');
        $this->parent->stdRemoveConfiguration($prefix . 'STANDARD');
        $this->parent->stdRemoveConfiguration($prefix . 'SAVER');
        $this->parent->stdRemoveConfiguration($prefix . '1200');
        $this->parent->stdRemoveConfiguration($prefix . 'EXPRESS');
        $this->parent->stdRemoveConfiguration($prefix . 'PLUS');
        $this->parent->stdRemoveConfiguration($prefix . 'EXPEDITED');
        $this->parent->stdRemoveConfiguration($prefix . 'END');
    }

    private function uninstallShippingNational(): void
    {
        $prefix = Group::SHIPPING_NATIONAL . '_';

        $this->parent->stdRemoveConfiguration($prefix . 'START');
        $this->parent->stdRemoveConfiguration($prefix . 'COUNTRY');

        foreach (\grandeljayups::$methods[Group::SHIPPING_NATIONAL] as $method) {
            $method = $prefix . $method;

            $this->parent->stdRemoveConfiguration($method . '_START');
            $this->parent->stdRemoveConfiguration($method . '_COSTS');
            $this->parent->stdRemoveConfiguration($method . '_KG');
            $this->parent->stdRemoveConfiguration($method . '_MIN');
            $this->parent->stdRemoveConfiguration($method . '_EXCLUDED');
            $this->parent->stdRemoveConfiguration($method . '_END');
        }

        $this->parent->stdRemoveConfiguration($prefix . 'END');
    }

    private function uninstallShippingGroups(): void
    {
        foreach (\grandeljayups::$methods_international as $group) {
            $this->parent->stdRemoveConfiguration($group . '_START');
            $this->parent->stdRemoveConfiguration($group . '_COUNTRIES');

            foreach (\grandeljayups::$methods[$group] as $method_name) {
                $method_group = $group . '_' . $method_name;

                $this->parent->stdRemoveConfiguration($method_group . '_START');
                $this->parent->stdRemoveConfiguration($method_group . '_COSTS');
                $this->parent->stdRemoveConfiguration($method_group . '_KG');
                $this->parent->stdRemoveConfiguration($method_group . '_MIN');
                $this->parent->stdRemoveConfiguration($method_group . '_EXCLUDED');
                $this->parent->stdRemoveConfiguration($method_group . '_END');
            }

            $this->parent->stdRemoveConfiguration($group . '_END');
        }
    }

    private function uninstallSurcharges(): void
    {
        $prefix = Group::SURCHARGES . '_';

        $this->parent->stdRemoveConfiguration($prefix . 'START');
        $this->parent->stdRemoveConfiguration($prefix . 'SURCHARGES');
        $this->parent->stdRemoveConfiguration($prefix . 'PICK_AND_PACK');
        $this->parent->stdRemoveConfiguration($prefix . 'ROUND_UP');
        $this->parent->stdRemoveConfiguration($prefix . 'ROUND_UP_TO');
        $this->parent->stdRemoveConfiguration($prefix . 'END');
    }

    private function uninstallBulkPriceChangePreview(): void
    {
        $prefix = Group::BULK_PRICE . '_';

        $this->parent->stdRemoveConfiguration($prefix . 'START');
        $this->parent->stdRemoveConfiguration($prefix . 'FACTOR');
        $this->parent->stdRemoveConfiguration($prefix . 'END');
    }
}
