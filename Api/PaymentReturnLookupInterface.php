<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Api;

/**
 * Payment Return Lookup Service
 * This message initiates a single Payment Return lookup.
 * Please see {@see PaymentReturnRequestInterface} for more information on the fields available.
 *
 * @api
 */
interface PaymentReturnLookupInterface
{
    /**
     * Look up the payment return for a request
     *
     * @param PaymentReturnRequestInterface $request
     * @return string
     */
    public function lookup(
        PaymentReturnRequestInterface $request
    ): string;
}
