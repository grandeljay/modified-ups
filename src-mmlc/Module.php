<?php

namespace Grandeljay\Ups;

class Module
{
    public function __construct(private object $parent)
    {
    }

    public function addKeys(): void
    {
        $keys = new ModuleKeys($this->parent);
        $keys->addKeys();
    }

    public function install(): void
    {
        $installer = new ModuleInstaller($this->parent);
        $installer->install();
    }
}
