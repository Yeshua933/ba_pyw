<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */

namespace PayYourWay\Pyw\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class AddTitle implements DataPatchInterface, PatchVersionInterface
{

    private ModuleDataSetupInterface $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public static function getVersion(): string
    {
        return '1.0.1';
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $this->moduleDataSetup->getConnection()->insert(
            "core_config_data",
            ["scope" => "default",
                "scope_id" => 0,
                "path" => "payment/payyourway/title",
                "value" => "Pay with Shop Your Way"
            ]
        );
        $this->moduleDataSetup->endSetup();
    }

    public function getAliases(): array
    {
        return [];
    }
}
