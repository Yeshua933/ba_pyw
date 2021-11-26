<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Api;

/**
 * Update Merchant Service
 * This message initiates a single Merchant Service lookup.
 * Please see {@see UpdateMerchantRequestInterface} for more information on the fields available.
 *
 * @api
 */
interface UpdateMerchantLookupInterface
{
    /**
     * Updates the merchant
     *
     * @param UpdateMerchantRequestInterface $request
     * @return string
     */
    public function execute(
        UpdateMerchantRequestInterface $request
    ): string;
}
