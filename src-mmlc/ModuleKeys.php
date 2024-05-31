<?php

namespace Grandeljay\Ups;

use Grandeljay\Ups\Configuration\Group;

class ModuleKeys
{
    public function __construct(private object $parent)
    {
    }

    private function addKey(string $key): void
    {
        $this->parent->addKey($key);
    }

    public function addKeys(): void
    {
        $this->addkey('SORT_ORDER');
        $this->addkey('DEBUG_ENABLE');

        $this->addKeysWeight();
        $this->addKeysMethods();
        $this->addKeysShippingNational();
        $this->addKeysShippingGroups();
        $this->addKeysSurcharges();
    }

    private function addKeysWeight(): void
    {
        $this->addkey(Group::SHIPPING_WEIGHT . '_START');
        $this->addkey(Group::SHIPPING_WEIGHT . '_MAX');
        $this->addkey(Group::SHIPPING_WEIGHT . '_IDEAL');
        $this->addkey(Group::SHIPPING_WEIGHT . '_END');
    }

    private function addKeysMethods(): void
    {
        $this->addkey(Group::SHIPPING_METHODS . '_START');
        $this->addkey(Group::SHIPPING_METHODS . '_STANDARD');
        $this->addkey(Group::SHIPPING_METHODS . '_SAVER');
        $this->addkey(Group::SHIPPING_METHODS . '_1200');
        $this->addkey(Group::SHIPPING_METHODS . '_EXPRESS');
        $this->addkey(Group::SHIPPING_METHODS . '_PLUS');
        $this->addkey(Group::SHIPPING_METHODS . '_EXPEDITED');
        $this->addkey(Group::SHIPPING_METHODS . '_END');
    }

    private function addKeysShippingNational(): void
    {
        $this->addkey(Group::SHIPPING_NATIONAL . '_START');
        $this->addkey(Group::SHIPPING_NATIONAL . '_COUNTRY');

        foreach (\grandeljayups::$methods[Group::SHIPPING_NATIONAL] as $method) {
            $method = Group::SHIPPING_NATIONAL . '_' . $method;

            $this->addkey($method . '_START');
            $this->addkey($method . '_COSTS');
            $this->addkey($method . '_KG');
            $this->addkey($method . '_MIN');
            $this->addkey($method . '_END');
        }

        $this->addKey(Group::SHIPPING_NATIONAL . '_END');
    }

    private function addKeysShippingGroups(): void
    {
        foreach (\grandeljayups::$methods_international as $group) {
            $this->addKey($group . '_START');

            if (Group::SHIPPING_GROUP_F !== $group) {
                $this->addKey($group . '_COUNTRIES');
            }

            foreach (\grandeljayups::$methods[$group] as $method_name) {
                $method_group = $group . '_' . $method_name;

                $this->addKey($method_group . '_START');
                $this->addKey($method_group . '_COSTS');
                $this->addKey($method_group . '_KG');
                $this->addKey($method_group . '_MIN');
                $this->addKey($method_group . '_END');
            }

            $this->addKey($group . '_END');
        }
    }

    private function addKeysSurcharges(): void
    {
        $this->addKey(Group::SURCHARGES . '_START');
        $this->addKey(Group::SURCHARGES . '_SURCHARGES');
        $this->addKey(Group::SURCHARGES . '_PICK_AND_PACK');
        $this->addKey(Group::SURCHARGES . '_ROUND_UP');
        $this->addKey(Group::SURCHARGES . '_ROUND_UP_TO');
        $this->addKey(Group::SURCHARGES . '_END');
    }
}
