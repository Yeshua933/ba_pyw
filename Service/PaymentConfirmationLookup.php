<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Service;

use Magento\Store\Model\ScopeInterface;
use PayYourWay\Pyw\Api\PaymentConfirmationLookupInterface;
use PayYourWay\Pyw\Api\RequestInterface;

class PaymentConfirmationLookup implements PaymentConfirmationLookupInterface
{
    public function lookup(
        RequestInterface $request,
        $scopeCode = null,
        $scopeType = ScopeInterface::SCOPE_STORE
    ): string {

        /**
         * @todo: Make a request to Payment Confirmation API in order
         * to check and save details from payment
         */

        return json_encode([
            'authCode' => 'XYZABC123',
            'orderDate' => '11-11-2021 09:00:00',
            'paymentStatus' => 'confirmed',
            'paymentTotal' => '100.00',
            'paymentDetails' => [
                'id' => '0000000023',
                'type' => 'CREDITCARD',
                'amount' => '100',
                'status' => 'complete',
                'currency' => 'USD',
                'cardLastFour' => '1111',
                'cardType' => 'Visa'
            ],
            'additionalInfo' => [
                '4111052212380192'
            ]
        ]);
    }
}
