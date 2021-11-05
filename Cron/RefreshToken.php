<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Cron;

use PayYourWay\Pyw\Model\Config;

class RefreshToken
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Refresh and generate a token
     */
    public function execute()
    {
        if ($this->config->isPayYourWayEnabled() && $this->config->isRefreshTokenProcessEnabled())
        {
            /**
             * @todo: Implement the refresh and generate logic
             */
        }
    }
}
