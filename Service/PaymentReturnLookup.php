<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Service;

use Magento\Framework\HTTP\ClientInterface;
use PayYourWay\Pyw\Api\PaymentReturnLookupInterface;
use PayYourWay\Pyw\Api\PaymentReturnRequestInterface;
use PayYourWay\Pyw\Api\ConfigInterface;

class PaymentReturnLookup implements PaymentReturnLookupInterface
{
    private ClientInterface $curl;
    private ConfigInterface $config;
    private const RETURN_URL = 'https://payment.uat.telluride.shopyourway.com/tell/api/payment/v1/return';

    public function __construct(
        ClientInterface $curl,
        ConfigInterface $config
    ) {
        $this->curl = $curl;
        $this->config = $config;
    }

    public function lookup(
        PaymentReturnRequestInterface $request
    ): string {
        $this->curl->setHeaders($request->getHeaders());
        $this->curl->post(self::RETURN_URL, json_encode($request->getBody()));

        return $this->curl->getBody();
    }
}
