<?php

namespace Grandeljay\Ups;

use Grandeljay\Ups\Configuration\Group;

class ModuleKeys
{
    public function __construct(private object $parent)
    {
    }

    public function addKeys(): void
    {
        $this->parent->stdAddKey('SORT_ORDER');
        $this->parent->stdAddKey('DEBUG_ENABLE');

        $this->addKeysWeight();
        $this->addKeysMethods();
        $this->addKeysShippingNational();
        $this->addKeysShippingGroups();
        $this->addKeysSurcharges();
        $this->addKeysBulkPriceChangePreview();
    }

    private function addKeysWeight(): void
    {
        $prefix = Group::SHIPPING_WEIGHT . '_';

        $this->parent->stdAddKey($prefix . 'START');
        $this->parent->stdAddKey($prefix . 'MAX');
        $this->parent->stdAddKey($prefix . 'IDEAL');
        $this->parent->stdAddKey($prefix . 'END');
    }

    private function addKeysMethods(): void
    {
        $prefix = Group::SHIPPING_METHODS . '_';

        $this->parent->stdAddKey($prefix . 'START');
        $this->parent->stdAddKey($prefix . 'STANDARD');
        $this->parent->stdAddKey($prefix . 'SAVER');
        $this->parent->stdAddKey($prefix . '1200');
        $this->parent->stdAddKey($prefix . 'EXPRESS');
        $this->parent->stdAddKey($prefix . 'PLUS');
        $this->parent->stdAddKey($prefix . 'EXPEDITED');
        $this->parent->stdAddKey($prefix . 'END');
    }

    private function addKeysShippingNational(): void
    {
        $prefix = Group::SHIPPING_NATIONAL . '_';

        $this->parent->stdAddKey($prefix . 'START');
        $this->parent->stdAddKey($prefix . 'COUNTRY');

        foreach (\grandeljayups::$methods[Group::SHIPPING_NATIONAL] as $method) {
            $method = $prefix . $method;

            $this->parent->stdAddKey($method . '_START');
            $this->parent->stdAddKey($method . '_COSTS');
            $this->parent->stdAddKey($method . '_KG');
            $this->parent->stdAddKey($method . '_MIN');
            $this->parent->stdAddKey($method . '_END');
        }

        $this->parent->stdAddKey($prefix . 'END');
    }

    private function addKeysShippingGroups(): void
    {
        foreach (\grandeljayups::$methods_international as $group) {
            $this->parent->stdAddKey($group . '_START');

            if (Group::SHIPPING_GROUP_F !== $group) {
                $this->parent->stdAddKey($group . '_COUNTRIES');
            }

            foreach (\grandeljayups::$methods[$group] as $method_name) {
                $method_group = $group . '_' . $method_name;

                $this->parent->stdAddKey($method_group . '_START');
                $this->parent->stdAddKey($method_group . '_COSTS');
                $this->parent->stdAddKey($method_group . '_KG');
                $this->parent->stdAddKey($method_group . '_MIN');
                $this->parent->stdAddKey($method_group . '_END');
            }

            $this->parent->stdAddKey($group . '_END');
        }
    }

    private function addKeysSurcharges(): void
    {
        $prefix = Group::SURCHARGES . '_';

        $this->parent->stdAddKey($prefix . 'START');
        $this->parent->stdAddKey($prefix . 'SURCHARGES');
        $this->parent->stdAddKey($prefix . 'PICK_AND_PACK');
        $this->parent->stdAddKey($prefix . 'ROUND_UP');
        $this->parent->stdAddKey($prefix . 'ROUND_UP_TO');
        $this->parent->stdAddKey($prefix . 'END');
    }

    private function addKeysBulkPriceChangePreview(): void
    {
        $prefix = Group::BULK_PRICE . '_';

        $this->parent->stdAddKey($prefix . 'START');
        $this->parent->stdAddKey($prefix . 'FACTOR');
        $this->parent->stdAddKey($prefix . 'END');
    }
}
