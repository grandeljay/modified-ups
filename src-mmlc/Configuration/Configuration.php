<?php

namespace Grandeljay\Ups\Configuration;

use Grandeljay\Ups\Constants;

class Configuration
{
    public static function get(string $key, string $default = 'Unknown'): string
    {
        $constant_key   = Constants::MODULE_SHIPPING_NAME . '_' . $key;
        $constant_value = $default;

        try {
            $constant_value = \constant($constant_key);
        } catch (\Throwable $throwable) {
            global $LogLevel;

            $logger = new \LoggingManager(
                DIR_FS_LOG . \grandeljayups::class . '_%1$s_%2$s.log',
                \grandeljayups::class,
                \strtolower($LogLevel)
            );
            $logger->log(
                \get_class($throwable),
                $throwable->__toString()
            );
        }

        return $constant_value;
    }
}
