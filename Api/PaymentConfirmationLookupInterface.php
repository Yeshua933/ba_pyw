<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Api;

/**
 * Payment Confirmation Lookup Service
 * This message initiates a single Payment Confirmation lookup.
 * Please see {@see RequestInterface} for more information on the fields available.
 *
 * @api
 */
interface PaymentConfirmationLookupInterface
{
    /**
     * Look up the payment confirmation for a request
     *
     * @param RequestInterface $request
     * @return string
     */
    public function lookup(
        RequestInterface $request
    ): string;
}
