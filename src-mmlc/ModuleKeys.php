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
        $this->parent->addkey('SORT_ORDER');
        $this->parent->addkey('DEBUG_ENABLE');

        $this->addKeysWeight();
        $this->addKeysMethods();
        $this->addKeysShippingNational();
        $this->addKeysShippingGroups();
        $this->addKeysSurcharges();
    }

    private function addKeysWeight(): void
    {
        $prefix = Group::SHIPPING_WEIGHT . '_';

        $this->parent->addkey($prefix . 'START');
        $this->parent->addkey($prefix . 'MAX');
        $this->parent->addkey($prefix . 'IDEAL');
        $this->parent->addkey($prefix . 'END');
    }

    private function addKeysMethods(): void
    {
        $prefix = Group::SHIPPING_METHODS . '_';

        $this->parent->addkey($prefix . 'START');
        $this->parent->addkey($prefix . 'STANDARD');
        $this->parent->addkey($prefix . 'SAVER');
        $this->parent->addkey($prefix . '1200');
        $this->parent->addkey($prefix . 'EXPRESS');
        $this->parent->addkey($prefix . 'PLUS');
        $this->parent->addkey($prefix . 'EXPEDITED');
        $this->parent->addkey($prefix . 'END');
    }

    private function addKeysShippingNational(): void
    {
        $prefix = Group::SHIPPING_NATIONAL . '_';

        $this->parent->addkey($prefix . 'START');
        $this->parent->addkey($prefix . 'COUNTRY');

        foreach (\grandeljayups::$methods[Group::SHIPPING_NATIONAL] as $method) {
            $method = $prefix . $method;

            $this->parent->addkey($method . '_START');
            $this->parent->addkey($method . '_COSTS');
            $this->parent->addkey($method . '_KG');
            $this->parent->addkey($method . '_MIN');
            $this->parent->addkey($method . '_END');
        }

        $this->parent->addKey($prefix . 'END');
    }

    private function addKeysShippingGroups(): void
    {
        foreach (\grandeljayups::$methods_international as $group) {
            $this->parent->addKey($group . '_START');

            if (Group::SHIPPING_GROUP_F !== $group) {
                $this->parent->addKey($group . '_COUNTRIES');
            }

            foreach (\grandeljayups::$methods[$group] as $method_name) {
                $method_group = $group . '_' . $method_name;

                $this->parent->addKey($method_group . '_START');
                $this->parent->addKey($method_group . '_COSTS');
                $this->parent->addKey($method_group . '_KG');
                $this->parent->addKey($method_group . '_MIN');
                $this->parent->addKey($method_group . '_END');
            }

            $this->parent->addKey($group . '_END');
        }
    }

    private function addKeysSurcharges(): void
    {
        $prefix = Group::SURCHARGES . '_';

        $this->parent->addKey($prefix . 'START');
        $this->parent->addKey($prefix . 'SURCHARGES');
        $this->parent->addKey($prefix . 'PICK_AND_PACK');
        $this->parent->addKey($prefix . 'ROUND_UP');
        $this->parent->addKey($prefix . 'ROUND_UP_TO');
        $this->parent->addKey($prefix . 'END');
    }
}
