<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Service;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\ScopeInterface;
use PayYourWay\Pyw\Api\PaymentConfirmationLookupInterface;
use PayYourWay\Pyw\Api\RequestInterface;
use PayYourWay\Pyw\Model\Adminhtml\Source\Environment;
use PayYourWay\Pyw\Model\Config;

class PaymentConfirmationLookup implements PaymentConfirmationLookupInterface
{
    private const PAYMENT_CONFIRMATION_URL_UAT = 'https://checkout.uat.telluride.shopyourway.com/';
    private const PAYMENT_CONFIRMATION_URL_PROD = 'https://checkout.telluride.shopyourway.com/';

    private Curl $curl;
    private Config $config;

    public function __construct(
        Curl $curl,
        Config $config
    ) {
        $this->curl = $curl;
        $this->config = $config;
    }

    public function lookup(
        RequestInterface $request,
        $scopeCode = null,
        $scopeType = ScopeInterface::SCOPE_STORE
    ): string {

        /**
         * @todo: Check whether production mode is enabled or not
         */
        $paymentConfirmationUrl = $this->config->getEnvironment() === Environment::ENVIRONMENT_SANDBOX ?
            self::PAYMENT_CONFIRMATION_URL_UAT : self::PAYMENT_CONFIRMATION_URL_PROD;

        $paymentConfirmationUrl = self::PAYMENT_CONFIRMATION_URL_UAT;

        $this->curl->get($paymentConfirmationUrl);

        $headers = [
            "Content-Type" => "application/json",
            "channel" => $request->getChannel(),
            "merchantId" => $request->getMerchantId(),
            "pywid" => $request->getMerchantId(),
            "transactionId" => $request->getTransactionId(),
            "actionType" => $request->getActionType(),
            "transactionType" => $request->getTransactionType(),
            "refid" => $request->getRefId()
        ];

        $this->curl->setHeaders($headers);

        return $this->curl->getBody();

        /*
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
        */
    }
}
