<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Service;

use Magento\Framework\HTTP\ClientInterface;
use Magento\Store\Model\ScopeInterface;
use PayYourWay\Pyw\Api\PaymentConfirmationLookupInterface;
use PayYourWay\Pyw\Api\RequestInterface;
use PayYourWay\Pyw\Api\ConfigInterface;

class PaymentConfirmationLookup implements PaymentConfirmationLookupInterface
{
    private ClientInterface $curl;
    private ConfigInterface $config;

    public function __construct(
        ClientInterface $curl,
        ConfigInterface $config
    ) {
        $this->curl = $curl;
        $this->config = $config;
    }

    public function lookup(
        RequestInterface $request
    ): string {
        $this->curl->setHeaders($request->getHeaders());
        $this->curl->get($this->config->getPaymentConfirmationApiEndpoint());

        return $this->curl->getBody();
    }
}
