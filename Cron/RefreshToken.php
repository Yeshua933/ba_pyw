<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Cron;

use PayYourWay\Pyw\Api\GenerateAccessTokenInterface;
use PayYourWay\Pyw\Model\Config;

class RefreshToken
{
    private Config $config;
    private GenerateAccessTokenInterface $generateAccessToken;

    public function __construct(
        Config $config,
        GenerateAccessTokenInterface $generateAccessToken
    ) {
        $this->config = $config;
        $this->generateAccessToken = $generateAccessToken;
    }

    /**
     * Refresh and generate a token.
     */
    public function execute()
    {
        if ($this->config->isPayYourWayEnabled() && $this->config->isRefreshTokenProcessEnabled()) {
            $accessToken = $this->getAccessToken();
        }
    }

    /**
     * This is a work in progress
     */
    private function getAccessToken(): ?string
    {
        return $this->generateAccessToken->execute();
    }
}
