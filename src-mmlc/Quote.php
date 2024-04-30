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

    public function exceedsMaximumWeight(): bool
    {
        global $order;

        if (null === $order) {
            return true;
        }

        $shipping_weight_max = Configuration::get(Group::SHIPPING_WEIGHT . '_MAX');

        foreach ($order->products as $product) {
            if ($product['weight'] >= $shipping_weight_max) {
                return true;
            }
        }

        return false;
    }

    private function getShippingMethods(): array
    {
        $methods = [];
        $method  = new Method();

        if ($method->isNational()) {
            $national = new National();

            $methods = $national->getMethods();
        }

        if ($method->isInternational()) {
            $international = new International();

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

    private function addDebugOutput(array &$methods): void
    {

        $debug_is_enabled = Configuration::get('DEBUG_ENABLE');
        $user_is_admin    = isset($_SESSION['customers_status']['customers_status_id']) && 0 === (int) $_SESSION['customers_status']['customers_status_id'];

        if ('true' === $debug_is_enabled && $user_is_admin) {
            foreach ($methods as &$method) {
                $total = 0;

                ob_start();
                ?>
                <br><br>

                <h3>Debug mode</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Costs</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($method['calculations'] as $calculation) { ?>
                            <?php $total += $calculation['costs']; ?>

                            <tr>
                                <td><?= $calculation['item'] ?></td>
                                <td><?= \sprintf('%01.2f', $calculation['costs']) ?></td>
                                <td><?= \sprintf('%01.2f', $total) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                $method['title'] .= ob_get_clean();
            }
        }
    }

    public function getQuote(string $method_id): ?array
    {
        if (empty($this->methods) || $this->exceedsMaximumWeight()) {
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

        /**
         * Output debug
         */
        $this->addDebugOutput($methods);

        $quote = [
            'id'      => \grandeljayups::class,
            'module'  => 'UPS',
            'methods' => $methods,
        ];

        return $quote;
    }
}
