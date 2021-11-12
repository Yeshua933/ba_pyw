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

        $paymentConfirmationUrl = $this->config->getEnvironment() === Environment::ENVIRONMENT_SANDBOX ?
            self::PAYMENT_CONFIRMATION_URL_UAT : self::PAYMENT_CONFIRMATION_URL_PROD;

        $paymentConfirmationUrl .= "tell/api/checkout/v1/orderpaymentconfirm";

        $headers = [
            "Accept" => "application/json",
            "channel" => $request->getChannel(),
            "merchantId" => $request->getMerchantId(),
            "pywid" => $request->getPywid(),
            "transactionId" => $request->getTransactionId(),
            "actionType" => $request->getActionType(),
            "transactionType" => $request->getTransactionType(),
            "refid" => $request->getRefId()
        ];

        $this->curl->setHeaders($headers);
        $this->curl->get($paymentConfirmationUrl);

        return $this->curl->getBody();
    }
}
