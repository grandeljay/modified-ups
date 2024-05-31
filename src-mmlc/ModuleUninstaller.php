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
        $this->parent->removeConfiguration('ALLOWED');
        $this->parent->removeConfiguration('SORT_ORDER');
        $this->parent->removeConfiguration('DEBUG_ENABLE');

        $this->uninstallWeight();
        $this->uninstallMethods();
        $this->uninstallShippingNational();
        $this->uninstallShippingGroups();
        $this->uninstallSurcharges();
    }

    private function uninstallWeight(): void
    {
        $prefix = Group::SHIPPING_WEIGHT . '_';

        $this->parent->removeConfiguration($prefix . 'START');
        $this->parent->removeConfiguration($prefix . 'MAX');
        $this->parent->removeConfiguration($prefix . 'IDEAL');
        $this->parent->removeConfiguration($prefix . 'END');
    }

    private function uninstallMethods(): void
    {
        $prefix = Group::SHIPPING_METHODS . '_';

        $this->parent->removeConfiguration($prefix . 'START');
        $this->parent->removeConfiguration($prefix . 'STANDARD');
        $this->parent->removeConfiguration($prefix . 'SAVER');
        $this->parent->removeConfiguration($prefix . '1200');
        $this->parent->removeConfiguration($prefix . 'EXPRESS');
        $this->parent->removeConfiguration($prefix . 'PLUS');
        $this->parent->removeConfiguration($prefix . 'EXPEDITED');
        $this->parent->removeConfiguration($prefix . 'END');
    }

    private function uninstallShippingNational(): void
    {
        $prefix = Group::SHIPPING_NATIONAL . '_';

        $this->parent->removeConfiguration($prefix . 'START');
        $this->parent->removeConfiguration($prefix . 'COUNTRY');

        foreach (\grandeljayups::$methods[Group::SHIPPING_NATIONAL] as $method) {
            $method = $prefix . $method;

            $this->parent->removeConfiguration($method . '_START');
            $this->parent->removeConfiguration($method . '_COSTS');
            $this->parent->removeConfiguration($method . '_KG');
            $this->parent->removeConfiguration($method . '_MIN');
            $this->parent->removeConfiguration($method . '_END');
        }

        $this->parent->removeConfiguration($prefix . 'END');
    }

    private function uninstallShippingGroups(): void
    {
        foreach (\grandeljayups::$methods_international as $group) {
            $this->parent->removeConfiguration($group . '_START');

            if (Group::SHIPPING_GROUP_F !== $group) {
                $this->parent->removeConfiguration($group . '_COUNTRIES');
            }

            foreach (\grandeljayups::$methods[$group] as $method_name) {
                $method_group = $group . '_' . $method_name;

                $this->parent->removeConfiguration($method_group . '_START');
                $this->parent->removeConfiguration($method_group . '_COSTS');
                $this->parent->removeConfiguration($method_group . '_KG');
                $this->parent->removeConfiguration($method_group . '_MIN');
                $this->parent->removeConfiguration($method_group . '_END');
            }

            $this->parent->removeConfiguration($group . '_END');
        }
    }

    private function uninstallSurcharges(): void
    {
        $prefix = Group::SURCHARGES . '_';

        $this->parent->removeConfiguration($prefix . 'START');
        $this->parent->removeConfiguration($prefix . 'SURCHARGES');
        $this->parent->removeConfiguration($prefix . 'PICK_AND_PACK');
        $this->parent->removeConfiguration($prefix . 'ROUND_UP');
        $this->parent->removeConfiguration($prefix . 'ROUND_UP_TO');
        $this->parent->removeConfiguration($prefix . 'END');
    }
}
