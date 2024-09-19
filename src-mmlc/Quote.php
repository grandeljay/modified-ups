<?php

namespace Grandeljay\Ups;

use Grandeljay\Ups\Configuration\Configuration;
use Grandeljay\Ups\Configuration\Group;

class Quote
{
    private array $methods = [];

    public function __construct(string $module)
    {
        global $order;

        if (!isset($order)) {
            return;
        }

        $this->methods = $this->getShippingMethods();
    }

    private function getShippingMethods(): array
    {
        $methods = [];
        $method  = new Method();

        if ($method->isNational()) {
            $national = new National();
            $national->calculateWeight();

            $methods = $national->getMethods();
        }

        if ($method->isInternational()) {
            $international = new International();
            $international->calculateWeight();

            $methods = $international->getMethods();
        }

        return $methods;
    }

    private function getForMethod(string $method_id): array
    {
        $methods = [];

        foreach ($this->methods as $method) {
            if ($method_id === $method['id']) {
                $methods[] = $method;
            }
        }

        return $methods;
    }

    public function getQuote(string $method_id): ?array
    {
        if (empty($this->methods)) {
            return null;
        }

        $methods = $this->methods;

        if ('' !== $method_id) {
            $methods = $this->getForMethod($method_id);
        }

        if (\class_exists('Grandeljay\ShippingConditions\Surcharges')) {
            $surcharges = new \Grandeljay\ShippingConditions\Surcharges(
                \grandeljayups::class,
                $methods
            );
            $surcharges->setSurcharges();

            $methods = $surcharges->getMethods();
        }

        $quote = [
            'id'      => \grandeljayups::class,
            'module'  => 'UPS',
            'methods' => $methods,
        ];

        return $quote;
    }
}
