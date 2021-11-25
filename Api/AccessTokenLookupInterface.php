<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Api;

/**
 * Access Token Lookup Service
 * This message initiates a single Access Token lookup.
 * Please see {@see AccessTokenRequestInterface} for more information on the fields available.
 *
 * @api
 */
interface AccessTokenLookupInterface
{
    /**
     * Look up the access token for a request
     *
     * @param AccessTokenRequestInterface $request
     * @return string
     */
    public function execute(
        AccessTokenRequestInterface $request
    ): string;
}
