<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Api;

use PayYourWay\Pyw\Model\AccessToken;

/**
 * Used for creating/renewing the Pay Your Way Access Token
 *
 * @api
 */
interface GenerateAccessTokenInterface
{
    /**
     * Generate the OAUTH token for PYW server passing the parameters
     *
     * @return AccessToken
     */
    public function execute(
        string $clientId,
        string $privateKey
    ): ?string;
}
